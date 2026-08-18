<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reuses the shared invoice architecture and PDF template entirely — same
 * pattern as JobSeeker\InvoiceController. Job filter reuses the existing
 * invoices.job_application_id link (added in Stage 2) transitively through
 * to job_posting_id, no new schema needed here.
 */
class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user();
        $employerJobPostings = $employer->jobPostings()->orderBy('title')->get();

        $query = $employer->invoices()->with('jobApplication.jobPosting');

        if ($request->filled('job_posting_id')) {
            $query->whereHas('jobApplication', fn ($q) => $q->where('job_posting_id', $request->integer('job_posting_id')));
        }

        $invoices = $query->latest('due_date')->paginate(10)->withQueryString();

        return view('employer.invoices.index', [
            'invoices' => $invoices,
            'summary' => Invoice::financialSummaryForPerson($employer->id),
            'jobPostings' => $employerJobPostings,
        ]);
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'payments' => fn ($q) => $q->latest(), 'jobApplication.jobPosting']);

        return view('employer.invoices.show', ['invoice' => $invoice]);
    }

    public function downloadPdf(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'student']);

        $pdf = Pdf::loadView('student.invoices.pdf', ['invoice' => $invoice]);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
