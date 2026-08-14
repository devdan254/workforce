<?php

namespace App\Services;

use App\Events\PaymentConfirmed;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * The ONLY place invoice.amount_paid should ever change. If you find yourself
 * writing $invoice->amount_paid = ... anywhere else, stop — route it through here,
 * or Student/Admin balances WILL drift apart (the one thing the spec explicitly forbids).
 */
class PaymentService
{
    public function recordPayment(
        Invoice $invoice,
        float $amount,
        string $method,
        ?User $recordedBy = null,
    ): Payment {
        return Payment::create([
            'invoice_id' => $invoice->id,
            'student_id' => $invoice->student_id,
            'amount' => $amount,
            'currency' => $invoice->currency,
            'method' => $method,
            'status' => 'pending',
        ]);
    }

    public function confirmPayment(Payment $payment, User $confirmedBy): Payment
    {
        return DB::transaction(function () use ($payment, $confirmedBy) {
            $payment->update([
                'status' => 'confirmed',
                'paid_at' => now(),
                'confirmed_by' => $confirmedBy->id,
            ]);

            $invoice = $payment->invoice()->lockForUpdate()->first();
            $newAmountPaid = $invoice->payments()->where('status', 'confirmed')->sum('amount');

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'status' => $newAmountPaid >= $invoice->total ? 'paid' : $invoice->status,
            ]);

            event(new PaymentConfirmed($payment));

            return $payment;
        });
    }

    public function refundPayment(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'refunded']);

            $invoice = $payment->invoice()->lockForUpdate()->first();
            $newAmountPaid = $invoice->payments()->where('status', 'confirmed')->sum('amount');

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'status' => $newAmountPaid >= $invoice->total ? 'paid' : 'pending',
            ]);

            return $payment;
        });
    }
}
