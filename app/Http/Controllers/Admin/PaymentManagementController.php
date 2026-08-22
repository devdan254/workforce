<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * The one central place every payment in the system is visible, regardless
 * of whether it belongs to a Student, Job Seeker, Employer, or a Visa
 * process. Deliberately NOT a new payment system — every action here
 * (confirm/refund/update) calls the exact same PaymentService the Student,
 * Job Seeker, Employer, and Visa Management workspaces already use.
 * Confirming a payment here is confirming the SAME row those workspaces
 * read; nothing is duplicated, nothing needs synchronizing, because there
 * was only ever one table.
 *
 * "Type/Service" is genuinely computed, not stored — see categoryFor()
 * below. A payment is "Visa" when its invoice is explicitly tagged
 * (invoice.visa_application_id, added in the Visa Management build)
 * regardless of the payer's own role — a Student can still have a
 * Visa-context invoice, and that specific payment belongs under Visa here,
 * not Student, matching the spec's own explicit category list.
 */
class PaymentManagementController extends Controller
{
    private const CATEGORIES = ['visa', 'student', 'job_seeker', 'employer'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['invoice.studyApplication.student', 'invoice.jobApplication.jobSeeker', 'invoice.visaApplication', 'student', 'confirmedBy', 'transactions']);

        if ($request->filled('category') && in_array($request->string('category')->value(), self::CATEGORIES, true)) {
            $this->applyCategoryFilter($query, $request->string('category')->value());
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereHas('student', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        if ($request->filled('invoice_number')) {
            $query->whereHas('invoice', fn ($q) => $q->where('invoice_number', 'like', '%'.$request->string('invoice_number').'%'));
        }

        if ($request->filled('transaction_code')) {
            $query->whereHas('transactions', fn ($q) => $q->where('gateway_reference', 'like', '%'.$request->string('transaction_code').'%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date('date_to'));
        }

        $payments = $query->latest('paid_at')->paginate(20)->withQueryString();

        // Per-row category label, computed once here rather than
        // re-evaluating the same match() in the view for every row.
        $payments->getCollection()->transform(function (Payment $payment) {
            $payment->computed_category = $this->categoryFor($payment);

            return $payment;
        });

        return view('admin.payments-management.index', [
            'payments' => $payments,
            'counts' => $this->categoryCounts(),
        ]);
    }

    public function confirm(Request $request, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('confirm', $payment);

        $service->confirmPayment($payment, $request->user());

        return back()->with('success', 'Payment confirmed — the invoice balance has been updated.');
    }

    public function refund(Request $request, Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('refund', $payment);

        $service->refundPayment($payment);

        return back()->with('success', 'Payment refunded — the invoice balance has been adjusted.');
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): RedirectResponse
    {
        $payment->update([
            'amount' => $request->input('amount'),
            'method' => $request->string('method'),
        ]);

        return back()->with('success', 'Payment updated.');
    }

    /**
     * Three portal-specific download routes already exist (Student/Job
     * Seeker/Employer), each correctly authorized for staff too — but none
     * cover a Visa-only guest invoice, since Visa Management never had a
     * download feature of its own. Rather than route through one of the
     * three arbitrarily (technically permitted, semantically wrong for a
     * guest who was never actually a student), this is one small generic
     * method reusing the SAME already-generic PDF template every other
     * download route already renders — no new template, no duplicated
     * invoice-rendering logic.
     */
    public function downloadInvoice(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'student']);

        $pdf = Pdf::loadView('student.invoices.pdf', ['invoice' => $invoice]);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Mirrors the exclusive precedence categoryFor() below uses — the
     * Visa filter must match exactly what shows as "Visa" per-row, and a
     * Student/Job Seeker/Employer filter must exclude anything already
     * claimed by Visa, or the same payment could appear to belong to two
     * categories depending which filter you clicked.
     */
    private function applyCategoryFilter($query, string $category): void
    {
        if ($category === 'visa') {
            $query->whereHas('invoice', fn ($q) => $q->whereNotNull('visa_application_id'));

            return;
        }

        $query->whereHas('invoice', fn ($q) => $q->whereNull('visa_application_id'))
            ->whereHas('student', fn ($q) => $q->role(match ($category) {
                'student' => 'student',
                'job_seeker' => 'job_seeker',
                'employer' => 'employer',
            }));
    }

    private function categoryFor(Payment $payment): string
    {
        if (! is_null($payment->invoice?->visa_application_id)) {
            return 'Visa';
        }

        $payer = $payment->student;

        return match (true) {
            is_null($payer) => 'Other',
            $payer->isStudent() => 'Student',
            $payer->isJobSeeker() => 'Job Seeker',
            $payer->isEmployer() => 'Employer',
            default => 'Other',
        };
    }

    /**
     * Powers the "All / Visa / Students / Job Seeker / Employer" tab
     * counts — same exclusive precedence as applyCategoryFilter(), so the
     * numbers shown always match what clicking each tab actually returns.
     */
    private function categoryCounts(): array
    {
        $base = fn () => Payment::query();

        $visa = (clone $base())->whereHas('invoice', fn ($q) => $q->whereNotNull('visa_application_id'))->count();

        $byRole = fn (string $role) => (clone $base())
            ->whereHas('invoice', fn ($q) => $q->whereNull('visa_application_id'))
            ->whereHas('student', fn ($q) => $q->role($role))
            ->count();

        return [
            'all' => $base()->count(),
            'visa' => $visa,
            'student' => $byRole('student'),
            'job_seeker' => $byRole('job_seeker'),
            'employer' => $byRole('employer'),
        ];
    }
}
