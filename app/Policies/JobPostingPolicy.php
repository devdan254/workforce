<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEmployer() || $user->can('job_postings.view');
    }

    /**
     * Employer can only view postings that belong to them (employer_id
     * matches) — never another employer's postings, even if they somehow
     * guess the URL. Staff can view any posting via the permission.
     */
    public function view(User $user, JobPosting $posting): bool
    {
        if ($user->isEmployer()) {
            return $posting->employer_id === $user->id;
        }

        return $user->can('job_postings.view');
    }

    public function create(User $user): bool
    {
        return $user->can('job_postings.create');
    }

    public function update(User $user, JobPosting $posting): bool
    {
        return $user->can('job_postings.update');
    }

    /**
     * Publish/Unpublish/Close/Archive/Feature — grouped under one ability
     * since they're all state-changes on an existing posting, not content
     * edits. Separate from update() per the spec's explicit permission set
     * (job_postings.publish exists distinctly from job_postings.update).
     * Deliberately staff-only, no Employer branch — the spec is explicit
     * that "the employer should not have a Publish button."
     */
    public function publish(User $user, JobPosting $posting): bool
    {
        return $user->can('job_postings.publish');
    }
}
