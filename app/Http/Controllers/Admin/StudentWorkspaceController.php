<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RequestDocumentRequest;
use App\Http\Requests\Admin\UpdateStudentProfileRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Invoice;
use App\Models\Note;
use App\Models\Payment;
use App\Models\StudentProfile;
use App\Models\Task;
use App\Models\User;
use App\Models\VisaApplication;
use App\Notifications\DocumentRequestedNotification;
use App\Services\DocumentVerificationService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentWorkspaceController extends Controller
{
    /**
     * The Unified Student Management Workspace. Per the spec: one page,
     * tab-structured, NOT a collection of disconnected pages. Every tab's
     * data is loaded here in one request — this controller is intentionally
     * the "heaviest" one in the app because the spec explicitly wants ONE
     * page, not eleven separate ones each re-querying the student.
     *
     * Tab consolidation note: the spec lists "Universities" and "Admission"
     * as separate top-level tabs. Architecturally, Universities is a catalog
     * (not student-owned data) and Admission is 1:1 with a single
     * StudyApplication — neither makes sense as its own student-wide tab,
     * so both are folded into the Applications tab.
     */
    public function show(User $student): View
    {
        $this->authorize('view', $student);
        abort_unless($student->hasRole('student'), 404);

        $student->load('studentProfile');

        $applications = $student->studyApplications()
            ->with([
                'university', 'course', 'currentStatus', 'assignedOfficer',
                'admission.offerLetterDocument',
                'visaApplication.currentStatus', 'visaApplication.statusHistories.toStatus', 'visaApplication.statusHistories.changedBy',
                'documents.category', 'documents.verifiedBy',
                'statusHistories.toStatus', 'statusHistories.fromStatus', 'statusHistories.changedBy',
            ])
            ->latest('submitted_at')
            ->get();

        $primaryApplication = $applications->first();

        $documentStats = [
            'completed' => $student->documents()->whereIn('status', ['uploaded', 'under_review', 'verified'])->count(),
            'total' => $student->documents()->count(),
        ];

        $financials = Invoice::financialSummaryForStudent($student->id);

        $openTasks = $this->taskQuery($student)?->where('status', '!=', 'completed')->count() ?? 0;

        // "Unread Messages" = open/in-progress tickets where the student's message
        // is the last one in the thread — i.e. staff owes a reply. There's no
        // read-tracking column on support_messages, so this is the honest proxy.
        $tickets = $student->supportTickets()->with(['messages.author', 'assignedTo'])->latest()->get();
        $unreadMessages = $tickets->filter(fn ($t) => ! in_array($t->status, ['resolved', 'closed']) && $t->messages->last()?->user_id === $student->id)->count();
        $openTickets = $tickets->whereNotIn('status', ['resolved', 'closed'])->count();

        $appointments = $student->appointments()->with(['staff', 'studyApplication.university'])->orderByDesc('scheduled_at')->get();
        $nextAppointment = $appointments->first(fn ($a) => $a->scheduled_at->isFuture() && in_array($a->status, ['requested', 'confirmed']));

        // Actionable widgets on Overview — verify/reject/confirm without leaving the page.
        $documentsAwaitingReview = $student->documents()->where('status', 'under_review')->with('category')->get();
        $pendingPayments = $student->payments()->where('status', 'pending')->with(['invoice', 'transactions'])->get();

        $allDocuments = $student->documents()->with(['category', 'studyApplication.university', 'verifiedBy'])->get()
            ->groupBy(fn ($d) => $d->category->name ?? 'Other Documents');

        $allPayments = $student->payments()->with(['invoice', 'transactions', 'confirmedBy'])->latest()->get();
        $allInvoices = $student->invoices()->with('items')->latest()->get();

        $notifications = $student->notifications()->latest()->take(30)->get();

        $tasks = $this->taskQuery($student)?->with(['assignedTo', 'createdBy'])->latest()->get() ?? collect();
        $notes = $this->noteQuery($student)?->with('createdBy')->latest()->get() ?? collect();

        $officers = User::whereHas('roles', fn ($q) => $q->whereNotIn('name', ['student']))->orderBy('name')->get();
        $categories = DocumentCategory::orderBy('name')->get();

        $activities = $this->activityForStudent($student, $applications, $allDocuments->flatten(), $allPayments);

        return view('admin.students.show', [
            'student' => $student,
            'applications' => $applications,
            'primaryApplication' => $primaryApplication,
            'documentStats' => $documentStats,
            'financials' => $financials,
            'openTasks' => $openTasks,
            'unreadMessages' => $unreadMessages,
            'openTickets' => $openTickets,
            'nextAppointment' => $nextAppointment,
            'documentsAwaitingReview' => $documentsAwaitingReview,
            'pendingPayments' => $pendingPayments,
            'allDocuments' => $allDocuments,
            'allPayments' => $allPayments,
            'allInvoices' => $allInvoices,
            'appointments' => $appointments,
            'tickets' => $tickets,
            'notifications' => $notifications,
            'tasks' => $tasks,
            'notes' => $notes,
            'officers' => $officers,
            'categories' => $categories,
            'activities' => $activities,
        ]);
    }

    /**
     * Admin editing a student's Personal/Academic/Passport info on their
     * behalf — same StudentProfile fields the student can edit themselves
     * at /student/profile, just reachable from the workspace's Personal
     * Information tab instead. Recalculates profile_completion_percent
     * exactly like the student-side save does, so the two paths never
     * leave that number out of sync.
     */
    public function updateProfile(UpdateStudentProfileRequest $request, User $student): RedirectResponse
    {
        if ($request->filled('phone')) {
            $student->update(['phone' => $request->string('phone')]);
        }

        $profile = $student->studentProfile()->updateOrCreate(
            ['user_id' => $student->id],
            $request->safe()->except('phone')
        );

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', "{$student->name}'s profile updated.");
    }

    /**
     * Inline browser preview (not a forced download) — this is what the
     * Overview tab's "View File" now links to instead of the student-scoped
     * download route, which 403'd for staff because it sat behind
     * role:student middleware. This route sits under the admin group,
     * authorized by DocumentPolicy::view (staff branch: documents.view).
     */
    public function previewDocument(Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        abort_unless($document->file_path && Storage::disk('public')->exists($document->file_path), 404, 'File not available.');

        return Storage::disk('public')->response($document->file_path, $document->name);
    }

    public function verifyDocument(Request $request, User $student, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('verify', $document);
        abort_unless($document->student_id === $student->id, 404);

        $service->verify($document, $request->user(), $request->input('notes'));

        return back()->with('success', "\"{$document->name}\" verified.");
    }

    public function rejectDocument(Request $request, User $student, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('reject', $document);
        abort_unless($document->student_id === $student->id, 404);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $service->reject($document, $request->user(), $request->string('reason'));

        return back()->with('success', "\"{$document->name}\" rejected — the student will see your reason and can re-upload.");
    }

    /**
     * Admin REQUESTING a document — the inverse of a student's own
     * "Add Document" self-service flow. Creates a `required` row with no
     * file, optionally tied to a specific application (via
     * study_application_id) or general/vault-level if left blank. Either
     * way it's the same `documents` table — no duplicate concept for
     * "requested" vs "self-added" documents.
     */
    public function requestDocument(RequestDocumentRequest $request, User $student): RedirectResponse
    {
        abort_unless($student->hasRole('student'), 404);

        $document = Document::create([
            'student_id' => $student->id,
            'study_application_id' => $request->input('study_application_id'),
            'document_category_id' => $request->integer('document_category_id'),
            'name' => $request->string('name'),
            'status' => 'required',
        ]);

        $student->notify(new DocumentRequestedNotification($document));

        return back()->with('success', "\"{$document->name}\" requested from {$student->name}.");
    }

    /**
     * Admin/staff uploading a FILE directly onto an existing document row —
     * e.g. they received it by email or in person and are logging it into
     * the system on the student's behalf. Uses the same DocumentVerificationService
     * as every other upload path (student self-upload, replace-rejected) —
     * one place a file transitions a document to under_review, always.
     */
    public function uploadDocument(Request $request, User $student, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('upload', $document);
        abort_unless($document->student_id === $student->id, 404);

        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png']]);

        $path = $request->file('file')->store("documents/{$student->id}", 'public');

        $service->markUploaded(
            $document,
            $path,
            $request->file('file')->getClientMimeType(),
            $request->file('file')->getSize(),
        );

        return back()->with('success', "\"{$document->name}\" uploaded on behalf of {$student->name}.");
    }

    /**
     * Metadata edit — rename or re-categorize. Does NOT touch the file or
     * status; that's upload()/verify()/reject()'s job respectively.
     */
    public function updateDocument(Request $request, User $student, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);
        abort_unless($document->student_id === $student->id, 404);

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

    public function deleteDocument(User $student, Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        abort_unless($document->student_id === $student->id, 404);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    public function confirmPayment(Request $request, User $student, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('confirm', $payment);
        abort_unless($payment->student_id === $student->id, 404);

        $service->confirmPayment($payment, $request->user());

        return back()->with('success', 'Payment confirmed — the invoice balance has been updated and the student notified.');
    }

    private function taskQuery(User $student)
    {
        return $student->studentProfile
            ? Task::where('taskable_type', StudentProfile::class)->where('taskable_id', $student->studentProfile->id)
            : null;
    }

    private function noteQuery(User $student)
    {
        return $student->studentProfile
            ? Note::where('noteable_type', StudentProfile::class)->where('noteable_id', $student->studentProfile->id)
            : null;
    }

    /**
     * Activity tab — aggregates spatie/activitylog entries across every
     * model type that touches this student (the User row itself, each of
     * their applications, documents, and payments). Not stored anywhere
     * specifically for this tab — computed from the same activity_log table
     * every LogsActivity-enabled model already writes to automatically.
     */
    private function activityForStudent(User $student, $applications, $documents, $payments)
    {
        $visaIds = $applications->pluck('visaApplication.id')->filter()->values();

        return Activity::query()
            ->where(function ($query) use ($student, $applications, $documents, $payments, $visaIds) {
                $query->where(fn ($q) => $q->where('subject_type', User::class)->where('subject_id', $student->id));

                if ($applications->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', \App\Models\StudyApplication::class)->whereIn('subject_id', $applications->pluck('id')));
                }
                if ($documents->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', Document::class)->whereIn('subject_id', $documents->pluck('id')));
                }
                if ($payments->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', Payment::class)->whereIn('subject_id', $payments->pluck('id')));
                }
                if ($visaIds->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', VisaApplication::class)->whereIn('subject_id', $visaIds));
                }
            })
            ->with('causer')
            ->latest()
            ->take(50)
            ->get();
    }
}
