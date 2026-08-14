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

    /**
     * Admin/staff REQUESTING a document from a student — creates a "required"
     * row with no file yet, the inverse of create() above. Reuses documents.verify
     * rather than documents.upload, since anyone who reviews documents is the
     * natural fit for also requesting one.
     */
    public function request(User $user): bool
    {
        return $user->can('documents.verify') && ! $user->isStudent();
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->isStudent()) {
            return $document->student_id === $user->id;
        }

        return $user->can('documents.view');
    }

    /**
     * Uploading a FILE onto an existing document row — student uploading
     * their own, or staff uploading on the student's behalf (e.g. a document
     * they received by email/in person and are logging into the system).
     */
    public function upload(User $user, Document $document): bool
    {
        if ($user->isStudent()) {
            return $document->student_id === $user->id;
        }

        return $user->can('documents.upload');
    }

    /**
     * Renaming / re-categorizing a document row — metadata only, not the file
     * itself. Same permission as upload; if you can attach a file, you can
     * also fix a typo in its name.
     */
    public function update(User $user, Document $document): bool
    {
        if ($user->isStudent()) {
            return $document->student_id === $user->id && $document->status !== 'verified';
        }

        return $user->can('documents.upload');
    }

    /**
     * Deleting a document row entirely — genuinely destructive, so this is
     * staff-only regardless of status, gated on a dedicated permission
     * rather than reusing upload/verify.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->can('documents.delete') && ! $user->isStudent();
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
