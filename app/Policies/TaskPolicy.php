<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Tasks are staff-internal (assigning work between officers) — students never see these.
     */
    public function viewAny(User $user): bool
    {
        return ! $user->isStudent();
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->isStudent()) {
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
        if ($user->isStudent()) {
            return false;
        }

        return $task->assigned_to === $user->id || $user->hasAnyRole(['super_admin', 'admin_officer']);
    }
}
