<?php

namespace App\Policies;

use App\Models\Resource;
use App\Models\User;

/**
 * One permission (resources.manage) governs every action here — that's
 * what was already seeded, covering create/update/delete together rather
 * than split into resources.view/create/update/delete like most other
 * modules. Not invented for this delivery; reused exactly as it existed.
 */
class ResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('resources.manage');
    }

    public function view(User $user, Resource $resource): bool
    {
        return $user->can('resources.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('resources.manage');
    }

    public function update(User $user, Resource $resource): bool
    {
        return $user->can('resources.manage');
    }

    public function delete(User $user, Resource $resource): bool
    {
        return $user->can('resources.manage');
    }
}
