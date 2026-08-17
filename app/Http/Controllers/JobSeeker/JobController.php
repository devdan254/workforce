<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $query = JobPosting::open()->with('category');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        }
        if ($request->filled('country')) {
            $query->where('country', $request->string('country'));
        }
        if ($request->filled('job_category_id')) {
            $query->where('job_category_id', $request->integer('job_category_id'));
        }
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->string('employment_type'));
        }
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $jobs = $query->latest()->paginate(9)->withQueryString();

        return view('job-seeker.jobs.index', [
            'jobs' => $jobs,
            'recommendedJobs' => $this->recommendedJobs($request->user()),
            'categories' => JobCategory::orderBy('name')->get(),
            'countries' => JobPosting::open()->distinct()->orderBy('country')->pluck('country'),
        ]);
    }

    public function show(JobPosting $jobPosting): View
    {
        abort_unless($jobPosting->status === 'open', 404);

        $jobPosting->load('category');

        $alreadyApplied = auth()->user()->jobApplications()->where('job_posting_id', $jobPosting->id)->exists();

        return view('job-seeker.jobs.show', [
            'jobPosting' => $jobPosting,
            'alreadyApplied' => $alreadyApplied,
        ]);
    }

    /**
     * Deliberately simple, explainable scoring — not a real matching
     * algorithm. Per the spec: "implement a sensible profile-based matching
     * structure that can be improved later," not build a recommendation
     * engine now. Degrades gracefully to "most recent" for a job seeker
     * with no profile yet, rather than showing nothing.
     */
    private function recommendedJobs(User $jobSeeker): Collection
    {
        $profile = $jobSeeker->jobSeekerProfile;
        $openJobs = JobPosting::open()->with('category')->latest()->get();

        if (! $profile) {
            return $openJobs->take(3)->values();
        }

        return $openJobs->map(function ($job) use ($profile) {
            $score = 60; // baseline — every open job starts as a plausible match

            if ($profile->preferred_countries && in_array($job->country, $profile->preferred_countries)) {
                $score += 25;
            }
            if ($profile->industry && $job->category && str_contains(
                strtolower($job->category->name), strtolower($profile->industry)
            )) {
                $score += 15;
            }

            $job->match_score = min($score, 96);

            return $job;
        })->sortByDesc('match_score')->take(3)->values();
    }
}
