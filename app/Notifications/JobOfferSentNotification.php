<?php

namespace App\Notifications;

use App\Models\JobOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class JobOfferSentNotification extends Notification
{
    use Queueable;

    public function __construct(public JobOffer $offer) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Job Offer Received',
            'body' => "You've received a job offer of {$this->offer->currency} ".number_format((float) $this->offer->salary, 0).'. Review the details and respond.',
            'link' => Route::has('job-seeker.offers.show') ? route('job-seeker.offers.show', $this->offer) : '#',
            'icon' => 'handshake',
        ];
    }
}
