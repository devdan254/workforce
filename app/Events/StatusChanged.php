<?php

namespace App\Events;

use App\Contracts\HasStatusWorkflow;
use App\Models\StatusHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired after ApplicationStatusService successfully transitions a StudyApplication
 * OR a VisaApplication. Listeners should not assume which one — check
 * $event->statusable->statusType() if the distinction matters.
 */
class StatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Model&HasStatusWorkflow $statusable,
        public StatusHistory $history,
    ) {}
}
