<?php

namespace App\Policies;

use App\Models\User;

/**
 * Governs Admin's ability to manage STUDENT records specifically (User rows
 * with the student role). Stage 1 has no staff-managing-staff UI, so this
 * policy's scope is deliberately narrow — every method maps to the
 * `students.*` permissions seeded in RolesAndPermissionsSeeder.
 *
 * "Suspend" is authorized via students.update, not a separate permission —
 * it's just toggling is_active, not a destructive action. students.delete
 * exists in the permission set but nothing in Stage 1's UI calls it yet;
 * the spec is explicit about not building casual destructive-delete flows.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('students.view');
    }

    public function view(User $user, User $student): bool
    {
        return $user->can('students.view');
    }

    public function create(User $user): bool
    {
        return $user->can('students.create');
    }

    public function update(User $user, User $student): bool
    {
        return $user->can('students.update');
    }

    public function suspend(User $user, User $student): bool
    {
        return $user->can('students.update');
    }
}
