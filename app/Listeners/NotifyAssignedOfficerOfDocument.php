<?php

namespace App\Listeners;

use App\Events\DocumentUploaded;
use App\Notifications\DocumentUploadedNotification;

class NotifyAssignedOfficerOfDocument
{
    public function handle(DocumentUploaded $event): void
    {
        $officer = $event->document->studyApplication?->assignedOfficer;

        // No application-specific officer assigned (e.g. a general vault document) —
        // fall back to notifying whoever holds documents.verify would be a query;
        // for Stage 1 we simply skip rather than guess who should see it.
        if (! $officer) {
            return;
        }

        $officer->notify(new DocumentUploadedNotification($event->document));
    }
}
