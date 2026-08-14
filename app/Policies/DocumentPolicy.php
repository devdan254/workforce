<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * A student proactively adding a document (not just uploading against
     * a pre-created "required" row) — no specific Document instance exists
     * yet, so this checks the actor only, same pattern as StudyApplicationPolicy::create.
     */
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->isStudent()) {
            return $document->student_id === $user->id;
        }

        return $user->can('documents.view');
    }

    public function upload(User $user, Document $document): bool
    {
        if ($user->isStudent()) {
            return $document->student_id === $user->id;
        }

        return $user->can('documents.upload');
    }

    public function verify(User $user, Document $document): bool
    {
        return $user->can('documents.verify') && ! $user->isStudent();
    }

    public function reject(User $user, Document $document): bool
    {
        return $user->can('documents.reject') && ! $user->isStudent();
    }
}
