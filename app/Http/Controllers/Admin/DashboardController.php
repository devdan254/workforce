<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\JobApplication;
use App\Models\Payment;
use App\Models\StudyApplication;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\VisaApplication;
use App\Models\WorkerRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * A 360-degree overview built entirely from existing tables, models, and
 * relationships — no new business logic, no duplicate systems. Every
 * number here is a live query against data the rest of the app already
 * produces (Payments Management, Visa Management, the Student/Job Seeker/
 * Employer portals, Support Tickets, the notification bell).
 *
 * The one deliberate departure from a naive read of the spec: "Amount
 * Paid"/"Amount Pending"/"Total Payments" are grouped by currency, not
 * blindly summed across all payments regardless of currency. Admin can
 * set an arbitrary currency per-invoice from Employer's, Job Seeker's,
 * and Visa Management's own invoice forms (confirmed by reading all
 * three controllers directly before writing this) — only Student
 * invoices are hardcoded to KES. Summing amounts across different
 * currencies as one number would silently produce a meaningless total
 * the moment more than one currency is ever actually used.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', array_merge(
            $this->financialStats(),
            $this->applicationStats(),
            $this->systemStats(),
            $this->recentActivity(),
        ));
    }

    private function financialStats(): array
    {
        $amountPaidByCurrency = Payment::where('status', 'confirmed')
            ->select('currency', DB::raw('SUM(amount) as total'))
            ->groupBy('currency')
            ->pluck('total', 'currency');

        // "Unpaid, partially paid, pending" all still carry one of these
        // three statuses — an invoice's status only ever becomes 'paid'
        // once amount_paid reaches total, so a partially-paid invoice is
        // already naturally included here without any separate check.
        // Draft (never sent) and cancelled are deliberately excluded —
        // neither represents money genuinely owed yet.
        $pendingInvoicesQuery = Invoice::whereIn('status', ['sent', 'pending', 'overdue']);

        $amountPendingByCurrency = (clone $pendingInvoicesQuery)
            ->select('currency', DB::raw('SUM(total - amount_paid) as total'))
            ->groupBy('currency')
            ->pluck('total', 'currency');

        $totalByCurrency = $amountPaidByCurrency->keys()
            ->merge($amountPendingByCurrency->keys())
            ->unique()
            ->mapWithKeys(fn ($currency) => [
                $currency => (float) ($amountPaidByCurrency[$currency] ?? 0) + (float) ($amountPendingByCurrency[$currency] ?? 0),
            ]);

        return [
            'amountPaidByCurrency' => $amountPaidByCurrency,
            'amountPendingByCurrency' => $amountPendingByCurrency,
            'totalByCurrency' => $totalByCurrency,
            'pendingInvoicesCount' => (clone $pendingInvoicesQuery)->count(),
        ];
    }

    private function applicationStats(): array
    {
        return [
            'studentApplicationsCount' => StudyApplication::count(),
            'jobApplicationsCount' => JobApplication::count(),
            'workerRequestsCount' => WorkerRequest::count(),
            'visaApplicationsCount' => VisaApplication::count(),
        ];
    }

    private function systemStats(): array
    {
        // Explicit staff role list, not isStaff() — that helper also
        // returns true for a standalone/guest visa_applicant (has none of
        // student/job_seeker/employer either), which would overcount this
        // specific "how many real staff members" figure.
        $staffRoles = [
            'super_admin', 'admin_officer', 'education_officer', 'finance_officer',
            'visa_officer', 'support_officer', 'sales_officer', 'recruitment_officer',
            'hr_outsourcing_officer',
        ];

        $userBreakdown = [
            'Students' => User::role('student')->count(),
            'Employers' => User::role('employer')->count(),
            'Job Seekers' => User::role('job_seeker')->count(),
            'Admin/Staff' => User::role($staffRoles)->count(),
        ];

        // Same "classify by the owner's actual role" approach Payments
        // Management already uses for its category breakdown — student_id
        // is the generic person-reference here too (confirmed by reading
        // Employer\AppointmentController directly: an employer's own
        // appointments use student_id = their own user id, no separate
        // employer_id column exists on this table).
        $upcomingAppointmentsQuery = fn () => Appointment::where('scheduled_at', '>=', now())
            ->whereIn('status', ['requested', 'confirmed']);

        $appointmentBreakdown = [
            'Students' => (clone $upcomingAppointmentsQuery())->whereHas('student', fn ($q) => $q->role('student'))->count(),
            'Job Applicants' => (clone $upcomingAppointmentsQuery())->whereHas('student', fn ($q) => $q->role('job_seeker'))->count(),
            'Employers' => (clone $upcomingAppointmentsQuery())->whereHas('student', fn ($q) => $q->role('employer'))->count(),
        ];

        $ticketBreakdown = [
            'Open' => SupportTicket::where('status', 'open')->count(),
            'In Progress' => SupportTicket::whereIn('status', ['in_progress', 'waiting_for_student'])->count(),
            'Resolved' => SupportTicket::where('status', 'resolved')->count(),
            'Closed' => SupportTicket::where('status', 'closed')->count(),
        ];

        $notificationBreakdown = [
            'Unread' => auth()->user()->unreadNotifications->count(),
            'Read' => auth()->user()->readNotifications->count(),
        ];

        return [
            'userBreakdown' => $userBreakdown,
            'allUsersCount' => User::count(),
            'appointmentBreakdown' => $appointmentBreakdown,
            'upcomingAppointmentsCount' => array_sum($appointmentBreakdown),
            'ticketBreakdown' => $ticketBreakdown,
            'notificationBreakdown' => $notificationBreakdown,
        ];
    }

    /**
     * Four genuinely different models normalized into one shared shape so
     * they can be merged and sorted together by date — not a new table,
     * not a new "activity log," just a read-time projection.
     */
    private function recentActivity(): array
    {
        $studentApps = StudyApplication::with(['student', 'currentStatus'])->latest('submitted_at')->take(8)->get()
            ->map(fn ($app) => [
                'applicant' => $app->student?->name ?? '—',
                'type' => 'Student / Study Abroad',
                'reference' => $app->reference_number,
                'date' => $app->submitted_at,
                'status' => $app->currentStatus?->label ?? '—',
                'url' => $app->student ? route('admin.students.show', $app->student) : null,
            ]);

        $jobApps = JobApplication::with(['jobSeeker', 'currentStatus'])->latest('applied_at')->take(8)->get()
            ->map(fn ($app) => [
                'applicant' => $app->jobSeeker?->name ?? '—',
                'type' => 'Job Seeker / Job Application',
                'reference' => $app->reference_number,
                'date' => $app->applied_at,
                'status' => $app->currentStatus?->label ?? '—',
                'url' => $app->jobSeeker ? route('admin.job-seekers.show', $app->jobSeeker) : null,
            ]);

        $workerRequests = WorkerRequest::with('employer')->latest('created_at')->take(8)->get()
            ->map(fn ($wr) => [
                'applicant' => $wr->employer?->name ?? $wr->contact_name,
                'type' => 'Worker Request',
                'reference' => "WR-{$wr->id}",
                'date' => $wr->created_at,
                'status' => ucwords(str_replace('_', ' ', $wr->status)),
                'url' => route('admin.worker-requests.show', $wr),
            ]);

        $visaApps = VisaApplication::with(['currentStatus'])->latest('created_at')->take(8)->get()
            ->map(fn ($visa) => [
                'applicant' => $visa->displayName(),
                'type' => 'Visa Application',
                'reference' => "VISA-{$visa->id}",
                'date' => $visa->created_at,
                'status' => $visa->currentStatus?->label ?? '—',
                'url' => route('admin.visa-management.show', $visa),
            ]);

        $recentApplications = $studentApps->concat($jobApps)->concat($workerRequests)->concat($visaApps)
            ->sortByDesc('date')
            ->take(8)
            ->values();

        $recentPayments = Payment::with([
            'invoice.items', 'invoice.studyApplication.student', 'invoice.jobApplication.jobSeeker',
            'invoice.visaApplication', 'student', 'confirmedBy', 'transactions',
        ])
            ->latest('created_at')
            ->take(8)
            ->get()
            ->map(function (Payment $payment) {
                $payment->computed_category = match (true) {
                    ! is_null($payment->invoice?->visa_application_id) => 'Visa',
                    $payment->student?->isStudent() => 'Student',
                    $payment->student?->isJobSeeker() => 'Job Seeker',
                    $payment->student?->isEmployer() => 'Employer',
                    default => 'Other',
                };

                return $payment;
            });

        return [
            'recentApplications' => $recentApplications,
            'recentPayments' => $recentPayments,
        ];
    }
}
