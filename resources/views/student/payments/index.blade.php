<x-student-layout title="Payments">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Payments</h2>

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
        @if($summary['balance'] > 0)
            <div class="text-center mt-3">
                <a href="{{ route('student.invoices.index') }}" class="btn btn-primary btn-sm">Pay Outstanding Balance →</a>
            </div>
        @endif
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</td>
                            <td>{{ $payment->invoice->description }}</td>
                            <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $payment->status === 'confirmed' ? 'bg-success' : ($payment->status === 'failed' ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($payment->status === 'confirmed')
                                    <a href="{{ route('student.payments.receipt', $payment) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @else
                                    <span class="text-secondary small">Pending confirmation</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-5">No payments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $payments->links() }}</div>

</x-student-layout>
