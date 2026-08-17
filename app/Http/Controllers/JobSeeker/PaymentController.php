<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobSeeker\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $payments = $request->user()->payments()
            ->with('invoice')
            ->latest('paid_at')
            ->paginate(15);

        return view('job-seeker.payments.index', [
            'payments' => $payments,
            'summary' => Invoice::financialSummaryForPerson($request->user()->id),
        ]);
    }

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
            ->route('job-seeker.invoices.show', $invoice)
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
