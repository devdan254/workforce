<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Replaces the static site's client-side "job filter demo" (literally
 * labeled that in main.js — a JS show/hide over 8 hardcoded cards) with a
 * real server-side search against the live JobPosting catalog. Category
 * and country options are pulled from actual data, not a hand-maintained
 * dropdown list that drifts out of sync with what's really posted.
 */
class JobsController extends Controller
{
    public function index(Request $request): View
    {
        $query = JobPosting::with('category')->open();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        if ($request->filled('country')) {
            $query->where('country', $request->string('country'));
        }

        $jobs = $query->latest()->paginate(8)->withQueryString();

        return view('public.jobs.index', [
            'jobs' => $jobs,
            'categories' => JobCategory::orderBy('name')->get(),
            'countries' => JobPosting::open()->distinct()->orderBy('country')->pluck('country'),
            'totalOpen' => JobPosting::open()->count(),
        ]);
    }

    /**
     * Public detail page — 404s on anything not genuinely open, so a
     * draft/closed/archived posting can't be reached by guessing its URL.
     * Similar Opportunities is the same category, excluding this posting,
     * real data — not the static page's 3 hardcoded unrelated cards.
     */
    public function show(JobPosting $jobPosting): View
    {
        abort_unless($jobPosting->status === 'open', 404);

        $jobPosting->load('category');

        $similarJobs = JobPosting::with('category')
            ->open()
            ->where('job_category_id', $jobPosting->job_category_id)
            ->where('id', '!=', $jobPosting->id)
            ->latest()
            ->take(3)
            ->get();

        return view('public.jobs.show', [
            'job' => $jobPosting,
            'similarJobs' => $similarJobs,
        ]);
    }
}
