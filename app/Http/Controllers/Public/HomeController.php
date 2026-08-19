<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\StudyPosting;
use Illuminate\View\View;

/**
 * The public marketing Home page. "Featured Opportunities" reads live from
 * the exact same JobPosting/StudyPosting catalog the Admin manages — no
 * separate content system, no hand-maintained homepage list. A posting
 * marked Featured and Open shows up here automatically; nothing else needs
 * touching when Admin changes what's featured.
 */
class HomeController extends Controller
{
    public function index(): View
    {
        $featuredJobs = JobPosting::with('category')
            ->featured()
            ->open()
            ->latest()
            ->take(3)
            ->get();

        $featuredStudyPostings = StudyPosting::featured()
            ->open()
            ->latest()
            ->take(3)
            ->get();

        return view('public.home', [
            'featuredJobs' => $featuredJobs,
            'featuredStudyPostings' => $featuredStudyPostings,
        ]);
    }
}
