<x-admin-layout title="Dashboard">

    <div class="mb-4">
        <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
        <p class="text-secondary mb-0">Overview of Altura Workforce Solutions.</p>
    </div>

    {{-- ============ ROW 1 — FINANCIAL OVERVIEW ============ --}}
    <h3 class="h6 fw-semibold text-secondary text-uppercase mb-3" style="letter-spacing:.04em;font-size:.8rem;">Financial Overview</h3>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6 col-lg-3">
            <a href="{{ route('admin.payments-management.index', ['status' => 'confirmed']) }}" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Amount Paid</div>
                    @forelse($amountPaidByCurrency as $currency => $amount)
                        <div class="stat-value" style="{{ !$loop->first ? 'font-size:1rem;' : '' }}">{{ $currency }} {{ number_format($amount, 0) }}</div>
                    @empty
                        <div class="stat-value">—</div>
                    @endforelse
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="{{ route('admin.payments-management.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Amount Pending</div>
                    @forelse($amountPendingByCurrency as $currency => $amount)
                        <div class="stat-value" style="{{ !$loop->first ? 'font-size:1rem;' : '' }}">{{ $currency }} {{ number_format($amount, 0) }}</div>
                    @empty
                        <div class="stat-value">—</div>
                    @endforelse
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="{{ route('admin.payments-management.index') }}" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Total Payments</div>
                    @forelse($totalByCurrency as $currency => $amount)
                        <div class="stat-value" style="{{ !$loop->first ? 'font-size:1rem;' : '' }}">{{ $currency }} {{ number_format($amount, 0) }}</div>
                    @empty
                        <div class="stat-value">—</div>
                    @endforelse
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="{{ route('admin.payments-management.index') }}" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Pending Invoices</div>
                    <div class="stat-value">{{ number_format($pendingInvoicesCount) }}</div>
                </div>
            </a>
        </div>
    </div>

    {{-- ============ ROW 2 — APPLICATIONS & SERVICES ============ --}}
    <h3 class="h6 fw-semibold text-secondary text-uppercase mb-3" style="letter-spacing:.04em;font-size:.8rem;">Applications &amp; Services</h3>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6 col-lg-3">
            <a href="{{ route('admin.students.index') }}" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Student Applications</div>
                    <div class="stat-value">{{ number_format($studentApplicationsCount) }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="{{ route('admin.job-seekers.index') }}" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Job Applications</div>
                    <div class="stat-value">{{ number_format($jobApplicationsCount) }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="{{ route('admin.worker-requests.index') }}" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Worker Requests</div>
                    <div class="stat-value">{{ number_format($workerRequestsCount) }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <a href="{{ route('admin.visa-management.index') }}" class="text-decoration-none">
                <div class="card stat-card p-3 h-100">
                    <div class="stat-label">Visa Applications</div>
                    <div class="stat-value">{{ number_format($visaApplicationsCount) }}</div>
                </div>
            </a>
        </div>
    </div>
    {{-- ============ ROW 3 — SYSTEM & OPERATIONS ============ --}}
    <h3 class="h6 fw-semibold text-secondary text-uppercase mb-3" style="letter-spacing:.04em;font-size:.8rem;">System &amp; Operations</h3>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label mb-2">System Users</div>
                @foreach($userBreakdown as $label => $count)
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-secondary">{{ $label }}</span>
                        <span class="fw-semibold">{{ number_format($count) }}</span>
                    </div>
                @endforeach
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="stat-label mb-0">All Users</span>
                    <span class="stat-value" style="font-size:1.1rem;">{{ number_format($allUsersCount) }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label mb-2">Upcoming Appointments</div>
                <a href="{{ route('admin.students.index') }}" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Students</span>
                    <span class="fw-semibold">{{ str_pad($appointmentBreakdown['Students'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
                <a href="{{ route('admin.job-seekers.index') }}" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Job Applicants</span>
                    <span class="fw-semibold">{{ str_pad($appointmentBreakdown['Job Applicants'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
                <a href="{{ route('admin.employers.index') }}" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Employers</span>
                    <span class="fw-semibold">{{ str_pad($appointmentBreakdown['Employers'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="stat-label mb-0">Total Upcoming</span>
                    <span class="stat-value" style="font-size:1.1rem;">{{ number_format($upcomingAppointmentsCount) }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label mb-2">Support Tickets</div>
                <a href="{{ route('admin.support.index', ['status' => 'open']) }}" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Open</span>
                    <span class="fw-semibold">{{ str_pad($ticketBreakdown['Open'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
                <a href="{{ route('admin.support.index', ['status' => ['in_progress', 'waiting_for_student']]) }}" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">In Progress</span>
                    <span class="fw-semibold">{{ str_pad($ticketBreakdown['In Progress'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
                <a href="{{ route('admin.support.index', ['status' => 'resolved']) }}" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Resolved</span>
                    <span class="fw-semibold">{{ str_pad($ticketBreakdown['Resolved'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
                <a href="{{ route('admin.support.index', ['status' => 'closed']) }}" class="d-flex justify-content-between text-decoration-none small">
                    <span class="text-secondary">Closed</span>
                    <span class="fw-semibold">{{ str_pad($ticketBreakdown['Closed'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label mb-2">Notifications</div>
                <a href="{{ route('admin.notifications.index', ['read' => 0]) }}" class="d-flex justify-content-between text-decoration-none small mb-1">
                    <span class="text-secondary">Unread</span>
                    <span class="fw-semibold">{{ str_pad($notificationBreakdown['Unread'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
                <a href="{{ route('admin.notifications.index', ['read' => 1]) }}" class="d-flex justify-content-between text-decoration-none small">
                    <span class="text-secondary">Read</span>
                    <span class="fw-semibold">{{ str_pad($notificationBreakdown['Read'], 2, '0', STR_PAD_LEFT) }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ============ ROW 4 — RECENT ACTIVITY ============ --}}
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card stat-card">
                <div class="p-3 border-bottom">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Recent Applications</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Applicant</th><th>Type</th><th>Reference</th><th>Date</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications as $item)
                                <tr>
                                    <td class="small fw-semibold">{{ $item['applicant'] }}</td>
                                    <td class="small text-secondary">{{ $item['type'] }}</td>
                                    <td class="small">{{ $item['reference'] }}</td>
                                    <td class="small">{{ $item['date']?->format('d M Y') ?? '—' }}</td>
                                    <td><span class="badge bg-primary-subtle text-primary-emphasis">{{ $item['status'] }}</span></td>
                                    <td class="text-end">
                                        @if($item['url'])
                                            <a href="{{ $item['url'] }}" class="btn btn-sm btn-outline-secondary">View</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-4">No recent activity.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card stat-card">
                <div class="p-3 border-bottom">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Recent Payments</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Payer</th><th>Type</th><th>Invoice #</th><th>Amount</th><th>Date</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                @php $transaction = $payment->transactions->first(); @endphp
                                <tr>
                                    <td class="small fw-semibold">{{ $payment->student?->name ?? '—' }}</td>
                                    <td class="small text-secondary">{{ $payment->computed_category }}</td>
                                    <td class="small">
                                        @if($payment->invoice)
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#dash-invoice-{{ $payment->id }}" class="fw-semibold">{{ $payment->invoice->invoice_number }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="small fw-semibold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                    <td class="small">{{ $payment->created_at->format('d M Y') }}</td>
                                    <td><span class="badge {{ match($payment->status) { 'confirmed' => 'bg-success-subtle text-success-emphasis', 'failed' => 'bg-danger-subtle text-danger-emphasis', 'refunded' => 'bg-secondary-subtle text-secondary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucfirst($payment->status) }}</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.payments-management.index') }}" class="btn btn-sm btn-outline-secondary">Manage</a>

                                        {{-- Modal lives INSIDE this <td>, not as a sibling
                                             between <tr> elements — see Payments Management's
                                             own view for the full reasoning; a <div> between
                                             rows is invalid HTML and gets silently relocated
                                             by the browser, breaking every row after it. --}}
                                        @if($payment->invoice)
                                            <div class="modal fade" id="dash-invoice-{{ $payment->id }}" tabindex="-1">
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
                                                                @if($transaction)
                                                                    <div class="col-6 text-secondary">Transaction Code</div>
                                                                    <div class="col-6 text-end fw-semibold">{{ $transaction->gateway_reference ?? '—' }}</div>
                                                                    <div class="col-6 text-secondary">Payment Method</div>
                                                                    <div class="col-6 text-end text-capitalize">{{ str_replace('_', ' ', $payment->method) }}</div>
                                                                @endif
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
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-secondary py-4">No recent payments.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-admin-layout>
