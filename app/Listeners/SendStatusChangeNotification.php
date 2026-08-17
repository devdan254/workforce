<?php

namespace App\Listeners;

use App\Events\StatusChanged;
use App\Models\JobApplication;
use App\Models\StudyApplication;
use App\Notifications\StatusChangedNotification;

class SendStatusChangeNotification
{
    /**
     * Resolves the notifiable person regardless of which of the four real
     * cases this transition is:
     *   1. StudyApplication status changed directly       -> ->student
     *   2. JobApplication status changed directly         -> ->jobSeeker
     *   3. VisaApplication linked to a StudyApplication    -> ->studyApplication->student
     *   4. VisaApplication linked to a JobApplication      -> ->jobApplication->jobSeeker
     * Case 2 and 4 were missing entirely before this fix — this listener is
     * wired to the generic StatusChanged event (fired by
     * ApplicationStatusService for ANY status transition, application or
     * visa, either type), so it silently only worked for Students until a
     * Job Application status change actually exercised the gap.
     */
    public function handle(StatusChanged $event): void
    {
        $statusable = $event->statusable;

        $person = match (true) {
            $statusable instanceof StudyApplication => $statusable->student,
            $statusable instanceof JobApplication => $statusable->jobSeeker,
            $statusable->studyApplication !== null => $statusable->studyApplication->student,
            $statusable->jobApplication !== null => $statusable->jobApplication->jobSeeker,
            default => null,
        };

        $person?->notify(new StatusChangedNotification($event->history));
    }
}