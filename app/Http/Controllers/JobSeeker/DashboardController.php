<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mirrors Student\DashboardController's shape deliberately — same pattern:
 * "primary application" summary card, real DB-driven stat counts, no
 * hardcoded figures anywhere. Reuses Invoice::financialSummaryForPerson()
 * unmodified (built generic back in Stage 2 Step 3).
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $jobSeeker = $request->user();

        $primaryApplication = $jobSeeker->jobApplications()
            ->with(['jobPosting.category', 'currentStatus', 'offer', 'visaApplication.currentStatus'])
            ->latest('applied_at')
            ->first();

        $applicationsCount = $jobSeeker->jobApplications()
            ->whereHas('currentStatus', fn ($q) => $q->where('is_terminal', false))
            ->count();

        // "Interviews" = upcoming appointments tied to any of this job seeker's
        // applications — reuses the exact same Appointment table/relationship
        // pattern as Student, just scoped via job_application_id instead of
        // study_application_id.
        $upcomingInterviews = $jobSeeker->appointments()
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['requested', 'confirmed'])
            ->whereNotNull('job_application_id')
            ->orderBy('scheduled_at')
            ->get();

        $offersCount = \App\Models\JobOffer::whereHas(
            'jobApplication', fn ($q) => $q->where('job_seeker_id', $jobSeeker->id)
        )->where('status', 'pending')->count();

        $documentStats = [
            'uploaded' => $jobSeeker->documents()->whereIn('status', ['uploaded', 'under_review', 'verified'])->count(),
            'pending' => $jobSeeker->documents()->where('status', 'required')->count(),
        ];

        $financialSummary = Invoice::financialSummaryForPerson($jobSeeker->id);

        $notifications = $jobSeeker->notifications()->latest()->take(5)->get();

        // "My Active Applications" mini-table (spec Section 6) — Job/Country/
        // Applied/Salary/Status/Action, most recent 5.
        $recentApplications = $jobSeeker->jobApplications()
            ->with(['jobPosting', 'currentStatus'])
            ->latest('applied_at')
            ->take(5)
            ->get();

        return view('job-seeker.dashboard', [
            'jobSeeker' => $jobSeeker,
            'primaryApplication' => $primaryApplication,
            'applicationsCount' => $applicationsCount,
            'upcomingInterviews' => $upcomingInterviews,
            'offersCount' => $offersCount,
            'documentStats' => $documentStats,
            'financialSummary' => $financialSummary,
            'notifications' => $notifications,
            'recentApplications' => $recentApplications,
        ]);
    }
}