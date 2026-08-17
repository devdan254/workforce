<?php

namespace App\Notifications;

use App\Models\StatusHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class StatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public StatusHistory $history) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $statusable = $this->history->historyable;
        $isVisa = $statusable->statusType() === 'visa';
        $typeLabel = $isVisa ? 'Visa' : 'Application';
        $isJobSeeker = $notifiable->isJobSeeker();

        if ($isVisa) {
            $link = $isJobSeeker
                ? $this->routeOrFallback('job-seeker.applications.visa', $statusable->job_application_id)
                : $this->routeOrFallback('student.applications.visa', $statusable->study_application_id);
        } else {
            $link = $isJobSeeker
                ? $this->routeOrFallback('job-seeker.applications.show', $statusable->id)
                : $this->routeOrFallback('student.applications.show', $statusable->id);
        }

        return [
            'title' => "{$typeLabel} Update",
            'body' => "Your {$typeLabel} status changed to \"{$this->history->toStatus->label}\".",
            'link' => $link,
            'icon' => 'circle-check',
        ];
    }

    /**
     * Falls back to '#' so notifications/events/seeders all work even if a
     * named route isn't registered yet — no code change needed here once it is.
     */
    private function routeOrFallback(string $name, mixed $param): string
    {
        return Route::has($name) && $param ? route($name, $param) : '#';
    }
}
