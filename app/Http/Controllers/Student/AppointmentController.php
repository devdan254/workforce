<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\RescheduleAppointmentRequest;
use App\Http\Requests\Student\StoreAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $appointments = $request->user()->appointments()
            ->with(['staff', 'studyApplication.university'])
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        return view('student.appointments.index', ['appointments' => $appointments]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Appointment::class);

        return view('student.appointments.create', [
            'applications' => $request->user()->studyApplications()->with('university')->get(),
        ]);
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        Appointment::create([
            'student_id' => $request->user()->id,
            'study_application_id' => $request->integer('study_application_id') ?: null,
            'type' => $request->string('type'),
            'mode' => $request->string('mode'),
            'scheduled_at' => $request->date('scheduled_at'),
            'status' => 'requested',
            'notes' => $request->string('notes'),
        ]);

        return redirect()
            ->route('student.appointments.index')
            ->with('success', "Appointment requested — we'll confirm shortly.");
    }

    /**
     * Rescheduling resets status back to "requested" — a confirmed slot with
     * the old time is no longer valid once the time itself changes, and staff
     * need to actively re-confirm the new one rather than it silently staying
     * "confirmed" against a date nobody agreed to.
     */
    public function reschedule(RescheduleAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update([
            'scheduled_at' => $request->date('scheduled_at'),
            'status' => 'requested',
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
