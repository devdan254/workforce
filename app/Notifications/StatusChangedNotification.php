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
        $typeLabel = $statusable->statusType() === 'visa' ? 'Visa' : 'Application';

        $link = $statusable->statusType() === 'visa'
            ? $this->routeOrFallback('student.applications.visa', $statusable->study_application_id)
            : $this->routeOrFallback('student.applications.show', $statusable->id);

        return [
            'title' => "{$typeLabel} Update",
            'body' => "Your {$typeLabel} status changed to \"{$this->history->toStatus->label}\".",
            'link' => $link,
            'icon' => 'circle-check',
        ];
    }

    /**
     * Student portal routes don't exist yet (next deliverable). Falls back to '#'
     * so notifications/events/seeders all work TODAY and pick up the real link
     * automatically the moment the named route is registered — no code change needed here.
     */
    private function routeOrFallback(string $name, mixed $param): string
    {
        return Route::has($name) ? route($name, $param) : '#';
    }
}
