<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Employer's relationship to an appointment is TWO different things,
     * not one:
     *   1. Direct — an appointment booked WITH the employer themselves
     *      (e.g. "Recruitment Consultation"), where student_id genuinely
     *      is their own ID, same as Student/Job Seeker's ownership check.
     *   2. Transitive — an interview tied to one of their candidates,
     *      where student_id is the CANDIDATE's ID, not theirs; ownership
     *      only makes sense through job_application -> job_posting ->
     *      employer_id.
     * The original Stage 3 fix only handled case 2 (interviews), which
     * silently broke case 1 — a direct employer appointment has no
     * job_application_id at all, so the transitive check alone would
     * always deny it. Both are checked now.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isStudent() || $user->isJobSeeker()) {
            return $appointment->student_id === $user->id;
        }

        if ($user->isEmployer()) {
            return $appointment->student_id === $user->id
                || $appointment->jobApplication?->jobPosting?->employer_id === $user->id;
        }

        return $user->can('appointments.view')
            && ($appointment->staff_id === $user->id || $user->hasAnyRole(['super_admin', 'admin_officer']));
    }

    /**
     * Employer deliberately excluded — they don't book their own
     * appointments (this branch is for a candidate self-booking a
     * consultation); Employer appointments (interviews or direct
     * consultations) are scheduled BY Admin, not created by the employer
     * via this ownership path.
     */
    public function create(User $user): bool
    {
        return $user->isStudent() || $user->isJobSeeker() || $user->can('appointments.create');
    }

    /**
     * Employer deliberately excluded here too — Reschedule/Cancel for
     * Employer are explicitly "where permitted" per the spec, and that
     * permission-grant mechanism doesn't exist yet (same deferred decision
     * as Documents). Nothing currently calls update() as an employer, so
     * this omission is intentional, not an oversight.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isStudent() || $user->isJobSeeker()) {
            // Owners may reschedule/cancel their own upcoming appointments only.
            return $appointment->student_id === $user->id && $appointment->status !== 'completed';
        }

        return $user->can('appointments.update');
    }
}
