<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired AFTER PaymentService::confirmPayment() has already updated the invoice
 * balance — this event is for side effects (notifications) only, never for
 * core data mutation. Balance consistency must never depend on a listener firing.
 */
class PaymentConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(public Payment $payment) {}
}
