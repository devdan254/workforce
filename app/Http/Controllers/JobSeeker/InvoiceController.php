<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = $request->user()->invoices()
            ->with('jobApplication.jobPosting')
            ->latest('due_date')
            ->paginate(10);

        return view('job-seeker.invoices.index', [
            'invoices' => $invoices,
            'summary' => Invoice::financialSummaryForPerson($request->user()->id),
        ]);
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'payments' => fn ($q) => $q->latest(), 'jobApplication.jobPosting']);

        return view('job-seeker.invoices.show', ['invoice' => $invoice]);
    }

    public function downloadPdf(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'student']);

        $pdf = Pdf::loadView('student.invoices.pdf', ['invoice' => $invoice]);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
