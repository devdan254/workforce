<?php

namespace App\Listeners;

use App\Events\StatusChanged;
use App\Models\StudyApplication;
use App\Notifications\StatusChangedNotification;

class SendStatusChangeNotification
{
    public function handle(StatusChanged $event): void
    {
        // Resolve the student regardless of whether this was an application or visa transition.
        $student = $event->statusable instanceof StudyApplication
            ? $event->statusable->student
            : $event->statusable->studyApplication->student;

        $student->notify(new StatusChangedNotification($event->history));
    }
}
