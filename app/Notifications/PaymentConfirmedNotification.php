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

        return [
            'title' => 'Payment Confirmed',
            'body' => "Your payment of {$this->payment->currency} {$amount} has been confirmed. Thank you.",
            'link' => Route::has('student.invoices.show')
                ? route('student.invoices.show', $this->payment->invoice_id)
                : '#',
            'icon' => 'circle-dollar',
        ];
    }
}
