<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\Payment;
use App\Models\StudyApplication;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\VisaApplication;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Every number here is a live query — none of this is hardcoded, per the
     * spec's explicit rule for the Admin dashboard, same as the Student one.
     */
    public function index(): View
    {
        $stats = [
            'total_students' => User::role('student')->count(),

            // "Active" = not yet in a terminal status (completed/rejected).
            'active_applications' => StudyApplication::whereHas(
                'currentStatus', fn ($q) => $q->where('is_terminal', false)
            )->count(),

            'documents_awaiting_review' => Document::where('status', 'under_review')->count(),

            'pending_payments' => Payment::where('status', 'pending')->count(),

            'visa_applications' => VisaApplication::whereHas(
                'currentStatus', fn ($q) => $q->where('is_terminal', false)
            )->count(),

            'upcoming_appointments' => Appointment::where('scheduled_at', '>=', now())
                ->whereIn('status', ['requested', 'confirmed'])
                ->count(),

            'open_tickets' => SupportTicket::whereNotIn('status', ['resolved', 'closed'])->count(),
        ];

        $recentStudents = User::role('student')
            ->with('studentProfile')
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = SupportTicket::whereNotIn('status', ['resolved', 'closed'])
            ->with('student')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentStudents' => $recentStudents,
            'recentTickets' => $recentTickets,
        ]);
    }
}
