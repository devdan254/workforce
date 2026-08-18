<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Reuses the shared Appointment table entirely, per the spec's explicit
 * instruction ("Reuse Student Appointment architecture"). Unlike Interviews
 * (which only shows candidate-tied interview appointments), this page shows
 * EVERYTHING — matching Student/Job Seeker's own "one Appointments page,
 * all types together" pattern. Two genuinely different appointment shapes
 * exist for Employer, per AppointmentPolicy::view():
 *   1. Direct — booked WITH the employer (Recruitment Consultation,
 *      Onboarding), student_id is their own ID.
 *   2. Transitive — an interview tied to one of their candidates,
 *      student_id is the CANDIDATE's ID.
 * View + Join only — Reschedule/Cancel stay deferred, same reasoning as
 * every other permission-gated action in the Employer Portal so far.
 */
class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user();
        $jobPostingIds = $employer->jobPostings()->pluck('id');

        // Wrapped in a single where() closure so the OR stays correctly
        // scoped even if more conditions are added to this query later —
        // an unwrapped top-level orWhereHas() would otherwise widen the
        // whole query, not just this one condition.
        $appointments = Appointment::where(function ($query) use ($employer, $jobPostingIds) {
            $query->where('student_id', $employer->id)
                ->orWhereHas('jobApplication', fn ($q) => $q->whereIn('job_posting_id', $jobPostingIds));
        })
            ->with(['jobApplication.jobPosting', 'jobApplication.jobSeeker', 'staff'])
            ->orderByDesc('scheduled_at')
            ->get();

        $upcoming = $appointments->filter(fn ($a) => $a->scheduled_at->isFuture() && in_array($a->status, ['requested', 'confirmed']))
            ->sortBy('scheduled_at');

        $past = $appointments->filter(fn ($a) => $a->scheduled_at->isPast() || in_array($a->status, ['completed', 'cancelled']));

        return view('employer.appointments.index', [
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }
}
