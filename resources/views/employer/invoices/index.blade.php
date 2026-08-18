<x-employer-layout title="Invoices">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Invoices</h2>

        <form method="GET" action="{{ route('employer.invoices.index') }}" class="d-flex align-items-center gap-2">
            <label class="form-label small fw-semibold mb-0 text-nowrap">Job</label>
            <select name="job_posting_id" class="form-select form-select-sm" style="min-width:220px;" onchange="this.form.submit()">
                <option value="">All Jobs</option>
                @foreach($jobPostings as $posting)
                    <option value="{{ $posting->id }}" @selected((int) request('job_posting_id') === $posting->id)>{{ $posting->title }} — {{ $posting->country }}</option>
                @endforeach
            </select>
            @if(request('job_posting_id'))
                <a href="{{ route('employer.invoices.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    <div class="card stat-card p-3 mb-4">
        <div class="row text-center">
            <div class="col-4">
                <div class="stat-label">Total Cost</div>
                <div class="stat-value">{{ $summary['currency'] }} {{ number_format($summary['total'], 0) }}</div>
            </div>
            <div class="col-4">
                <div class="stat-label">Paid</div>
                <div class="stat-value text-success">{{ $summary['currency'] }} {{ number_format($summary['paid'], 0) }}</div>
            </div>
            <div class="col-4">
                <div class="stat-label">Outstanding</div>
                <div class="stat-value {{ $summary['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $summary['currency'] }} {{ number_format($summary['balance'], 0) }}
                </div>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="fw-semibold">{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->description }}</td>
                            <td>{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                            <td>{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($invoice->status) {
                                        'paid' => 'bg-success-subtle text-success-emphasis',
                                        'overdue' => 'bg-danger-subtle text-danger-emphasis',
                                        'cancelled' => 'bg-secondary-subtle text-secondary-emphasis',
                                        default => 'bg-warning-subtle text-warning-emphasis',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employer.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('employer.invoices.download', $invoice) }}" class="btn btn-sm btn-outline-secondary">PDF</a>
                                @if($invoice->status !== 'paid')
                                    <a href="{{ route('employer.invoices.show', $invoice) }}#pay" class="btn btn-sm btn-primary">Pay Now</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No invoices match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $invoices->links() }}</div>

</x-employer-layout>
