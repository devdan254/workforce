<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\WorkerRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Rebuilt now that the rest of the Employer Portal actually exists —
 * originally written in Phase B Step 1 when Worker Requests/Jobs/
 * Candidates/Payments were all still TODO, hence the old "coming in future
 * deliveries" placeholder. Every widget here reads real data from features
 * that now exist; nothing hardcoded, nothing still pending.
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user();
        $jobPostingIds = $employer->jobPostings()->pluck('id');

        $applications = JobApplication::whereIn('job_posting_id', $jobPostingIds);

        $activeJobs = $employer->jobPostings()->where('status', 'open')->count();
        $totalApplications = (clone $applications)->count();
        $shortlisted = (clone $applications)->whereHas('currentStatus', fn ($q) => $q->where('slug', 'shortlisted'))->count();
        $interviews = (clone $applications)->whereHas('currentStatus', fn ($q) => $q->whereIn('slug', ['interview_scheduled', 'interview_completed']))->count();
        $offers = JobOffer::whereHas('jobApplication', fn ($q) => $q->whereIn('job_posting_id', $jobPostingIds))->count();
        $hired = (clone $applications)->whereHas('currentStatus', fn ($q) => $q->where('slug', 'deployed'))->count();

        $financials = Invoice::financialSummaryForPerson($employer->id);
        $pendingPayments = $employer->payments()->where('status', 'pending')->count();
        $overdueInvoices = $employer->invoices()->where('status', 'sent')->where('due_date', '<', now())->count();

        $recentWorkerRequests = $employer->workerRequests()->latest()->take(5)->get();

        $upcomingInterviews = JobApplication::whereIn('job_posting_id', $jobPostingIds)
            ->whereHas('currentStatus', fn ($q) => $q->where('slug', 'interview_scheduled'))
            ->count();

        $pendingOffers = JobOffer::whereHas('jobApplication', fn ($q) => $q->whereIn('job_posting_id', $jobPostingIds))
            ->where('status', 'pending')
            ->count();

        $clarificationNeeded = $employer->workerRequests()->where('status', 'clarification_required')->count();

        // Computed, not stored — a genuine "what needs my attention today"
        // list, matching the spec's Action Center. Only shows items that
        // are actually true right now; empty state handled in the view.
        $actionItems = collect([
            $shortlisted > 0 ? ['icon' => 'users', 'text' => "Review {$shortlisted} shortlisted candidate".($shortlisted > 1 ? 's' : ''), 'route' => route('employer.candidates.index', ['filter' => 'shortlisted'])] : null,
            $clarificationNeeded > 0 ? ['icon' => 'circle-question', 'text' => "Altura needs clarification on {$clarificationNeeded} worker request".($clarificationNeeded > 1 ? 's' : ''), 'route' => route('employer.worker-requests.index')] : null,
            $pendingOffers > 0 ? ['icon' => 'handshake', 'text' => "{$pendingOffers} job offer".($pendingOffers > 1 ? 's' : '')." awaiting the candidate's response", 'route' => route('employer.offers.index')] : null,
            $overdueInvoices > 0 ? ['icon' => 'triangle-exclamation', 'text' => "{$overdueInvoices} invoice".($overdueInvoices > 1 ? 's' : '')." overdue", 'route' => route('employer.invoices.index')] : null,
            $pendingPayments > 0 ? ['icon' => 'clock', 'text' => "{$pendingPayments} payment".($pendingPayments > 1 ? 's' : '')." awaiting confirmation", 'route' => route('employer.payments.index')] : null,
        ])->filter()->values();

        return view('employer.dashboard', [
            'employer' => $employer,
            'activeJobs' => $activeJobs,
            'totalApplications' => $totalApplications,
            'shortlisted' => $shortlisted,
            'interviews' => $interviews,
            'offers' => $offers,
            'hired' => $hired,
            'financials' => $financials,
            'recentWorkerRequests' => $recentWorkerRequests,
            'upcomingInterviews' => $upcomingInterviews,
            'actionItems' => $actionItems,
        ]);
    }
}
