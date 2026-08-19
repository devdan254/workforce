<?php

namespace App\Http\Controllers\Admin\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployerInvoiceRequest;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Http\Requests\Admin\UpdatePaymentRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\InvoiceSentNotification;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Mirrors Admin\JobSeeker\FinanceController almost exactly — genuinely
 * reuses StorePaymentRequest/UpdatePaymentRequest unmodified (both were
 * already fully generic before this delivery, no student/job-seeker-
 * specific validation at all). Only StoreEmployerInvoiceRequest needed a
 * variant, matching the same job_application_id filter chain the
 * Employer-facing Invoices page already reads from.
 */
class FinanceController extends Controller
{
    public function storePayment(StorePaymentRequest $request, User $employer, Invoice $invoice, PaymentService $service): RedirectResponse
    {
        abort_unless($invoice->student_id === $employer->id, 404);

        $payment = $service->recordPayment($invoice, (float) $request->input('amount'), $request->string('method'));

        if ($request->filled('reference')) {
            $payment->transactions()->create([
                'gateway' => 'manual',
                'gateway_reference' => $request->string('reference'),
                'status' => $request->boolean('confirm_immediately') ? 'confirmed' : 'pending',
            ]);
        }

        if ($request->boolean('confirm_immediately')) {
            $service->confirmPayment($payment, $request->user());
        }

        return back()->with('success', 'Payment recorded'.($request->boolean('confirm_immediately') ? ' and confirmed.' : ' as pending confirmation.'));
    }

    public function updatePayment(UpdatePaymentRequest $request, User $employer, Payment $payment): RedirectResponse
    {
        abort_unless($payment->student_id === $employer->id, 404);

        $payment->update([
            'amount' => $request->input('amount'),
            'method' => $request->string('method'),
        ]);

        return back()->with('success', 'Payment updated.');
    }

    public function refundPayment(Request $request, User $employer, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('refund', $payment);
        abort_unless($payment->student_id === $employer->id, 404);

        $service->refundPayment($payment);

        return back()->with('success', 'Payment refunded — the invoice balance has been adjusted.');
    }

    public function storeInvoice(StoreEmployerInvoiceRequest $request, User $employer): RedirectResponse
    {
        $invoice = DB::transaction(function () use ($request, $employer) {
            $items = collect($request->input('items'))->map(fn ($item) => [
                'description' => $item['description'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'line_total' => (int) $item['quantity'] * (float) $item['unit_price'],
            ]);

            $subtotal = $items->sum('line_total');

            $invoice = Invoice::create([
                'student_id' => $employer->id,
                'job_application_id' => $request->input('job_application_id'),
                'description' => $request->string('description'),
                'currency' => $request->string('currency'),
                'subtotal' => $subtotal,
                'tax' => 0,
                'total' => $subtotal,
                'status' => 'draft',
                'due_date' => $request->input('due_date'),
            ]);

            foreach ($items as $item) {
                InvoiceItem::create($item + ['invoice_id' => $invoice->id]);
            }

            return $invoice;
        });

        return back()->with('success', "Invoice {$invoice->invoice_number} created as a draft. Send it when ready.");
    }

    public function sendInvoice(Request $request, User $employer, Invoice $invoice): RedirectResponse
    {
        $this->authorize('send', $invoice);
        abort_unless($invoice->student_id === $employer->id, 404);

        $invoice->update(['status' => 'sent', 'sent_at' => now()]);

        $employer->notify(new InvoiceSentNotification($invoice));

        return back()->with('success', 'Invoice sent to employer.');
    }

    public function cancelInvoice(Request $request, User $employer, Invoice $invoice): RedirectResponse
    {
        $this->authorize('cancel', $invoice);
        abort_unless($invoice->student_id === $employer->id, 404);

        $invoice->update(['status' => 'cancelled']);

        return back()->with('success', 'Invoice cancelled.');
    }
}
