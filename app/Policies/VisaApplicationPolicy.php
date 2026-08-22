<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisaApplication;

/**
 * Handles BOTH Student and Job Seeker visas — a VisaApplication links to
 * exactly one of studyApplication/jobApplication (enforced by
 * HasExclusiveApplicationLink), never both. Checking $visa->studyApplication
 * unconditionally would throw a null-pointer error for a Job Seeker's visa,
 * since that relationship is null in that case — this was a real bug caught
 * while building Job Seeker's Applications tab, not a hypothetical one.
 */
class VisaApplicationPolicy
{
    /**
     * Only staff reach the central Visa Management list at all — a
     * Student/Job Seeker views their own visa through their Workspace
     * (view() below), not this list. No per-row check needed here since
     * the list itself is filtered to what the viewing staff member should
     * see at the query level, same pattern as every other Admin index.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('visa.view');
    }

    public function create(User $user): bool
    {
        return $user->can('visa.create');
    }

    public function view(User $user, VisaApplication $visa): bool
    {
        if ($user->isStudent()) {
            return $visa->studyApplication?->student_id === $user->id;
        }

        if ($user->isJobSeeker()) {
            return $visa->jobApplication?->job_seeker_id === $user->id;
        }

        return $user->can('visa.view');
    }

    public function update(User $user, VisaApplication $visa): bool
    {
        if (! $user->can('visa.update') || $user->isStudent() || $user->isJobSeeker()) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'admin_officer', 'hr_outsourcing_officer', 'visa_officer'])) {
            return true;
        }

        $assignedOfficerId = $visa->studyApplication?->assigned_officer_id ?? $visa->jobApplication?->assigned_officer_id;

        return $assignedOfficerId === $user->id;
    }
}
