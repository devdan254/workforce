<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Payment history across ALL of the student's invoices, per the spec's
     * dashboard example table: Date | Description | Amount | Status | Receipt.
     */
    public function index(Request $request): View
    {
        $payments = $request->user()->payments()
            ->with('invoice')
            ->latest('paid_at')
            ->paginate(15);

        return view('student.payments.index', [
            'payments' => $payments,
            'summary' => Invoice::financialSummaryForStudent($request->user()->id),
        ]);
    }

    /**
     * "Pay Now" — records the payment as PENDING and attaches a transaction
     * reference (e.g. an M-Pesa code) for Finance to verify against. This does
     * NOT touch invoice.amount_paid — that only happens when Finance actually
     * confirms it (PaymentService::confirmPayment), never on student submission
     * alone. Matches the spec: payments must be a real, auditable workflow,
     * not a student self-reporting a number that's trusted blindly.
     */
    public function store(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        $payment = $this->paymentService->recordPayment(
            $invoice,
            (float) $request->input('amount'),
            $request->string('method'),
        );

        if ($request->filled('reference')) {
            $payment->transactions()->create([
                'gateway' => 'manual',
                'gateway_reference' => $request->string('reference'),
                'status' => 'pending',
            ]);
        }

        return redirect()
            ->route('student.invoices.show', $invoice)
            ->with('success', 'Payment submitted — our Finance team will confirm it shortly.');
    }

    public function receipt(Payment $payment): Response
    {
        $this->authorize('view', $payment);

        if ($payment->status !== 'confirmed') {
            abort(404, 'Receipt is only available for confirmed payments.');
        }

        $payment->load('invoice', 'student', 'confirmedBy');

        $pdf = Pdf::loadView('student.payments.receipt', ['payment' => $payment]);

        return $pdf->download("receipt-{$payment->id}.pdf");
    }
}
