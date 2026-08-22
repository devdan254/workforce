<x-admin-layout title="Payments Management">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Payments Management</h2>
    </div>

    {{-- Category tabs — All/Visa/Student/Job Seeker/Employer, counts computed
         with the exact same exclusive precedence the filter itself uses. --}}
    <ul class="nav nav-pills mb-4 flex-wrap gap-1">
        @foreach([
            '' => ['label' => 'All', 'count' => $counts['all']],
            'visa' => ['label' => 'Visa', 'count' => $counts['visa']],
            'student' => ['label' => 'Students', 'count' => $counts['student']],
            'job_seeker' => ['label' => 'Job Seekers', 'count' => $counts['job_seeker']],
            'employer' => ['label' => 'Employers', 'count' => $counts['employer']],
        ] as $key => $tab)
            <li class="nav-item">
                <a class="nav-link {{ request('category', '') === $key ? 'active' : '' }}"
                   href="{{ route('admin.payments-management.index', array_merge(request()->except('page'), ['category' => $key])) }}">
                    {{ $tab['label'] }} <span class="badge bg-light text-dark ms-1">{{ $tab['count'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    {{-- Filters --}}
    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.payments-management.index') }}" class="row g-2 align-items-end">
            <input type="hidden" name="category" value="{{ request('category') }}">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Customer</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or email">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Invoice #</label>
                <input type="text" name="invoice_number" class="form-control" value="{{ request('invoice_number') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Transaction Code</label>
                <input type="text" name="transaction_code" class="form-control" value="{{ request('transaction_code') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Payment Type</label>
                <select name="method" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['mpesa', 'card', 'bank_transfer', 'cash'] as $method)
                        <option value="{{ $method }}" @selected(request('method') === $method)>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'confirmed', 'failed', 'refunded'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                <a href="{{ route('admin.payments-management.index', ['category' => request('category')]) }}" class="btn btn-light btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Payer</th>
                        <th>Type</th>
                        <th>Invoice #</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Transaction Code</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php $transaction = $payment->transactions->first(); @endphp
                        <tr>
                            <td class="small">{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</td>
                            <td class="small fw-semibold">{{ $payment->student?->name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ match($payment->computed_category) { 'Visa' => 'bg-info-subtle text-info-emphasis', 'Employer' => 'bg-primary-subtle text-primary-emphasis', 'Job Seeker' => 'bg-warning-subtle text-warning-emphasis', 'Student' => 'bg-success-subtle text-success-emphasis', default => 'bg-secondary-subtle text-secondary-emphasis' } }}">{{ $payment->computed_category }}</span>
                            </td>
                            <td class="small">
                                @if($payment->invoice)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#invoice-{{ $payment->id }}" class="fw-semibold">{{ $payment->invoice->invoice_number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small fw-semibold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                            <td class="small text-capitalize">{{ str_replace('_', ' ', $payment->method) }}</td>
                            <td class="small">
                                @if($transaction && $transaction->gateway_reference)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#transaction-{{ $payment->id }}" class="fw-semibold">{{ $transaction->gateway_reference }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ match($payment->status) { 'confirmed' => 'bg-success-subtle text-success-emphasis', 'failed' => 'bg-danger-subtle text-danger-emphasis', 'refunded' => 'bg-secondary-subtle text-secondary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucfirst($payment->status) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($payment->status === 'pending')
                                        @can('confirm', $payment)
                                            <form method="POST" action="{{ route('admin.payments-management.confirm', $payment) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                            </form>
                                        @endcan
                                        @can('update', $payment)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit-{{ $payment->id }}">Edit</button>
                                        @endcan
                                    @elseif($payment->status === 'confirmed')
                                        @can('refund', $payment)
                                            <form method="POST" action="{{ route('admin.payments-management.refund', $payment) }}" onsubmit="return confirm('Refund this payment?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Refund</button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>

                                {{-- Modals for this row live INSIDE this table cell, not
                                     as siblings between table rows — a container element
                                     placed directly between rows would fall outside what
                                     the table body is allowed to contain, and browsers
                                     silently relocate it during parsing, breaking every
                                     row that follows. Matches how the Documents partials
                                     already nest their modals correctly. --}}

                                {{-- ===== Invoice modal ===== --}}
                                @if($payment->invoice)
                            <div class="modal fade" id="invoice-{{ $payment->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Invoice {{ $payment->invoice->invoice_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3 mb-3">
                                                <div class="col-6">
                                                    <div class="small text-secondary">Customer</div>
                                                    <div class="fw-semibold">{{ $payment->invoice->student?->name ?? '—' }}</div>
                                                    <div class="small text-secondary">{{ $payment->invoice->student?->email }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="small text-secondary">Related Application</div>
                                                    <div class="fw-semibold small">
                                                        @if($payment->invoice->visaApplication)
                                                            Visa — {{ $payment->invoice->visaApplication->destination_country }}
                                                        @elseif($payment->invoice->studyApplication)
                                                            Study Application — {{ $payment->invoice->studyApplication->student?->name }}
                                                        @elseif($payment->invoice->jobApplication)
                                                            Job Application — {{ $payment->invoice->jobApplication->jobSeeker?->name }}
                                                        @else
                                                            General — not tied to a specific application
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-sm mb-3">
                                                <thead class="table-light">
                                                    <tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($payment->invoice->items as $item)
                                                        <tr>
                                                            <td class="small">{{ $item->description }}</td>
                                                            <td class="text-end small">{{ $item->quantity }}</td>
                                                            <td class="text-end small">{{ $payment->invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                                            <td class="text-end small">{{ $payment->invoice->currency }} {{ number_format($item->line_total, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="row g-2 small">
                                                <div class="col-6 text-secondary">Total Amount</div>
                                                <div class="col-6 text-end fw-semibold">{{ $payment->invoice->currency }} {{ number_format($payment->invoice->total, 2) }}</div>
                                                <div class="col-6 text-secondary">Amount Paid</div>
                                                <div class="col-6 text-end text-success fw-semibold">{{ $payment->invoice->currency }} {{ number_format($payment->invoice->amount_paid, 2) }}</div>
                                                <div class="col-6 text-secondary">Balance / Pending</div>
                                                <div class="col-6 text-end fw-semibold {{ $payment->invoice->balance > 0 ? 'text-danger' : 'text-success' }}">{{ $payment->invoice->currency }} {{ number_format($payment->invoice->balance, 2) }}</div>
                                                <div class="col-6 text-secondary">Due Date</div>
                                                <div class="col-6 text-end">{{ $payment->invoice->due_date?->format('d M Y') ?? '—' }}</div>
                                                <div class="col-6 text-secondary">Invoice Status</div>
                                                <div class="col-6 text-end">
                                                    <span class="badge {{ match($payment->invoice->status) { 'paid' => 'bg-success-subtle text-success-emphasis', 'sent' => 'bg-primary-subtle text-primary-emphasis', 'overdue' => 'bg-danger-subtle text-danger-emphasis', 'cancelled' => 'bg-secondary-subtle text-secondary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucfirst($payment->invoice->status) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('admin.payments-management.invoices.download', $payment->invoice) }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-download"></i> Download</a>
                                            <a href="{{ route('admin.payments-management.invoices.download', $payment->invoice) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-print"></i> Print</a>
                                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ===== Transaction modal ===== --}}
                        @if($transaction)
                            <div class="modal fade" id="transaction-{{ $payment->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Transaction Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-2 small">
                                                <div class="col-6 text-secondary">Reference Code</div>
                                                <div class="col-6 text-end fw-semibold">{{ $transaction->gateway_reference ?? '—' }}</div>
                                                <div class="col-6 text-secondary">Gateway</div>
                                                <div class="col-6 text-end text-capitalize">{{ $transaction->gateway }}</div>
                                                <div class="col-6 text-secondary">Payment Method</div>
                                                <div class="col-6 text-end text-capitalize">{{ str_replace('_', ' ', $payment->method) }}</div>
                                                <div class="col-6 text-secondary">Amount</div>
                                                <div class="col-6 text-end fw-semibold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
                                                <div class="col-6 text-secondary">Date</div>
                                                <div class="col-6 text-end">{{ $transaction->created_at->format('d M Y') }}</div>
                                                <div class="col-6 text-secondary">Time</div>
                                                <div class="col-6 text-end">{{ $transaction->created_at->format('g:i:s A') }}</div>
                                                <div class="col-6 text-secondary">Transaction Status</div>
                                                <div class="col-6 text-end">
                                                    <span class="badge {{ $transaction->status === 'confirmed' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">{{ ucfirst($transaction->status) }}</span>
                                                </div>
                                                @if($payment->confirmedBy)
                                                    <div class="col-6 text-secondary">Confirmed By</div>
                                                    <div class="col-6 text-end">{{ $payment->confirmedBy->name }}</div>
                                                @endif
                                            </div>
                                            @if($transaction->raw_payload)
                                                <hr>
                                                <div class="small text-secondary mb-1">Gateway Payload</div>
                                                <pre class="small bg-light p-2 rounded" style="white-space:pre-wrap;max-height:160px;overflow:auto;">{{ json_encode($transaction->raw_payload, JSON_PRETTY_PRINT) }}</pre>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ===== Edit payment modal ===== --}}
                        @if($payment->status === 'pending')
                            <div class="modal fade" id="edit-{{ $payment->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.payments-management.update', $payment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Edit Payment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Amount</label>
                                                    <input type="number" step="0.01" name="amount" class="form-control form-control-sm" value="{{ $payment->amount }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Method</label>
                                                    <select name="method" class="form-select form-select-sm" required>
                                                        @foreach(['mpesa', 'card', 'bank_transfer', 'cash'] as $method)
                                                            <option value="{{ $method }}" @selected($payment->method === $method)>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-5">No payments match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $payments->links() }}</div>

</x-admin-layout>
