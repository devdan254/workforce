<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Once a candidate accepts an offer, the employer should see: Employees /
 * Hired Candidates" — scoped to that exact set of statuses (offer accepted
 * through fully deployed), reusing the same JobApplication data everything
 * else in the Employer Portal already reads. Deliberately no separate
 * "worker profile" page — View links to the existing Candidate Profile
 * (employer.candidates.show), which is the same person, same record; a
 * second near-duplicate page would be exactly the kind of thing this whole
 * build has avoided.
 */
class WorkerController extends Controller
{
    private const HIRED_STATUSES = [
        'offer_accepted', 'documentation', 'visa_processing',
        'travel_preparation', 'deployment_ready', 'deployed',
    ];

    public function index(Request $request): View
    {
        $employerJobPostings = $request->user()->jobPostings()->orderBy('title')->get();
        $jobPostingIds = $employerJobPostings->pluck('id');

        $query = JobApplication::whereIn('job_posting_id', $jobPostingIds)
            ->whereHas('currentStatus', fn ($q) => $q->whereIn('slug', self::HIRED_STATUSES))
            ->with(['jobSeeker.jobSeekerProfile', 'jobPosting', 'currentStatus', 'offer', 'visaApplication.currentStatus']);

        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->integer('job_posting_id'));
        }

        $workers = $query->latest('applied_at')->paginate(15)->withQueryString();

        return view('employer.workers.index', [
            'workers' => $workers,
            'jobPostings' => $employerJobPostings,
        ]);
    }
}
