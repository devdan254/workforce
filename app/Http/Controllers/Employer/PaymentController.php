<?php

namespace App\Http\Controllers\Employer;

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

/**
 * Reuses the shared payment architecture entirely — same PaymentService,
 * same Invoice::financialSummaryForPerson(), same receipt PDF template
 * Student and Job Seeker already use. Nothing new here except the query
 * scoping and the Job filter (via invoice.jobApplication.jobPosting — a
 * Payment has no direct job link, only its parent Invoice does).
 */
class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $employer = $request->user();
        $employerJobPostings = $employer->jobPostings()->orderBy('title')->get();

        $query = $employer->payments()->with('invoice.jobApplication.jobPosting');

        if ($request->filled('job_posting_id')) {
            $query->whereHas(
                'invoice.jobApplication',
                fn ($q) => $q->where('job_posting_id', $request->integer('job_posting_id'))
            );
        }

        $payments = $query->latest('paid_at')->paginate(15)->withQueryString();

        return view('employer.payments.index', [
            'payments' => $payments,
            'summary' => Invoice::financialSummaryForPerson($employer->id),
            'jobPostings' => $employerJobPostings,
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
            ->route('employer.invoices.show', $invoice)
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
