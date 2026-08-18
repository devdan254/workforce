<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkerRequest;

class WorkerRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEmployer() || $user->can('worker_requests.view');
    }

    public function view(User $user, WorkerRequest $workerRequest): bool
    {
        if ($user->isEmployer()) {
            return $workerRequest->employer_id === $user->id;
        }

        return $user->can('worker_requests.view');
    }

    public function create(User $user): bool
    {
        return $user->isEmployer();
    }

    /**
     * An employer can only edit their own request while it's still
     * pending Altura's attention — once Altura has started reviewing it
     * (or decided on it), the request is part of the record, same
     * reasoning as Payment::update() only allowing edits while pending.
     */
    public function update(User $user, WorkerRequest $workerRequest): bool
    {
        if ($user->isEmployer()) {
            return $workerRequest->employer_id === $user->id
                && in_array($workerRequest->status, ['draft', 'submitted']);
        }

        return $user->can('worker_requests.update');
    }

    /**
     * Answering a clarification request — distinct from update() since it's
     * only valid in exactly one status, and it's a narrower action (one
     * text field) than editing the whole request.
     */
    public function respond(User $user, WorkerRequest $workerRequest): bool
    {
        return $user->isEmployer()
            && $workerRequest->employer_id === $user->id
            && $workerRequest->status === 'clarification_required';
    }

    public function review(User $user, WorkerRequest $workerRequest): bool
    {
        return $user->can('worker_requests.review');
    }
}