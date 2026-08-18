<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only, deliberately — per the spec, "the employer should not have a
 * Publish button," and Edit/Close actions depend on per-employer permission
 * grants that don't exist yet (deferred until Candidates/Documents exist to
 * gate, same reasoning noted in the Phase B Step 1 delivery). This is
 * strictly View + View Details for now.
 */
class JobController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', JobPosting::class);

        $postings = $request->user()->jobPostings()
            ->with('category')
            ->latest()
            ->paginate(10);

        // Per-row recruitment counts — same aggregation shape as the
        // Dashboard's totals, just computed per posting instead of summed.
        $postings->getCollection()->transform(function (JobPosting $posting) {
            $posting->applications_total = $posting->applications()->count();
            $posting->shortlisted_total = $posting->applications()
                ->whereHas('currentStatus', fn ($q) => $q->where('slug', 'shortlisted'))
                ->count();

            return $posting;
        });

        return view('employer.jobs.index', ['postings' => $postings]);
    }

    public function show(JobPosting $jobPosting): View
    {
        $this->authorize('view', $jobPosting);

        $jobPosting->load('category');

        $applications = $jobPosting->applications();

        $stats = [
            'applications' => (clone $applications)->count(),
            'shortlisted' => (clone $applications)->whereHas('currentStatus', fn ($q) => $q->where('slug', 'shortlisted'))->count(),
            'interviews' => (clone $applications)->whereHas('currentStatus', fn ($q) => $q->whereIn('slug', ['interview_scheduled', 'interview_completed']))->count(),
            'offers' => JobOffer::whereHas('jobApplication', fn ($q) => $q->where('job_posting_id', $jobPosting->id))->count(),
            'hired' => (clone $applications)->whereHas('currentStatus', fn ($q) => $q->where('slug', 'deployed'))->count(),
        ];

        return view('employer.jobs.show', ['posting' => $jobPosting, 'stats' => $stats]);
    }
}
