<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RequestVisaDocumentRequest;
use App\Http\Requests\Admin\StoreNoteRequest;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Http\Requests\Admin\StoreVisaApplicationRequest;
use App\Http\Requests\Admin\StoreVisaInvoiceRequest;
use App\Http\Requests\Admin\UpdatePaymentRequest;
use App\Http\Requests\Admin\UpdateVisaApplicationRequest;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobApplication;
use App\Models\Note;
use App\Models\Payment;
use App\Models\Status;
use App\Models\StatusTransition;
use App\Models\StudyApplication;
use App\Models\User;
use App\Models\VisaApplication;
use App\Notifications\DocumentRequestedNotification;
use App\Notifications\InvoiceSentNotification;
use App\Services\ApplicationStatusService;
use App\Services\DocumentVerificationService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * The ONE central Visa Management module — deliberately not split per
 * applicant type. Every method here works identically regardless of
 * whether a given VisaApplication is Student-linked, Job-Seeker-linked, or
 * standalone; VisaApplication::applicantType()/owner() (Stage 1) already
 * carry that distinction, so this controller never needs its own
 * conditional branching for "which kind of visa is this" beyond what's
 * genuinely different (e.g. only a standalone applicant's personal fields
 * are editable here — a linked applicant's identity lives on their own
 * profile).
 *
 * Reused without modification: VisaApplicationPolicy (already handles all
 * three paths — see its own docblock), ApplicationStatusService (already
 * documented as supporting VisaApplication), StoreNoteRequest, the Note
 * model's polymorphism. This is the second Stage of a build that's
 * explicitly staged — Documents/Payments/Invoices sections show as
 * "coming in a later delivery" placeholders here, not fully wired yet.
 */
class VisaManagementController extends Controller
{
    private const APPLICANT_TYPE_FILTERS = ['student', 'job_seeker', 'guest'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', VisaApplication::class);

        $query = VisaApplication::with([
            'studyApplication.student', 'jobApplication.jobSeeker', 'user', 'currentStatus',
        ]);

        if ($request->filled('applicant_type') && in_array($request->input('applicant_type'), self::APPLICANT_TYPE_FILTERS, true)) {
            match ($request->input('applicant_type')) {
                'student' => $query->whereNotNull('study_application_id'),
                'job_seeker' => $query->whereNotNull('job_application_id'),
                'guest' => $query->whereNull('study_application_id')->whereNull('job_application_id'),
            };
        }

        if ($request->filled('visa_type')) {
            $query->where('visa_type', $request->string('visa_type'));
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->integer('status_id'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('passport_number', 'like', "%{$search}%")
                    ->orWhere('destination_country', 'like', "%{$search}%")
                    ->orWhereHas('studyApplication.student', fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('jobApplication.jobSeeker', fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $visaApplications = $query->latest()->paginate(15)->withQueryString();

        return view('admin.visa-management.index', [
            'visaApplications' => $visaApplications,
            'statuses' => Status::where('type', 'visa')->orderBy('sort_order')->get(),
            'visaTypes' => VisaApplication::whereNotNull('visa_type')->distinct()->orderBy('visa_type')->pluck('visa_type'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', VisaApplication::class);

        return view('admin.visa-management.create', [
            'students' => User::role('student')->orderBy('name')->get(),
            'jobSeekers' => User::role('job_seeker')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreVisaApplicationRequest $request): RedirectResponse
    {
        $initialStatusId = StatusTransition::where('status_type', 'visa')->whereNull('from_status_id')->value('to_status_id');

        $baseData = [
            'destination_country' => $request->string('destination_country'),
            'visa_type' => $request->input('visa_type'),
            'purpose_of_travel' => $request->input('purpose_of_travel'),
            'status_id' => $initialStatusId,
        ];

        if ($request->input('applicant_source') === 'student') {
            $student = User::findOrFail($request->integer('user_id'));
            abort_unless($student->isStudent(), 422, 'Selected user is not a Student.');

            $application = StudyApplication::where('student_id', $student->id)->latest()->first();
            abort_if(is_null($application), 422, 'This student has no study application to attach a visa to yet.');
            abort_if($application->visaApplication, 422, 'This student\'s study application already has a visa application.');

            $visaApplication = VisaApplication::create($baseData + ['study_application_id' => $application->id]);
        } elseif ($request->input('applicant_source') === 'job_seeker') {
            $jobSeeker = User::findOrFail($request->integer('user_id'));
            abort_unless($jobSeeker->isJobSeeker(), 422, 'Selected user is not a Job Seeker.');

            $application = JobApplication::where('job_seeker_id', $jobSeeker->id)->latest()->first();
            abort_if(is_null($application), 422, 'This job seeker has no job application to attach a visa to yet.');
            abort_if($application->visaApplication, 422, 'This job seeker\'s job application already has a visa application.');

            $visaApplication = VisaApplication::create($baseData + ['job_application_id' => $application->id]);
        } else {
            // Standalone/guest — same "give them a real User row immediately"
            // pattern as the public wizard (Public\VisaApplicationController
            // never created one; this is the manual-entry equivalent),
            // so Documents/Payments/Invoices have somewhere real to attach
            // once those stages exist, with zero special-casing later.
            $guest = User::create([
                'name' => trim($request->string('first_name').' '.$request->string('last_name')),
                'email' => $request->string('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make(Str::random(32)),
                'is_active' => true,
            ]);
            $guest->assignRole('visa_applicant');

            $visaApplication = VisaApplication::create($baseData + [
                'user_id' => $guest->id,
                'first_name' => $request->string('first_name'),
                'last_name' => $request->string('last_name'),
                'date_of_birth' => $request->input('date_of_birth'),
                'nationality' => $request->input('nationality'),
                'passport_number' => $request->input('passport_number'),
            ]);
        }

        return redirect()->route('admin.visa-management.show', $visaApplication)->with('success', 'Visa application created.');
    }

    public function show(VisaApplication $visaApplication): View
    {
        $this->authorize('view', $visaApplication);

        $visaApplication->load([
            'studyApplication.student', 'jobApplication.jobSeeker', 'user',
            'currentStatus', 'statusHistories.changedBy', 'statusHistories.fromStatus', 'statusHistories.toStatus',
        ]);

        $notes = Note::where('noteable_type', VisaApplication::class)
            ->where('noteable_id', $visaApplication->id)
            ->with('createdBy')
            ->latest()
            ->get();

        $service = app(ApplicationStatusService::class);

        $documents = $visaApplication->documentsQuery()->with('category')->get();

        $invoices = $visaApplication->invoicesQuery()->with(['items', 'payments' => fn ($q) => $q->latest()])->latest()->get();
        $payments = Payment::whereHas('invoice', fn ($q) => $q->where('visa_application_id', $visaApplication->id))
            ->with(['invoice', 'confirmedBy'])
            ->latest()
            ->get();

        return view('admin.visa-management.show', [
            'visaApplication' => $visaApplication,
            'notes' => $notes,
            'allowedNextStatuses' => $service->allowedNextStatuses($visaApplication),
            'documents' => $documents,
            'documentCategories' => \App\Models\DocumentCategory::orderBy('name')->get(),
            'invoices' => $invoices,
            'payments' => $payments,
        ]);
    }

    public function edit(VisaApplication $visaApplication): View
    {
        $this->authorize('update', $visaApplication);

        return view('admin.visa-management.edit', ['visaApplication' => $visaApplication]);
    }

    public function update(UpdateVisaApplicationRequest $request, VisaApplication $visaApplication): RedirectResponse
    {
        $data = $request->safe()->only([
            'destination_country', 'visa_type', 'purpose_of_travel', 'expected_travel_date',
            'duration_of_stay', 'embassy_appointment_at', 'previously_applied', 'previously_refused',
            'refusal_explanation', 'travelled_internationally', 'countries_visited', 'additional_info',
        ]);

        // Personal/passport fields only actually apply to a standalone
        // applicant — see this request class's own docblock for why a
        // linked Student/Job Seeker's identity isn't overwritten here.
        if ($visaApplication->applicantType() === 'guest') {
            $data += $request->safe()->only([
                'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender',
                'nationality', 'country_of_residence', 'passport_number', 'passport_expiry',
            ]);
        }

        $visaApplication->update($data);

        return back()->with('success', 'Visa application updated.');
    }

    public function updateStatus(Request $request, VisaApplication $visaApplication, ApplicationStatusService $service): RedirectResponse
    {
        $this->authorize('update', $visaApplication);

        $request->validate([
            'to_status_id' => ['required', 'exists:statuses,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $service->transition($visaApplication, $request->integer('to_status_id'), $request->user(), $request->input('note'));
        } catch (InvalidStatusTransitionException $e) {
            return back()->withErrors(['to_status_id' => 'That status change is not allowed from the current stage.']);
        }

        return back()->with('success', 'Visa status updated.');
    }

    public function storeNote(StoreNoteRequest $request, VisaApplication $visaApplication): RedirectResponse
    {
        $this->authorize('update', $visaApplication);

        Note::create([
            'noteable_type' => VisaApplication::class,
            'noteable_id' => $visaApplication->id,
            'body' => $request->string('body'),
            'is_internal' => $request->boolean('is_internal', true),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Note added.');
    }

    /**
     * Stage 5 — Documents. Mirrors Admin\EmployerWorkspaceController's
     * document methods exactly (same six actions, same
     * DocumentVerificationService calls, same DocumentPolicy checks) —
     * the only genuinely new piece is WHICH fields a new Document gets on
     * creation, which has to branch by applicant type so a Student/Job-
     * Seeker-linked visa's documents land in the SAME application-scoped
     * vault their own Workspace Documents tab already reads (see
     * VisaApplication::documentsQuery() for the read side of this same
     * logic — keep both in sync if this ever changes).
     */
    private function newDocumentOwnerFields(VisaApplication $visaApplication): array
    {
        return match ($visaApplication->applicantType()) {
            'student' => [
                'student_id' => $visaApplication->studyApplication->student_id,
                'study_application_id' => $visaApplication->study_application_id,
            ],
            'job_seeker' => [
                'student_id' => $visaApplication->jobApplication->job_seeker_id,
                'job_application_id' => $visaApplication->job_application_id,
            ],
            default => [
                'student_id' => $visaApplication->user_id,
            ],
        };
    }

    public function requestDocument(RequestVisaDocumentRequest $request, VisaApplication $visaApplication): RedirectResponse
    {
        $this->authorize('update', $visaApplication);

        $document = Document::create($this->newDocumentOwnerFields($visaApplication) + [
            'document_category_id' => $request->integer('document_category_id'),
            'name' => $request->string('name'),
            'status' => 'required',
        ]);

        $visaApplication->owner()?->notify(new DocumentRequestedNotification($document));

        return back()->with('success', "\"{$document->name}\" requested.");
    }

    public function uploadDocument(Request $request, VisaApplication $visaApplication, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('upload', $document);
        abort_unless($visaApplication->documentsQuery()->whereKey($document->id)->exists(), 404);

        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png']]);

        $path = $request->file('file')->store("documents/{$document->student_id}", 'public');

        $service->markUploaded($document, $path, $request->file('file')->getClientMimeType(), $request->file('file')->getSize());

        return back()->with('success', "\"{$document->name}\" uploaded.");
    }

    public function verifyDocument(Request $request, VisaApplication $visaApplication, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('verify', $document);
        abort_unless($visaApplication->documentsQuery()->whereKey($document->id)->exists(), 404);

        $service->verify($document, $request->user(), $request->input('notes'));

        return back()->with('success', "\"{$document->name}\" verified.");
    }

    public function rejectDocument(Request $request, VisaApplication $visaApplication, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('reject', $document);
        abort_unless($visaApplication->documentsQuery()->whereKey($document->id)->exists(), 404);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $service->reject($document, $request->user(), $request->string('reason'));

        return back()->with('success', "\"{$document->name}\" rejected.");
    }

    public function updateDocument(Request $request, VisaApplication $visaApplication, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);
        abort_unless($visaApplication->documentsQuery()->whereKey($document->id)->exists(), 404);

        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'document_category_id' => ['required', 'exists:document_categories,id'],
        ]);

        $document->update([
            'name' => $request->string('name'),
            'document_category_id' => $request->integer('document_category_id'),
        ]);

        return back()->with('success', 'Document updated.');
    }

    public function deleteDocument(VisaApplication $visaApplication, Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        abort_unless($visaApplication->documentsQuery()->whereKey($document->id)->exists(), 404);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    /**
     * Stage 6 — Payments & Invoices. Mirrors Admin\Employer\FinanceController
     * and Admin\JobSeeker\FinanceController almost exactly — same
     * PaymentService calls, same StorePaymentRequest/UpdatePaymentRequest
     * reused unmodified (already fully generic). Only StoreVisaInvoiceRequest
     * needed a variant, and only because there's no job_application_id-style
     * field to accept here — every invoice created through this controller
     * is implicitly tagged with THIS visa_application_id, not optionally.
     *
     * The one genuinely new wrinkle: a standalone/guest applicant has no
     * portal at all, so InvoiceSentNotification's in-app notification would
     * be created for someone who could never see it (no dashboard, no
     * notification bell to view it in) — see below, skipped deliberately
     * for that path rather than silently creating an unreachable row.
     */
    public function storePayment(StorePaymentRequest $request, VisaApplication $visaApplication, Invoice $invoice, PaymentService $service): RedirectResponse
    {
        abort_unless($invoice->visa_application_id === $visaApplication->id, 404);

        $payment = $service->recordPayment($invoice, (float) $request->input('amount'), $request->string('method'));

        if ($request->filled('reference')) {
            $payment->transactions()->create([
                'gateway' => 'manual',
                'gateway_reference' => $request->string('reference'),
                'status' => $request->boolean('confirm_immediately') ? 'confirmed' : 'pending',
            ]);
        }

        if ($request->boolean('confirm_immediately')) {
            $service->confirmPayment($payment, $request->user());
        }

        return back()->with('success', 'Payment recorded'.($request->boolean('confirm_immediately') ? ' and confirmed.' : ' as pending confirmation.'));
    }

    public function confirmPayment(Request $request, VisaApplication $visaApplication, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('confirm', $payment);
        abort_unless($payment->invoice?->visa_application_id === $visaApplication->id, 404);

        $service->confirmPayment($payment, $request->user());

        return back()->with('success', 'Payment confirmed — the invoice balance has been updated.');
    }

    public function updatePayment(UpdatePaymentRequest $request, VisaApplication $visaApplication, Payment $payment): RedirectResponse
    {
        abort_unless($payment->invoice?->visa_application_id === $visaApplication->id, 404);

        $payment->update([
            'amount' => $request->input('amount'),
            'method' => $request->string('method'),
        ]);

        return back()->with('success', 'Payment updated.');
    }

    public function refundPayment(Request $request, VisaApplication $visaApplication, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('refund', $payment);
        abort_unless($payment->invoice?->visa_application_id === $visaApplication->id, 404);

        $service->refundPayment($payment);

        return back()->with('success', 'Payment refunded — the invoice balance has been adjusted.');
    }

    public function storeInvoice(StoreVisaInvoiceRequest $request, VisaApplication $visaApplication): RedirectResponse
    {
        $owner = $visaApplication->owner();
        abort_if(is_null($owner), 422, 'This visa application has no owning account to bill yet.');

        $invoice = DB::transaction(function () use ($request, $visaApplication, $owner) {
            $items = collect($request->input('items'))->map(fn ($item) => [
                'description' => $item['description'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'line_total' => (int) $item['quantity'] * (float) $item['unit_price'],
            ]);

            $subtotal = $items->sum('line_total');

            $invoice = Invoice::create([
                'student_id' => $owner->id,
                'visa_application_id' => $visaApplication->id,
                'description' => $request->string('description'),
                'currency' => $request->string('currency'),
                'subtotal' => $subtotal,
                'tax' => 0,
                'total' => $subtotal,
                'status' => 'draft',
                'due_date' => $request->input('due_date'),
            ]);

            foreach ($items as $item) {
                InvoiceItem::create($item + ['invoice_id' => $invoice->id]);
            }

            return $invoice;
        });

        return back()->with('success', "Invoice {$invoice->invoice_number} created as a draft. Send it when ready.");
    }

    public function sendInvoice(Request $request, VisaApplication $visaApplication, Invoice $invoice): RedirectResponse
    {
        $this->authorize('send', $invoice);
        abort_unless($invoice->visa_application_id === $visaApplication->id, 404);

        $invoice->update(['status' => 'sent', 'sent_at' => now()]);

        // Standalone/guest applicants have no portal — see this method's
        // class-level docblock. A Student or Job Seeker's own dashboard
        // genuinely shows this notification; a guest has nowhere to see
        // it, so creating one for them would just be a dead database row.
        if ($visaApplication->applicantType() !== 'guest') {
            $visaApplication->owner()?->notify(new InvoiceSentNotification($invoice));
        }

        return back()->with('success', 'Invoice sent.');
    }

    public function cancelInvoice(Request $request, VisaApplication $visaApplication, Invoice $invoice): RedirectResponse
    {
        $this->authorize('cancel', $invoice);
        abort_unless($invoice->visa_application_id === $visaApplication->id, 404);

        $invoice->update(['status' => 'cancelled']);

        return back()->with('success', 'Invoice cancelled.');
    }
}
