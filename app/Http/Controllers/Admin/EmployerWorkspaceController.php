<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RequestEmployerDocumentRequest;
use App\Http\Requests\Admin\StoreEmployerAppointmentRequest;
use App\Http\Requests\Admin\StoreNoteRequest;
use App\Http\Requests\Admin\StoreSupportReplyRequest;
use App\Http\Requests\Admin\StoreSupportTicketRequest;
use App\Http\Requests\Admin\UpdateAppointmentRequest;
use App\Http\Requests\Admin\UpdateEmployerProfileRequest;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\EmployerProfile;
use App\Models\Invoice;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobOffer;
use App\Models\Note;
use App\Models\Payment;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\DocumentRequestedNotification;
use App\Services\DocumentVerificationService;
use App\Services\PaymentService;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Worker Requests/Jobs/Candidates tabs (previous delivery) reuse
 * Admin\WorkerRequestController and Admin\JobPostingController's actions
 * entirely. Documents (this delivery) is different — it needs its OWN
 * action methods here, mirroring JobSeekerWorkspaceController's document
 * methods exactly (verify/reject/request/upload/update/delete), since
 * there's no separate "Admin\Employer\DocumentController" the way there's
 * no separate one for Job Seeker either — this workspace controller IS
 * where that logic lives, same precedent already established.
 */
class EmployerWorkspaceController extends Controller
{
    private const DOCUMENT_STATUS_FILTERS = ['required', 'under_review', 'verified', 'rejected'];

    public function show(User $employer, Request $request): View
    {
        $this->authorize('viewEmployer', $employer);
        abort_unless($employer->hasRole('employer'), 404);

        $employer->load('employerProfile.assignedOfficer');

        $jobPostingIds = $employer->jobPostings()->pluck('id');
        $applications = JobApplication::whereIn('job_posting_id', $jobPostingIds);

        $activeJobs = $employer->jobPostings()->where('status', 'open')->count();
        $totalJobs = $employer->jobPostings()->count();
        $totalApplicants = (clone $applications)->count();
        $shortlisted = (clone $applications)->whereHas('currentStatus', fn ($q) => $q->where('slug', 'shortlisted'))->count();
        $hired = (clone $applications)->whereHas('currentStatus', fn ($q) => $q->where('slug', 'deployed'))->count();

        $financials = Invoice::financialSummaryForPerson($employer->id);

        $recentWorkerRequests = $employer->workerRequests()->latest()->take(5)->get();
        $recentJobPostings = $employer->jobPostings()->with('category')->withCount('applications')->latest()->take(5)->get();

        $officers = User::whereHas('roles', fn ($q) => $q->whereNotIn('name', ['student', 'job_seeker', 'employer']))
            ->orderBy('name')
            ->get();

        $allWorkerRequests = $employer->workerRequests()->latest()->get();

        $allJobPostings = $employer->jobPostings()
            ->with('category')
            ->withCount('applications')
            ->latest()
            ->get()
            ->map(function ($posting) {
                $posting->shortlisted_count = $posting->applications()
                    ->whereHas('currentStatus', fn ($q) => $q->where('slug', 'shortlisted'))
                    ->count();

                return $posting;
            });

        $allCandidates = JobApplication::whereIn('job_posting_id', $jobPostingIds)
            ->with(['jobSeeker.jobSeekerProfile', 'jobPosting', 'currentStatus'])
            ->latest('applied_at')
            ->get();

        $categories = JobCategory::orderBy('name')->get();

        // Documents tab data — same Status + Job filter pattern as the
        // employer-facing Documents page, reachable via ?doc_status= and
        // ?doc_job_posting_id= query params on this same workspace URL.
        $documentQuery = $employer->documents()->with(['category', 'jobPosting']);

        $docStatus = $request->string('doc_status')->value();
        if (in_array($docStatus, self::DOCUMENT_STATUS_FILTERS, true)) {
            $documentQuery->where('status', $docStatus);
        }
        if ($request->filled('doc_job_posting_id')) {
            $documentQuery->where('job_posting_id', $request->integer('doc_job_posting_id'));
        }

        $groupedDocuments = $documentQuery->get()->groupBy(fn ($d) => $d->category->name ?? 'Other Documents');
        $documentTotal = $employer->documents()->count();
        $documentCompleted = $employer->documents()->whereIn('status', ['under_review', 'verified'])->count();
        $documentCategories = DocumentCategory::orderBy('name')->get();

        // Payments / Invoices tab data — same shape the Employer-facing
        // pages already use (Invoice::financialSummaryForPerson), plus the
        // full lists for Admin's own record/create/send/cancel actions.
        $allPayments = $employer->payments()->with(['invoice', 'transactions', 'confirmedBy'])->latest()->get();
        $allInvoices = $employer->invoices()->with(['items', 'jobApplication.jobPosting'])->latest()->get();

        // Appointments tab data — both ownership shapes (direct + via a
        // candidate's interview), same query AppointmentPolicy checks
        // against, so Admin sees exactly what the employer themselves would.
        // Wrapped in a single where() closure so the OR stays correctly
        // scoped — an unwrapped top-level orWhereHas() would otherwise
        // incorrectly widen this whole query.
        $allAppointments = Appointment::where(function ($query) use ($employer, $jobPostingIds) {
            $query->where('student_id', $employer->id)
                ->orWhereHas('jobApplication', fn ($q) => $q->whereIn('job_posting_id', $jobPostingIds));
        })
            ->with(['jobApplication.jobPosting', 'jobApplication.jobSeeker', 'staff'])
            ->orderByDesc('scheduled_at')
            ->get();

        // Support tickets — genuine direct ownership (SupportTicketPolicy
        // already handled isEmployer() correctly since Step 1), no
        // ownership-shape complication like Appointments had.
        $tickets = $employer->supportTickets()->with(['messages.author', 'assignedTo'])->latest()->get();

        // Notes — polymorphic against the PROFILE (EmployerProfile), not
        // the User directly, same pattern Job Seeker's notes already use.
        $notes = $this->noteQuery($employer)?->with('createdBy')->latest()->get() ?? collect();

        // Activity — only User/Document/Payment/JobApplication/JobOffer
        // actually log activity (JobPosting/WorkerRequest/Invoice don't),
        // so those are the only sources this aggregates. Candidate
        // applications/offers are included deliberately — an employer
        // cares about recruitment activity happening on their behalf, not
        // just changes to their own account. Uses a fresh, UNFILTERED
        // documents query — groupedDocuments above is scoped to whatever
        // Status/Job filter is currently active on the Documents tab,
        // which would incorrectly narrow the Activity log to match.
        $allDocumentsForActivity = $employer->documents()->get();
        $activities = $this->activityForEmployer($employer, $allCandidates, $allDocumentsForActivity, $allPayments);

        return view('admin.employers.show', [
            'employer' => $employer,
            'activeJobs' => $activeJobs,
            'totalJobs' => $totalJobs,
            'totalApplicants' => $totalApplicants,
            'shortlisted' => $shortlisted,
            'hired' => $hired,
            'financials' => $financials,
            'recentWorkerRequests' => $recentWorkerRequests,
            'recentJobPostings' => $recentJobPostings,
            'officers' => $officers,
            'allWorkerRequests' => $allWorkerRequests,
            'allJobPostings' => $allJobPostings,
            'allCandidates' => $allCandidates,
            'categories' => $categories,
            'groupedDocuments' => $groupedDocuments,
            'documentTotal' => $documentTotal,
            'documentCompleted' => $documentCompleted,
            'documentCategories' => $documentCategories,
            'activeDocStatus' => in_array($docStatus, self::DOCUMENT_STATUS_FILTERS, true) ? $docStatus : '',
            'allPayments' => $allPayments,
            'allInvoices' => $allInvoices,
            'allAppointments' => $allAppointments,
            'tickets' => $tickets,
            'notes' => $notes,
            'activities' => $activities,
            'conductInterviews' => $employer->can('employer_interviews.conduct'),
            'viewCandidateDocuments' => $employer->can('employer_documents.view_candidate'),
        ]);
    }

    public function updateProfile(UpdateEmployerProfileRequest $request, User $employer): RedirectResponse
    {
        if ($request->filled('phone')) {
            $employer->update(['phone' => $request->string('phone')]);
        }

        $profile = $employer->employerProfile()->updateOrCreate(
            ['user_id' => $employer->id],
            [
                'company_name' => $request->input('company_name'),
                'industry' => $request->input('industry'),
                'country' => $request->input('country'),
                'company_website' => $request->input('company_website'),
                'company_size' => $request->input('company_size'),
                'city' => $request->input('city'),
                'company_description' => $request->input('company_description'),
                'contact_job_title' => $request->input('contact_job_title'),
                'assigned_officer_id' => $request->input('assigned_officer_id'),
            ]
        );

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Company profile updated.');
    }

    public function requestDocument(RequestEmployerDocumentRequest $request, User $employer): RedirectResponse
    {
        abort_unless($employer->hasRole('employer'), 404);

        $document = Document::create([
            'student_id' => $employer->id,
            'job_posting_id' => $request->input('job_posting_id'),
            'document_category_id' => $request->integer('document_category_id'),
            'name' => $request->string('name'),
            'status' => 'required',
        ]);

        $employer->notify(new DocumentRequestedNotification($document));

        return back()->with('success', "\"{$document->name}\" requested from {$employer->name}.");
    }

    public function verifyDocument(Request $request, User $employer, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('verify', $document);
        abort_unless($document->student_id === $employer->id, 404);

        $service->verify($document, $request->user(), $request->input('notes'));

        return back()->with('success', "\"{$document->name}\" verified.");
    }

    public function rejectDocument(Request $request, User $employer, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('reject', $document);
        abort_unless($document->student_id === $employer->id, 404);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $service->reject($document, $request->user(), $request->string('reason'));

        return back()->with('success', "\"{$document->name}\" rejected — the employer will see your reason and can re-upload.");
    }

    public function uploadDocument(Request $request, User $employer, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('upload', $document);
        abort_unless($document->student_id === $employer->id, 404);

        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png']]);

        $path = $request->file('file')->store("documents/{$employer->id}", 'public');

        $service->markUploaded(
            $document,
            $path,
            $request->file('file')->getClientMimeType(),
            $request->file('file')->getSize(),
        );

        return back()->with('success', "\"{$document->name}\" uploaded on behalf of {$employer->name}.");
    }

    public function updateDocument(Request $request, User $employer, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);
        abort_unless($document->student_id === $employer->id, 404);

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

    public function deleteDocument(User $employer, Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        abort_unless($document->student_id === $employer->id, 404);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    public function confirmPayment(Request $request, User $employer, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('confirm', $payment);
        abort_unless($payment->student_id === $employer->id, 404);

        $service->confirmPayment($payment, $request->user());

        return back()->with('success', 'Payment confirmed — the invoice balance has been updated and the employer notified.');
    }

    public function storeAppointment(StoreEmployerAppointmentRequest $request, User $employer): RedirectResponse
    {
        Appointment::create([
            'student_id' => $employer->id,
            'staff_id' => $request->input('staff_id') ?? $request->user()->id,
            'job_application_id' => $request->input('job_application_id'),
            'type' => $request->string('type'),
            'mode' => $request->string('mode'),
            'meeting_link' => $request->input('meeting_link'),
            'scheduled_at' => $request->date('scheduled_at'),
            'status' => 'confirmed',
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Appointment booked and confirmed.');
    }

    public function updateAppointment(UpdateAppointmentRequest $request, User $employer, Appointment $appointment): RedirectResponse
    {
        abort_unless($this->appointmentBelongsToEmployer($appointment, $employer), 404);

        $appointment->update([
            'type' => $request->string('type'),
            'mode' => $request->string('mode'),
            'scheduled_at' => $request->date('scheduled_at'),
            'staff_id' => $request->input('staff_id') ?? $appointment->staff_id,
        ]);

        return back()->with('success', 'Appointment updated.');
    }

    public function confirmAppointment(Request $request, User $employer, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($this->appointmentBelongsToEmployer($appointment, $employer), 404);

        $appointment->update([
            'status' => 'confirmed',
            'staff_id' => $appointment->staff_id ?? $request->user()->id,
        ]);

        return back()->with('success', 'Appointment confirmed.');
    }

    public function cancelAppointment(Request $request, User $employer, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($this->appointmentBelongsToEmployer($appointment, $employer), 404);

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }

    public function completeAppointment(Request $request, User $employer, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);
        abort_unless($this->appointmentBelongsToEmployer($appointment, $employer), 404);

        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Appointment marked completed.');
    }

    /**
     * Same dual-ownership check AppointmentPolicy::view() already uses —
     * direct (student_id is the employer's own ID, e.g. a Recruitment
     * Consultation) OR transitive (an interview tied to one of their
     * candidates, where student_id is the CANDIDATE's ID instead). The
     * four action methods above were originally checking direct ownership
     * only, which would have incorrectly 404'd every interview-type
     * appointment — caught and fixed before this shipped, not after.
     */
    private function appointmentBelongsToEmployer(Appointment $appointment, User $employer): bool
    {
        return $appointment->student_id === $employer->id
            || $appointment->jobApplication?->jobPosting?->employer_id === $employer->id;
    }

    public function storeTicket(StoreSupportTicketRequest $request, User $employer): RedirectResponse
    {
        $ticket = SupportTicket::create([
            'student_id' => $employer->id,
            'category' => $request->string('category'),
            'priority' => $request->string('priority'),
            'subject' => $request->string('subject'),
            'status' => 'waiting_for_student',
            'assigned_to' => $request->user()->id,
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('message'),
        ]);

        return back()->with('success', "Message sent to {$employer->name}.");
    }

    public function replyTicket(StoreSupportReplyRequest $request, User $employer, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->student_id === $employer->id, 404);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->string('body'),
        ]);

        if (! in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'waiting_for_student']);
        }

        return back()->with('success', 'Reply sent.');
    }

    public function updateTicketStatus(Request $request, User $employer, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->student_id === $employer->id, 404);

        $request->validate(['status' => ['required', Rule::in(['open', 'in_progress', 'waiting_for_student', 'resolved', 'closed'])]]);

        $ability = $request->input('status') === 'closed' ? 'close' : 'respond';
        $this->authorize($ability, $ticket);

        $ticket->update(['status' => $request->string('status')]);

        return back()->with('success', 'Ticket status updated.');
    }

    public function storeNote(StoreNoteRequest $request, User $employer): RedirectResponse
    {
        // Unlike Job Seeker's profile, EmployerProfile requires company_name
        // and country (NOT NULL, no defaults) — an empty create([]) would
        // throw a database error for any employer who hasn't completed
        // onboarding yet. A real scenario: Admin wants to log a note about
        // a brand-new lead before their Company Profile is filled in.
        // Placeholder values here are safe since the real Company Profile
        // form always overwrites them via updateOrCreate once completed.
        $profile = $employer->employerProfile ?? $employer->employerProfile()->create([
            'company_name' => $employer->name,
            'country' => 'Unknown',
        ]);

        Note::create([
            'noteable_type' => EmployerProfile::class,
            'noteable_id' => $profile->id,
            'body' => $request->string('body'),
            'is_internal' => $request->boolean('is_internal', true),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Note added.');
    }

    private function noteQuery(User $employer)
    {
        return $employer->employerProfile
            ? Note::where('noteable_type', EmployerProfile::class)->where('noteable_id', $employer->employerProfile->id)
            : null;
    }

    private function activityForEmployer(User $employer, $candidates, $documents, $payments)
    {
        $offerIds = JobOffer::whereHas('jobApplication', fn ($q) => $q->whereIn('id', $candidates->pluck('id')))
            ->pluck('id');

        return Activity::query()
            ->where(function ($query) use ($employer, $candidates, $documents, $payments, $offerIds) {
                $query->where(fn ($q) => $q->where('subject_type', User::class)->where('subject_id', $employer->id));

                if ($candidates->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', JobApplication::class)->whereIn('subject_id', $candidates->pluck('id')));
                }
                if ($documents->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', Document::class)->whereIn('subject_id', $documents->pluck('id')));
                }
                if ($payments->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', Payment::class)->whereIn('subject_id', $payments->pluck('id')));
                }
                if ($offerIds->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', JobOffer::class)->whereIn('subject_id', $offerIds));
                }
            })
            ->with('causer')
            ->latest()
            ->take(50)
            ->get();
    }

    /**
     * The two optional permission grants — Interviews and Candidate
     * Documents. Everything else in the Employer Portal is baseline access,
     * automatic, no grant needed; these two are the ONLY things Admin
     * decides per employer. Uses givePermissionTo/revokePermissionTo
     * individually rather than syncPermissions(), so this never wipes any
     * other direct permission an employer might hold beyond these two.
     */
    public function updatePermissions(Request $request, User $employer): RedirectResponse
    {
        $this->authorize('updateEmployer', $employer);
        abort_unless($employer->hasRole('employer'), 404);

        $request->boolean('conduct_interviews')
            ? $employer->givePermissionTo('employer_interviews.conduct')
            : $employer->revokePermissionTo('employer_interviews.conduct');

        $request->boolean('view_candidate_documents')
            ? $employer->givePermissionTo('employer_documents.view_candidate')
            : $employer->revokePermissionTo('employer_documents.view_candidate');

        return back()->with('success', 'Permissions updated.');
    }
}
