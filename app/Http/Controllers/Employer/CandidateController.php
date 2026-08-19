<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Shortlisted Candidates" from the spec is deliberately NOT a separate
 * page/controller — it's the exact same data as Candidates, just filtered.
 * A dedicated route (?filter=shortlisted) reuses this index() entirely
 * rather than duplicating the query/view for what's the same underlying list.
 *
 * job_posting_id is a second, combinable filter dimension — an employer
 * with several open roles needs to narrow to "candidates for THIS job",
 * independent of (and stackable with) the status filter.
 *
 * Documents are now conditionally loaded — only when Admin has granted
 * employer_documents.view_candidate (DocumentPolicy::view() enforces this
 * independently regardless of what this controller decides to load, so
 * there's no way to bypass it even if this check were somehow skipped).
 */
class CandidateController extends Controller
{
    private const FILTER_STATUSES = [
        'shortlisted' => ['shortlisted'],
        'interview' => ['interview_scheduled', 'interview_completed'],
        'offers' => ['selected', 'offer_extended', 'offer_accepted'],
        'hired' => ['deployed'],
    ];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', JobApplication::class);

        $employerJobPostings = $request->user()->jobPostings()->orderBy('title')->get();
        $jobPostingIds = $employerJobPostings->pluck('id');

        $query = JobApplication::whereIn('job_posting_id', $jobPostingIds)
            ->with(['jobSeeker.jobSeekerProfile.experiences', 'jobPosting', 'currentStatus']);

        $filter = $request->string('filter', 'all')->value();
        if (isset(self::FILTER_STATUSES[$filter])) {
            $query->whereHas('currentStatus', fn ($q) => $q->whereIn('slug', self::FILTER_STATUSES[$filter]));
        }

        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->integer('job_posting_id'));
        }

        $candidates = $query->latest('applied_at')->paginate(15)->withQueryString();

        return view('employer.candidates.index', [
            'candidates' => $candidates,
            'activeFilter' => $filter,
            'jobPostings' => $employerJobPostings,
        ]);
    }

    public function show(JobApplication $application): View
    {
        $this->authorize('view', $application);

        $application->load([
            'jobSeeker.jobSeekerProfile.experiences',
            'jobSeeker.jobSeekerProfile.educations',
            'jobPosting',
            'currentStatus',
            'statusHistories.toStatus',
        ]);

        $canViewDocuments = auth()->user()->can('employer_documents.view_candidate');

        $documents = $canViewDocuments
            ? $application->jobSeeker->documents()->with('category')->get()
            : collect();

        return view('employer.candidates.show', [
            'application' => $application,
            'canViewDocuments' => $canViewDocuments,
            'documents' => $documents,
        ]);
    }
}
