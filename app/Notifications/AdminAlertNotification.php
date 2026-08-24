<?php

namespace App\Notifications;

use App\Mail\AdminNotificationMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * The one place every "notify admin" call goes through — both the in-app
 * bell AND the email fire from here. Every existing call site only needed
 * its Mail::to(...)->send(new AdminNotificationMail(...)) call swapped for
 * AdminAlertNotification::sendToAdmins(...) with the exact same arguments.
 * Nothing about what gets sent changed — just added a second channel
 * alongside the one that already worked.
 *
 * Deliberately NOT ShouldQueue — every other notification class in this
 * app (DocumentRequestedNotification, PaymentConfirmedNotification, etc.)
 * sends synchronously, with no dependency on a queue worker running. This
 * class originally implemented ShouldQueue, which meant it would sit
 * unprocessed on any environment without `php artisan queue:work`
 * actually running (the common case for local development) — the
 * in-app notification silently never appeared. Matches the established,
 * already-working pattern now instead of introducing a new one.
 *
 * Targets super_admin + admin_officer specifically — the general admin
 * roles — not every staff role. A visa_officer or finance_officer doesn't
 * need a bell notification for every new registration; they already have
 * their own scoped view into what's relevant to them.
 */
class AdminAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $heading,
        public array $lines,
        public ?string $actionUrl = null,
    ) {}

    /**
     * @param  array<string,string>  $lines
     * @param  array<int,array{content:string,name:string,mime:?string}>  $fileAttachments
     */
    public static function sendToAdmins(
        string $heading,
        array $lines,
        ?string $actionLabel = null,
        ?string $actionUrl = null,
        array $fileAttachments = [],
    ): void {
        $admins = User::role(['super_admin', 'admin_officer'])->get();

        NotificationFacade::send($admins, new self($heading, $lines, $actionUrl));

        Mail::to(config('notifications.admin_email'))->send(new AdminNotificationMail(
            heading: $heading,
            lines: $lines,
            actionLabel: $actionLabel,
            actionUrl: $actionUrl,
            fileAttachments: $fileAttachments,
        ));
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->heading,
            'body' => collect($this->lines)->map(fn ($value, $label) => "{$label}: {$value}")->implode(' · '),
            'link' => $this->actionUrl ?? '#',
            'icon' => 'bell',
        ];
    }
}
