<?php

namespace App\Services;

use App\Events\DocumentUploaded;
use App\Models\Document;
use App\Models\User;

/**
 * The only place document.status should be written — keeps the
 * Required -> Uploaded -> Under Review -> Verified (or Rejected -> Re-upload)
 * lifecycle consistent and auditable (Document already logs activity via LogsActivity).
 */
class DocumentVerificationService
{
    public function markUploaded(Document $document, string $filePath, string $mimeType, int $sizeBytes): Document
    {
        $document->update([
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'status' => 'under_review',
            'uploaded_at' => now(),
            // Clear any previous rejection once the student re-uploads.
            'rejection_reason' => null,
        ]);

        event(new DocumentUploaded($document));

        return $document;
    }

    public function verify(Document $document, User $verifier, ?string $notes = null): Document
    {
        $document->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'verification_notes' => $notes,
            'rejection_reason' => null,
        ]);

        return $document;
    }

    public function reject(Document $document, User $verifier, string $reason): Document
    {
        $document->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'rejection_reason' => $reason,
        ]);

        return $document;
    }
}
