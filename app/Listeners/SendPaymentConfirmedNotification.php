<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Notifications\PaymentConfirmedNotification;

class SendPaymentConfirmedNotification
{
    public function handle(PaymentConfirmed $event): void
    {
        $event->payment->student->notify(new PaymentConfirmedNotification($event->payment));
    }
}
