<x-student-layout title="Invoices">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Invoices</h2>

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
                                <a href="{{ route('student.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('student.invoices.download', $invoice) }}" class="btn btn-sm btn-outline-secondary">PDF</a>
                                @if($invoice->status !== 'paid')
                                    <a href="{{ route('student.invoices.show', $invoice) }}#pay" class="btn btn-sm btn-primary">Pay Now</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No invoices yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $invoices->links() }}</div>

</x-student-layout>
