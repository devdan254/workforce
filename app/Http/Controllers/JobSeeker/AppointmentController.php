<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Deliberately near-identical to Student\AppointmentController — same
 * Appointment model, same reschedule-resets-to-requested behavior, same
 * Policy (AppointmentPolicy already recognizes isJobSeeker() alongside
 * isStudent()). No new scheduling infrastructure, per the spec's explicit
 * instruction not to duplicate it.
 */
class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $appointments = $request->user()->appointments()
            ->with(['staff', 'jobApplication.jobPosting'])
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        return view('job-seeker.appointments.index', ['appointments' => $appointments]);
    }

    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $request->validate(['scheduled_at' => ['required', 'date', 'after:now']]);

        $appointment->update([
            'scheduled_at' => $request->date('scheduled_at'),
            'status' => 'requested', // needs re-confirmation, same as Student's own reschedule
        ]);

        return back()->with('success', 'Appointment rescheduled — awaiting confirmation.');
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }
}
