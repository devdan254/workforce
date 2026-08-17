<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

/**
 * Mirrors StudyApplicationPolicy's shape exactly — same ownership pattern
 * (job_seeker_id === $user->id), same permission names swapped for the
 * job_applications.* equivalents seeded in Stage 2's RolesAndPermissionsSeeder.
 */
class JobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isJobSeeker() || $user->can('job_applications.view');
    }

    public function view(User $user, JobApplication $application): bool
    {
        if ($user->isJobSeeker()) {
            return $application->job_seeker_id === $user->id;
        }

        return $user->can('job_applications.view');
    }

    public function create(User $user): bool
    {
        return $user->isJobSeeker() || $user->can('job_applications.create');
    }

    public function update(User $user, JobApplication $application): bool
    {
        if ($user->isJobSeeker()) {
            return $application->job_seeker_id === $user->id && is_null($application->applied_at);
        }

        return $user->can('job_applications.update');
    }

    public function changeStatus(User $user, JobApplication $application): bool
    {
        if (! $user->can('job_applications.change_status')) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'admin_officer', 'hr_outsourcing_officer'])) {
            return true;
        }

        return $application->assigned_officer_id === $user->id;
    }

    public function assignOfficer(User $user, JobApplication $application): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin_officer', 'hr_outsourcing_officer']);
    }

    public function delete(User $user, JobApplication $application): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin_officer', 'hr_outsourcing_officer']);
    }
}
