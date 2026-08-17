<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Services\DocumentVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Filter tabs match the spec's example exactly: All / Active / Interview /
     * Offers / Completed / Not Selected. "Active" is deliberately the broad
     * catch-all (submitted through pre-interview stages) since Interview and
     * Offers are already their own distinct tabs, not a subset of Active.
     */
    private const FILTER_STATUSES = [
        'active' => ['application_submitted', 'under_review', 'documents_required', 'documents_verified', 'shortlisted', 'employer_review', 'documentation', 'visa_processing', 'travel_preparation', 'deployment_ready'],
        'interview' => ['interview_scheduled', 'interview_completed'],
        'offers' => ['selected', 'offer_extended', 'offer_accepted'],
        'completed' => ['deployed'],
        'not_selected' => ['rejected', 'withdrawn'],
    ];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', JobApplication::class);

        $query = $request->user()->jobApplications()->with(['jobPosting.category', 'currentStatus']);

        $filter = $request->string('filter', 'all');
        if ($filter !== 'all' && isset(self::FILTER_STATUSES[$filter->value()])) {
            $query->whereHas('currentStatus', fn ($q) => $q->whereIn('slug', self::FILTER_STATUSES[$filter->value()]));
        }

        $applications = $query->latest('applied_at')->paginate(10)->withQueryString();

        return view('job-seeker.applications.index', [
            'applications' => $applications,
            'activeFilter' => $filter->value(),
        ]);
    }

    /**
     * The Job Seeker equivalent of Student's Application Details workspace —
     * same DB-driven timeline pattern, same "only show what's relevant to
     * this person" principle. Offer/Visa sections show a summary only for
     * now (no dedicated pages yet); accept/decline actions land with the
     * dedicated Job Offers page next.
     */
    public function show(JobApplication $application): View
    {
        $this->authorize('view', $application);

        $application->load([
            'jobPosting.category',
            'currentStatus',
            'assignedOfficer',
            'statusHistories.toStatus', 'statusHistories.fromStatus',
            'documents.category', 'documents.verifiedBy',
            'offer',
            'visaApplication.currentStatus',
            'appointments' => fn ($q) => $q->orderByDesc('scheduled_at'),
        ]);

        $nextStatuses = app(\App\Services\ApplicationStatusService::class)->allowedNextStatuses($application);

        $interview = $application->appointments->firstWhere('type', 'Interview')
            ?? $application->appointments->first(fn ($a) => str_contains(strtolower($a->type), 'interview'));

        return view('job-seeker.applications.show', [
            'application' => $application,
            'nextStatuses' => $nextStatuses,
            'interview' => $interview,
        ]);
    }

    /**
     * Upload/replace a document tied to THIS specific application — same
     * pattern as Student's per-application document handling, reusing
     * DocumentVerificationService unmodified.
     */
    public function uploadDocument(Request $request, JobApplication $application, \App\Models\Document $document, DocumentVerificationService $service): RedirectResponse
    {
        $this->authorize('view', $application);
        $this->authorize('upload', $document);
        abort_unless($document->job_application_id === $application->id, 404);

        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png']]);

        $file = $request->file('file');
        $path = $file->store('documents/'.auth()->id(), 'public');

        $service->markUploaded($document, $path, $file->getClientMimeType(), $file->getSize());

        return back()->with('success', "\"{$document->name}\" uploaded — awaiting review.");
    }
}
