<?php

namespace App\Policies;

use App\Models\StudyApplication;
use App\Models\User;

class StudyApplicationPolicy
{
    /**
     * The "My Applications" list. Students always pass (the query itself
     * scopes to their own records) — staff need applications.view.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStudent() || $user->can('applications.view');
    }

    /**
     * Student viewing their own application, OR staff with permission
     * (assigned officer, or anyone holding applications.view for cross-student visibility).
     */
    public function view(User $user, StudyApplication $application): bool
    {
        if ($user->isStudent()) {
            return $application->student_id === $user->id;
        }

        return $user->can('applications.view');
    }

    public function create(User $user): bool
    {
        // Students create their own applications; staff can create on a student's behalf.
        return $user->isStudent() || $user->can('applications.create');
    }

    public function update(User $user, StudyApplication $application): bool
    {
        if ($user->isStudent()) {
            // Students may only edit while still in a pre-submission state.
            return $application->student_id === $user->id && is_null($application->submitted_at);
        }

        return $user->can('applications.update');
    }

    /**
     * Changing status is the highest-risk action — require the specific permission
     * AND (Super Admin / Admin Officer, OR the officer actually assigned to this case).
     */
    public function changeStatus(User $user, StudyApplication $application): bool
    {
        if (! $user->can('applications.change_status')) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'admin_officer'])) {
            return true;
        }

        return $application->assigned_officer_id === $user->id;
    }

    public function assignOfficer(User $user, StudyApplication $application): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin_officer']);
    }

    /**
     * Soft-delete only (StudyApplication has SoftDeletes) — restricted to the
     * two highest trust levels, matching assignOfficer's bar. Deleting an
     * application is a materially bigger action than editing one.
     */
    public function delete(User $user, StudyApplication $application): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin_officer']);
    }
}
