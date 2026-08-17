<?php

namespace App\Listeners;

use App\Events\DocumentUploaded;
use App\Notifications\DocumentUploadedNotification;

class NotifyAssignedOfficerOfDocument
{
    /**
     * document->studyApplication only covers Student documents — for a Job
     * Seeker's document (linked via job_application_id), this always
     * resolved to null, silently skipping the notification every time
     * rather than throwing. No error, no log entry — just an officer who
     * never found out a document was uploaded. Fixed to check both links.
     */
    public function handle(DocumentUploaded $event): void
    {
        $officer = $event->document->studyApplication?->assignedOfficer
            ?? $event->document->jobApplication?->assignedOfficer;

        // No application-specific officer assigned (e.g. a general vault document) —
        // fall back to notifying whoever holds documents.verify would be a query;
        // for Stage 1/2 we simply skip rather than guess who should see it.
        if (! $officer) {
            return;
        }

        $officer->notify(new DocumentUploadedNotification($event->document));
    }
}