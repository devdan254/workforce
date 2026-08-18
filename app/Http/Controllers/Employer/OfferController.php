<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * View + Download only this delivery — Upload/Replace/Approve/Request
 * Changes are all explicitly "if Admin has granted permission" per the
 * spec, same deferred-permission-grant pattern as Documents (Candidates
 * step) and Reschedule/Cancel (Interviews step). Offers themselves are
 * still created/managed entirely by Admin (Admin\JobSeeker\
 * ApplicationController::storeOrUpdateOffer/sendOffer) — nothing here
 * duplicates that.
 */
class OfferController extends Controller
{
    public function index(Request $request): View
    {
        $employerJobPostings = $request->user()->jobPostings()->orderBy('title')->get();
        $jobPostingIds = $employerJobPostings->pluck('id');

        $query = JobOffer::whereHas(
            'jobApplication', fn ($q) => $q->whereIn('job_posting_id', $jobPostingIds)
        )->with(['jobApplication.jobPosting', 'jobApplication.jobSeeker']);

        if ($request->filled('job_posting_id')) {
            $query->whereHas('jobApplication', fn ($q) => $q->where('job_posting_id', $request->integer('job_posting_id')));
        }

        $offers = $query->latest()->paginate(15)->withQueryString();

        return view('employer.offers.index', [
            'offers' => $offers,
            'jobPostings' => $employerJobPostings,
        ]);
    }

    public function show(JobOffer $offer): View
    {
        $this->authorize('view', $offer);

        $offer->load(['jobApplication.jobPosting', 'jobApplication.jobSeeker.jobSeekerProfile', 'offerLetterDocument']);

        return view('employer.offers.show', ['offer' => $offer]);
    }
}
