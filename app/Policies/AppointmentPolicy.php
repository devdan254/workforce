<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isStudent() || $user->isJobSeeker()) {
            return $appointment->student_id === $user->id;
        }

        return $user->can('appointments.view')
            && ($appointment->staff_id === $user->id || $user->hasAnyRole(['super_admin', 'admin_officer']));
    }

    public function create(User $user): bool
    {
        return $user->isStudent() || $user->isJobSeeker() || $user->can('appointments.create');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isStudent() || $user->isJobSeeker()) {
            // Owners may reschedule/cancel their own upcoming appointments only.
            return $appointment->student_id === $user->id && $appointment->status !== 'completed';
        }

        return $user->can('appointments.update');
    }
}
