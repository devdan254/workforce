<x-student-layout title="Invoice Details">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('student.invoices.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Invoices</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $invoice->invoice_number }}</h2>
                <p class="text-secondary mb-0">{{ $invoice->description }}</p>
                @if($invoice->studyApplication)
                    <p class="text-secondary small mb-0">{{ $invoice->studyApplication->university->name }}</p>
                @endif
            </div>
            <a href="{{ route('student.invoices.download', $invoice) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-download"></i> Download PDF
            </a>
        </div>

        <hr>

        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">{{ $invoice->currency }} {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-semibold">Total</td>
                    <td class="text-end fw-semibold">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end text-success">Paid</td>
                    <td class="text-end text-success">{{ $invoice->currency }} {{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end fw-semibold">Balance</td>
                    <td class="text-end fw-semibold {{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $invoice->currency }} {{ number_format($invoice->balance, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="row g-4">
        {{-- Pay Now --}}
        @if($invoice->balance > 0)
            <div class="col-lg-6" id="pay">
                <div class="card stat-card p-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Pay Now</h3>
                    <p class="text-secondary small">
                        Submitting here records your payment as <strong>pending</strong> — our Finance team
                        confirms it against your reference before it reflects on your balance.
                    </p>
                    <form method="POST" action="{{ route('student.invoices.payments.store', $invoice) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $invoice->currency }}</span>
                                <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $invoice->balance) }}" max="{{ $invoice->balance }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select name="method" class="form-select" required>
                                <option value="mpesa">M-Pesa</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Transaction Reference (optional)</label>
                            <input type="text" name="reference" class="form-control" placeholder="e.g. M-Pesa confirmation code">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Payment</button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Payment history for THIS invoice --}}
        <div class="col-lg-6">
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Payments on This Invoice</h3>
                @forelse($invoice->payments as $payment)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold">{{ $invoice->currency }} {{ number_format($payment->amount, 2) }}</div>
                            <div class="text-secondary small">{{ ucfirst($payment->method) }} · {{ $payment->paid_at?->format('d M Y') ?? 'Not yet paid' }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $payment->status === 'confirmed' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($payment->status) }}</span>
                            @if($payment->status === 'confirmed')
                                <a href="{{ route('student.payments.receipt', $payment) }}" class="d-block small mt-1">Receipt</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No payments recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

</x-student-layout>
