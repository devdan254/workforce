<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RequestJobSeekerDocumentRequest;
use App\Http\Requests\Admin\UpdateJobSeekerProfileRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Invoice;
use App\Models\JobOffer;
use App\Models\JobSeekerProfile;
use App\Models\Note;
use App\Models\Payment;
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

/**
 * Deliberately mirrors StudentWorkspaceController's shape and reasoning —
 * same "one page, tab-structured, everything loaded in one request"
 * principle, same tab-consolidation logic (Job Offers/Visa fold into the
 * Applications tab rather than being separate top-level tabs, same as
 * Admission/Universities did for Student).
 *
 * THIS DELIVERY covers show() (Overview + Personal Info tab data) plus the
 * Document/Payment action methods — Applications tab actions (status
 * changes, interview scheduling, offer management) and
 * Appointments/Support/Tasks/Notes/Activity follow as their own deliveries,
 * same incremental approach used throughout Stage 2.
 *
 * previewDocument() is NOT duplicated here — StudentWorkspaceController's
 * version is already fully generic (just checks DocumentPolicy::view, no
 * student-specific logic at all), so the existing admin.documents.preview
 * route is reused directly for Job Seekers too.
 */
class JobSeekerWorkspaceController extends Controller
{
    public function show(User $jobSeeker): View
    {
        $this->authorize('viewJobSeeker', $jobSeeker);
        abort_unless($jobSeeker->hasRole('job_seeker'), 404);

        $jobSeeker->load('jobSeekerProfile.experiences', 'jobSeekerProfile.educations');

        $applications = $jobSeeker->jobApplications()
            ->with([
                'jobPosting.category', 'currentStatus', 'assignedOfficer',
                'offer',
                'visaApplication.currentStatus', 'visaApplication.statusHistories.toStatus', 'visaApplication.statusHistories.changedBy',
                'documents.category', 'documents.verifiedBy',
                'statusHistories.toStatus', 'statusHistories.fromStatus', 'statusHistories.changedBy',
            ])
            ->latest('applied_at')
            ->get();

        $primaryApplication = $applications->first();

        $documentStats = [
            'completed' => $jobSeeker->documents()->whereIn('status', ['uploaded', 'under_review', 'verified'])->count(),
            'total' => $jobSeeker->documents()->count(),
        ];

        $financials = Invoice::financialSummaryForPerson($jobSeeker->id);

        $openTasks = $this->taskQuery($jobSeeker)?->where('status', '!=', 'completed')->count() ?? 0;

        $tickets = $jobSeeker->supportTickets()->with(['messages.author', 'assignedTo'])->latest()->get();
        $unreadMessages = $tickets->filter(fn ($t) => ! in_array($t->status, ['resolved', 'closed']) && $t->messages->last()?->user_id === $jobSeeker->id)->count();
        $openTickets = $tickets->whereNotIn('status', ['resolved', 'closed'])->count();

        $appointments = $jobSeeker->appointments()->with(['staff', 'jobApplication.jobPosting'])->orderByDesc('scheduled_at')->get();
        $nextAppointment = $appointments->first(fn ($a) => $a->scheduled_at->isFuture() && in_array($a->status, ['requested', 'confirmed']));

        $documentsAwaitingReview = $jobSeeker->documents()->where('status', 'under_review')->with('category')->get();
        $pendingPayments = $jobSeeker->payments()->where('status', 'pending')->with(['invoice', 'transactions'])->get();

        $allDocuments = $jobSeeker->documents()->with(['category', 'jobApplication.jobPosting', 'verifiedBy'])->get()
            ->groupBy(fn ($d) => $d->category->name ?? 'Other Documents');

        $allPayments = $jobSeeker->payments()->with(['invoice', 'transactions', 'confirmedBy'])->latest()->get();
        $allInvoices = $jobSeeker->invoices()->with('items')->latest()->get();

        $notifications = $jobSeeker->notifications()->latest()->take(30)->get();

        $tasks = $this->taskQuery($jobSeeker)?->with(['assignedTo', 'createdBy'])->latest()->get() ?? collect();
        $notes = $this->noteQuery($jobSeeker)?->with('createdBy')->latest()->get() ?? collect();

        $offersCount = JobOffer::whereHas('jobApplication', fn ($q) => $q->where('job_seeker_id', $jobSeeker->id))->count();

        $officers = User::whereHas('roles', fn ($q) => $q->whereNotIn('name', ['student', 'job_seeker']))->orderBy('name')->get();
        $categories = DocumentCategory::orderBy('name')->get();

        $activities = $this->activityForJobSeeker($jobSeeker, $applications, $allDocuments->flatten(), $allPayments);

        return view('admin.job-seekers.show', [
            'jobSeeker' => $jobSeeker,
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
            'offersCount' => $offersCount,
            'officers' => $officers,
            'categories' => $categories,
            'activities' => $activities,
        ]);
    }

    public function updateProfile(UpdateJobSeekerProfileRequest $request, User $jobSeeker): RedirectResponse
    {
        if ($request->filled('phone')) {
            $jobSeeker->update(['phone' => $request->string('phone')]);
        }

        $profile = $jobSeeker->jobSeekerProfile()->updateOrCreate(
            ['user_id' => $jobSeeker->id],
            [
                'date_of_birth' => $request->input('date_of_birth'),
                'gender' => $request->input('gender'),
                'nationality' => $request->input('nationality'),
                'address_line' => $request->input('address_line'),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'professional_title' => $request->input('professional_title'),
                'industry' => $request->input('industry'),
                'skills' => $request->input('skills'),
                'languages' => $request->input('languages'),
                'certifications' => $request->input('certifications'),
                'passport_number' => $request->input('passport_number'),
                'passport_issue_date' => $request->input('passport_issue_date'),
                'passport_expiry_date' => $request->input('passport_expiry_date'),
                'passport_country' => $request->input('passport_country'),
                'expected_salary' => $request->input('expected_salary'),
                'expected_salary_currency' => $request->input('expected_salary_currency'),
                'earliest_availability' => $request->input('earliest_availability'),
                // Explicit boolean() rather than raw input — an unchecked checkbox
                // doesn't submit at all, so relying on input() would silently leave
                // the OLD value in place instead of actually setting it to false.
                'willing_to_relocate' => $request->boolean('willing_to_relocate'),
                'worked_abroad_before' => $request->boolean('worked_abroad_before'),
            ]
        );

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Profile updated.');
    }

    /**
     * Admin adding a work experience entry on the candidate's behalf — same
     * capability the candidate already has on their own Profile page
     * (JobSeeker\ProfileController::storeExperience), just reachable from
     * the workspace's Personal Information tab instead. Same authorization
     * bar as editing the rest of the profile (editJobSeekerProfile).
     */
    public function storeExperience(Request $request, User $jobSeeker): RedirectResponse
    {
        $this->authorize('editJobSeekerProfile', $jobSeeker);

        $request->validate([
            'occupation' => ['required', 'string', 'max:150'],
            'employer' => ['nullable', 'string', 'max:150'],
            'years_of_experience' => ['nullable', 'numeric', 'min:0', 'max:60'],
        ]);

        $profile = $jobSeeker->jobSeekerProfile ?? $jobSeeker->jobSeekerProfile()->create([]);

        $profile->experiences()->create([
            'occupation' => $request->string('occupation'),
            'employer' => $request->input('employer'),
            'years_of_experience' => $request->input('years_of_experience'),
            'sort_order' => $profile->experiences()->count(),
        ]);

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Work experience added.');
    }

    public function destroyExperience(User $jobSeeker, \App\Models\JobExperience $experience): RedirectResponse
    {
        $this->authorize('editJobSeekerProfile', $jobSeeker);
        abort_unless($experience->profile->user_id === $jobSeeker->id, 404);

        $experience->delete();

        return back()->with('success', 'Work experience removed.');
    }

    /**
     * Same pattern as storeExperience()/destroyExperience() above, for
     * Education — Admin routinely does the full onboarding on a candidate's
     * behalf (in person, over WhatsApp, etc.), not just account creation.
     */
    public function storeEducation(Request $request, User $jobSeeker): RedirectResponse
    {
        $this->authorize('editJobSeekerProfile', $jobSeeker);

        $request->validate([
            'level' => ['required', 'in:primary,secondary,diploma,bachelor,master,not_applicable'],
            'institution' => ['nullable', 'string', 'max:150'],
            'course' => ['nullable', 'string', 'max:150'],
            'graduation_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 10)],
        ]);

        $profile = $jobSeeker->jobSeekerProfile ?? $jobSeeker->jobSeekerProfile()->create([]);

        $profile->educations()->create([
            'level' => $request->string('level'),
            'institution' => $request->input('institution'),
            'course' => $request->input('course'),
            'graduation_year' => $request->input('graduation_year'),
            'sort_order' => $profile->educations()->count(),
        ]);

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Education added.');
    }

    public function destroyEducation(User $jobSeeker, \App\Models\JobEducation $education): RedirectResponse
    {
        $this->authorize('editJobSeekerProfile', $jobSeeker);
        abort_unless($education->profile->user_id === $jobSeeker->id, 404);

        $education->delete();

        return back()->with('success', 'Education removed.');
    }

    public function verifyDocument(Request $request, User $jobSeeker, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('verify', $document);
        abort_unless($document->student_id === $jobSeeker->id, 404);

        $service->verify($document, $request->user(), $request->input('notes'));

        return back()->with('success', "\"{$document->name}\" verified.");
    }

    public function rejectDocument(Request $request, User $jobSeeker, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('reject', $document);
        abort_unless($document->student_id === $jobSeeker->id, 404);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $service->reject($document, $request->user(), $request->string('reason'));

        return back()->with('success', "\"{$document->name}\" rejected — the candidate will see your reason and can re-upload.");
    }

    public function requestDocument(RequestJobSeekerDocumentRequest $request, User $jobSeeker): RedirectResponse
    {
        abort_unless($jobSeeker->hasRole('job_seeker'), 404);

        $document = Document::create([
            'student_id' => $jobSeeker->id,
            'job_application_id' => $request->input('job_application_id'),
            'document_category_id' => $request->integer('document_category_id'),
            'name' => $request->string('name'),
            'status' => 'required',
        ]);

        $jobSeeker->notify(new DocumentRequestedNotification($document));

        return back()->with('success', "\"{$document->name}\" requested from {$jobSeeker->name}.");
    }

    public function uploadDocument(Request $request, User $jobSeeker, Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('upload', $document);
        abort_unless($document->student_id === $jobSeeker->id, 404);

        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png']]);

        $path = $request->file('file')->store("documents/{$jobSeeker->id}", 'public');

        $service->markUploaded(
            $document,
            $path,
            $request->file('file')->getClientMimeType(),
            $request->file('file')->getSize(),
        );

        return back()->with('success', "\"{$document->name}\" uploaded on behalf of {$jobSeeker->name}.");
    }

    public function updateDocument(Request $request, User $jobSeeker, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);
        abort_unless($document->student_id === $jobSeeker->id, 404);

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

    public function deleteDocument(User $jobSeeker, Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);
        abort_unless($document->student_id === $jobSeeker->id, 404);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    public function confirmPayment(Request $request, User $jobSeeker, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('confirm', $payment);
        abort_unless($payment->student_id === $jobSeeker->id, 404);

        $service->confirmPayment($payment, $request->user());

        return back()->with('success', 'Payment confirmed — the invoice balance has been updated and the candidate notified.');
    }

    private function taskQuery(User $jobSeeker)
    {
        return $jobSeeker->jobSeekerProfile
            ? Task::where('taskable_type', JobSeekerProfile::class)->where('taskable_id', $jobSeeker->jobSeekerProfile->id)
            : null;
    }

    private function noteQuery(User $jobSeeker)
    {
        return $jobSeeker->jobSeekerProfile
            ? Note::where('noteable_type', JobSeekerProfile::class)->where('noteable_id', $jobSeeker->jobSeekerProfile->id)
            : null;
    }

    private function activityForJobSeeker(User $jobSeeker, $applications, $documents, $payments)
    {
        $visaIds = $applications->pluck('visaApplication.id')->filter()->values();

        return Activity::query()
            ->where(function ($query) use ($jobSeeker, $applications, $documents, $payments, $visaIds) {
                $query->where(fn ($q) => $q->where('subject_type', User::class)->where('subject_id', $jobSeeker->id));

                if ($applications->isNotEmpty()) {
                    $query->orWhere(fn ($q) => $q->where('subject_type', \App\Models\JobApplication::class)->whereIn('subject_id', $applications->pluck('id')));
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
