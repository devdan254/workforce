<?php

namespace App\Policies;

use App\Models\StudyPosting;
use App\Models\User;

/**
 * Mirrors JobPostingPolicy exactly — same shape, same reasoning. Study
 * postings are Altura's controlled catalog the same way job postings are;
 * only Admin creates/publishes them.
 */
class StudyPostingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('study_postings.view');
    }

    public function view(User $user, StudyPosting $posting): bool
    {
        return $user->can('study_postings.view');
    }

    public function create(User $user): bool
    {
        return $user->can('study_postings.create');
    }

    public function update(User $user, StudyPosting $posting): bool
    {
        return $user->can('study_postings.update');
    }

    /**
     * Publish/Unpublish/Close/Archive/Feature — grouped under one ability
     * since they're all state-changes on an existing posting, not content
     * edits. Separate from update() per the same pattern job_postings.publish
     * already established.
     */
    public function publish(User $user, StudyPosting $posting): bool
    {
        return $user->can('study_postings.publish');
    }
}
