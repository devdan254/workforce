<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisaApplication;

class VisaApplicationPolicy
{
    public function view(User $user, VisaApplication $visa): bool
    {
        if ($user->isStudent()) {
            return $visa->studyApplication->student_id === $user->id;
        }

        return $user->can('visa.view');
    }

    public function update(User $user, VisaApplication $visa): bool
    {
        if (! $user->can('visa.update') || $user->isStudent()) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'admin_officer', 'visa_officer'])) {
            return true;
        }

        return $visa->studyApplication->assigned_officer_id === $user->id;
    }
}
