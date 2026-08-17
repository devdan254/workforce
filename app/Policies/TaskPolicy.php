<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Tasks are staff-internal (assigning work between officers) — students
     * and job seekers never see these.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Task $task): bool
    {
        if (! $user->isStaff()) {
            return false;
        }

        return $task->assigned_to === $user->id
            || $task->created_by === $user->id
            || $user->hasAnyRole(['super_admin', 'admin_officer']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin_officer']);
    }

    public function update(User $user, Task $task): bool
    {
        if (! $user->isStaff()) {
            return false;
        }

        return $task->assigned_to === $user->id || $user->hasAnyRole(['super_admin', 'admin_officer']);
    }
}
