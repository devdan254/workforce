<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class PaymentConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $amount = number_format((float) $this->payment->amount, 2);
        $routeName = $notifiable->isJobSeeker() ? 'job-seeker.invoices.show' : 'student.invoices.show';

        return [
            'title' => 'Payment Confirmed',
            'body' => "Your payment of {$this->payment->currency} {$amount} has been confirmed. Thank you.",
            'link' => Route::has($routeName)
                ? route($routeName, $this->payment->invoice_id)
                : '#',
            'icon' => 'circle-dollar',
        ];
    }
}
