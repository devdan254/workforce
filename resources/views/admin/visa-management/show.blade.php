<x-admin-layout title="Visa Application">

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

    <a href="{{ route('admin.visa-management.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Visa Management</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $visaApplication->displayName() }}</h2>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @php
                        $typeBadge = match($visaApplication->applicantType()) {
                            'student' => 'bg-primary-subtle text-primary-emphasis',
                            'job_seeker' => 'bg-info-subtle text-info-emphasis',
                            default => 'bg-secondary-subtle text-secondary-emphasis',
                        };
                        $typeLabel = match($visaApplication->applicantType()) {
                            'student' => 'Student',
                            'job_seeker' => 'Job Seeker',
                            default => 'Visa-Only Applicant',
                        };
                    @endphp
                    <span class="badge {{ $typeBadge }}">{{ $typeLabel }}</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis">{{ $visaApplication->currentStatus?->label ?? 'No Status' }}</span>
                    <span class="text-secondary small">{{ $visaApplication->destination_country }}@if($visaApplication->visa_type) · {{ $visaApplication->visa_type }}@endif</span>
                </div>
            </div>
            @can('update', $visaApplication)
                <a href="{{ route('admin.visa-management.edit', $visaApplication) }}" class="btn btn-sm btn-outline-secondary">Edit Application</a>
            @endcan
        </div>

        @if($visaApplication->applicantType() !== 'guest')
            <hr>
            <p class="text-secondary small mb-0">
                Linked to
                @if($visaApplication->applicantType() === 'student')
                    <a href="{{ route('admin.students.show', $visaApplication->studyApplication->student_id) }}">this student's workspace →</a>
                @else
                    <a href="{{ route('admin.job-seekers.show', $visaApplication->jobApplication->job_seeker_id) }}">this job seeker's workspace →</a>
                @endif
            </p>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Applicant Details</h3>
                <dl class="row small mb-0">
                    @if($visaApplication->applicantType() === 'guest')
                        <dt class="col-4 text-secondary fw-normal">Date of Birth</dt><dd class="col-8">{{ $visaApplication->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                        <dt class="col-4 text-secondary fw-normal">Nationality</dt><dd class="col-8">{{ $visaApplication->nationality ?? '—' }}</dd>
                        <dt class="col-4 text-secondary fw-normal">Country of Residence</dt><dd class="col-8">{{ $visaApplication->country_of_residence ?? '—' }}</dd>
                        <dt class="col-4 text-secondary fw-normal">Passport Number</dt><dd class="col-8">{{ $visaApplication->passport_number ?? '—' }}</dd>
                        <dt class="col-4 text-secondary fw-normal">Passport Expiry</dt><dd class="col-8">{{ $visaApplication->passport_expiry?->format('d M Y') ?? '—' }}</dd>
                        <dt class="col-4 text-secondary fw-normal">Contact</dt><dd class="col-8">{{ $visaApplication->user?->email }}@if($visaApplication->user?->phone) · {{ $visaApplication->user->phone }}@endif</dd>
                    @else
                        <dt class="col-4 text-secondary fw-normal">Email</dt><dd class="col-8">{{ $visaApplication->owner()?->email }}</dd>
                        <dt class="col-4 text-secondary fw-normal">Phone</dt><dd class="col-8">{{ $visaApplication->owner()?->phone ?? '—' }}</dd>
                    @endif
                </dl>

                <hr>
                <dl class="row small mb-0">
                    <dt class="col-4 text-secondary fw-normal">Purpose of Travel</dt><dd class="col-8">{{ $visaApplication->purpose_of_travel ?? '—' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Expected Travel Date</dt><dd class="col-8">{{ $visaApplication->expected_travel_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Duration of Stay</dt><dd class="col-8">{{ $visaApplication->duration_of_stay ?? '—' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Embassy Appointment</dt><dd class="col-8">{{ $visaApplication->embassy_appointment_at?->format('d M Y, g:ia') ?? 'Not scheduled' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Decision</dt><dd class="col-8 text-capitalize">{{ $visaApplication->decision }}</dd>
                </dl>

                @if($visaApplication->previously_applied || $visaApplication->previously_refused || $visaApplication->travelled_internationally)
                    <hr>
                    <dl class="row small mb-0">
                        <dt class="col-4 text-secondary fw-normal">Previously Applied</dt><dd class="col-8">{{ $visaApplication->previously_applied ?? '—' }}</dd>
                        <dt class="col-4 text-secondary fw-normal">Previously Refused</dt><dd class="col-8">{{ $visaApplication->previously_refused ?? '—' }}</dd>
                        @if($visaApplication->refusal_explanation)
                            <dt class="col-4 text-secondary fw-normal">Refusal Explanation</dt><dd class="col-8">{{ $visaApplication->refusal_explanation }}</dd>
                        @endif
                        <dt class="col-4 text-secondary fw-normal">Travelled Internationally</dt><dd class="col-8">{{ $visaApplication->travelled_internationally ?? '—' }}</dd>
                        @if($visaApplication->countries_visited)
                            <dt class="col-4 text-secondary fw-normal">Countries Visited</dt><dd class="col-8">{{ $visaApplication->countries_visited }}</dd>
                        @endif
                    </dl>
                @endif

                @if($visaApplication->additional_info)
                    <hr>
                    <strong class="small">Additional Info</strong>
                    <p class="small mb-0 mt-1">{{ $visaApplication->additional_info }}</p>
                @endif
            </div>

            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Status History</h3>
                @forelse($visaApplication->statusHistories as $history)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="small">
                            {{ $history->fromStatus?->label ?? 'Started' }} → <strong>{{ $history->toStatus->label }}</strong>
                            <div class="text-secondary" style="font-size:.75rem;">by {{ $history->changedBy?->name ?? 'System' }} · {{ $history->created_at->format('d M Y, g:ia') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-secondary mb-0 small">No status changes yet.</p>
                @endforelse
            </div>

            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Notes</h3>
                @forelse($notes as $note)
                    <div class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex justify-content-between small">
                            <span class="fw-semibold">{{ $note->createdBy->name }}</span>
                            <span class="text-secondary">{{ $note->created_at->format('d M Y, g:ia') }}</span>
                        </div>
                        <p class="small mb-0 mt-1">{{ $note->body }}</p>
                    </div>
                @empty
                    <p class="text-secondary mb-3 small">No notes yet.</p>
                @endforelse
                <form method="POST" action="{{ route('admin.visa-management.notes.store', $visaApplication) }}" class="mt-3">
                    @csrf
                    <textarea name="body" class="form-control form-control-sm mb-2" rows="2" placeholder="Add an internal note..." required></textarea>
                    <button type="submit" class="btn btn-primary btn-sm">Add Note</button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            @can('update', $visaApplication)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Change Status</h3>
                    @if($allowedNextStatuses->isNotEmpty())
                        <form method="POST" action="{{ route('admin.visa-management.status.update', $visaApplication) }}">
                            @csrf
                            <select name="to_status_id" class="form-select form-select-sm mb-2" required>
                                <option value="">Select next status</option>
                                @foreach($allowedNextStatuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->label }}</option>
                                @endforeach
                            </select>
                            <textarea name="note" class="form-control form-control-sm mb-2" rows="2" placeholder="Note (optional)"></textarea>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button>
                        </form>
                    @else
                        <p class="text-secondary small mb-0">This visa application has reached a final status — no further transitions available.</p>
                    @endif
                </div>
            @endcan

            <div class="card stat-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Documents</h3>
                    @can('update', $visaApplication)
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestVisaDocumentModal">+ Request Document</button>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Document</th><th>Category</th><th>Status</th><th>Uploaded</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $document)
                                <tr>
                                    <td class="fw-semibold small">{{ $document->name }}</td>
                                    <td class="small text-secondary">{{ $document->category->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ match($document->status) { 'verified' => 'bg-success-subtle text-success-emphasis', 'rejected' => 'bg-danger-subtle text-danger-emphasis', 'under_review' => 'bg-primary-subtle text-primary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucwords(str_replace('_', ' ', $document->status)) }}</span>
                                    </td>
                                    <td class="small text-secondary">{{ $document->uploaded_at?->format('d M Y') ?? '—' }}</td>
                                    <td class="text-end">
                                        @include('admin.visa-management.partials._document-actions', ['document' => $document, 'visaApplication' => $visaApplication, 'categories' => $documentCategories, 'prefix' => 'visa'])
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-secondary py-4">No documents yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @can('update', $visaApplication)
                <div class="modal fade" id="requestVisaDocumentModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.visa-management.documents.request', $visaApplication) }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Request Document</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Category</label>
                                        <select name="document_category_id" class="form-select" required>
                                            <option value="">Select a category</option>
                                            @foreach($documentCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold">Document Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="e.g. Bank Statement" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Request Document</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="card stat-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Invoices</h3>
                    @can('update', $visaApplication)
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createVisaInvoiceModal">+ Create Invoice</button>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Invoice</th><th>Description</th><th>Amount</th><th>Due Date</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="fw-semibold small">{{ $invoice->invoice_number }}</td>
                                    <td class="small">{{ $invoice->description }}</td>
                                    <td class="small">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                                    <td class="small">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ match($invoice->status) { 'paid' => 'bg-success-subtle text-success-emphasis', 'sent' => 'bg-primary-subtle text-primary-emphasis', 'cancelled' => 'bg-secondary-subtle text-secondary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucfirst($invoice->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($invoice->status === 'draft')
                                                <form method="POST" action="{{ route('admin.visa-management.invoices.send', [$visaApplication, $invoice]) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">Send</button>
                                                </form>
                                            @endif
                                            @if(!in_array($invoice->status, ['paid', 'cancelled']))
                                                <form method="POST" action="{{ route('admin.visa-management.invoices.cancel', [$visaApplication, $invoice]) }}" onsubmit="return confirm('Cancel this invoice?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-4">No invoices yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Payments</h3>
                    @can('update', $visaApplication)
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordVisaPaymentModal">+ Record Payment</button>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>Amount</th><th>Method</th><th>Invoice</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="small">{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</td>
                                    <td class="fw-semibold small">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                    <td class="small text-capitalize">{{ $payment->method }}</td>
                                    <td class="small">{{ $payment->invoice->invoice_number }}</td>
                                    <td><span class="badge {{ $payment->status === 'confirmed' ? 'bg-success-subtle text-success-emphasis' : ($payment->status === 'refunded' ? 'bg-secondary-subtle text-secondary-emphasis' : 'bg-warning-subtle text-warning-emphasis') }}">{{ ucfirst($payment->status) }}</span></td>
                                    <td class="text-end">
                                        @if($payment->status === 'pending')
                                            <form method="POST" action="{{ route('admin.visa-management.payments.confirm', [$visaApplication, $payment]) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                            </form>
                                        @elseif($payment->status === 'confirmed')
                                            <form method="POST" action="{{ route('admin.visa-management.payments.refund', [$visaApplication, $payment]) }}" class="d-inline" onsubmit="return confirm('Refund this payment?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Refund</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-4">No payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @can('update', $visaApplication)
                <div class="modal fade" id="createVisaInvoiceModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.visa-management.invoices.store', $visaApplication) }}" id="createVisaInvoiceForm">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Create Invoice</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label small fw-semibold">Description</label>
                                            <input type="text" name="description" class="form-control form-control-sm" placeholder="e.g. Visa Processing Fee" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold">Currency</label>
                                            <input type="text" name="currency" class="form-control form-control-sm" value="USD" maxlength="3" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold">Due Date</label>
                                            <input type="date" name="due_date" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <label class="form-label small fw-semibold">Line Items</label>
                                    <div id="visaInvoiceItems">
                                        <div class="row g-2 mb-2">
                                            <div class="col-6"><input type="text" name="items[0][description]" class="form-control form-control-sm" placeholder="Description" required></div>
                                            <div class="col-2"><input type="number" name="items[0][quantity]" class="form-control form-control-sm" placeholder="Qty" value="1" min="1" required></div>
                                            <div class="col-4"><input type="number" step="0.01" name="items[0][unit_price]" class="form-control form-control-sm" placeholder="Unit Price" required></div>
                                        </div>
                                    </div>
                                    <button type="button" id="addVisaInvoiceItemBtn" class="btn btn-sm btn-outline-secondary">+ Add Line Item</button>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Create Invoice (Draft)</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="recordVisaPaymentModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ $invoices->isNotEmpty() ? route('admin.visa-management.invoices.payments.store', [$visaApplication, $invoices->first()]) : '#' }}" id="recordVisaPaymentForm">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Record Payment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    @if($invoices->isEmpty())
                                        <p class="text-secondary small mb-0">Create an invoice first.</p>
                                    @else
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Invoice</label>
                                            <select name="invoice_select" class="form-select form-select-sm" id="recordVisaPaymentInvoice" required>
                                                @foreach($invoices as $invoice)
                                                    <option value="{{ route('admin.visa-management.invoices.payments.store', [$visaApplication, $invoice]) }}">
                                                        {{ $invoice->invoice_number }} — {{ $invoice->currency }} {{ number_format($invoice->balance, 2) }} outstanding
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Amount</label>
                                            <input type="number" step="0.01" name="amount" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Method</label>
                                            <select name="method" class="form-select form-select-sm" required>
                                                <option value="mpesa">M-Pesa</option>
                                                <option value="card">Card</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="cash">Cash</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Reference (optional)</label>
                                            <input type="text" name="reference" class="form-control form-control-sm">
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="confirm_immediately" value="1" class="form-check-input" id="visaConfirmImmediately">
                                            <label class="form-check-label small" for="visaConfirmImmediately">Already received in full — confirm immediately</label>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    @if($invoices->isNotEmpty())
                                        <button type="submit" class="btn btn-primary">Record Payment</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    (function () {
                        var index = 1;
                        var btn = document.getElementById('addVisaInvoiceItemBtn');
                        var container = document.getElementById('visaInvoiceItems');
                        if (btn && container) {
                            btn.addEventListener('click', function () {
                                var row = document.createElement('div');
                                row.className = 'row g-2 mb-2';
                                row.innerHTML =
                                    '<div class="col-6"><input type="text" name="items[' + index + '][description]" class="form-control form-control-sm" placeholder="Description" required></div>' +
                                    '<div class="col-2"><input type="number" name="items[' + index + '][quantity]" class="form-control form-control-sm" placeholder="Qty" value="1" min="1" required></div>' +
                                    '<div class="col-4"><input type="number" step="0.01" name="items[' + index + '][unit_price]" class="form-control form-control-sm" placeholder="Unit Price" required></div>';
                                container.appendChild(row);
                                index++;
                            });
                        }
                        var select = document.getElementById('recordVisaPaymentInvoice');
                        var form = document.getElementById('recordVisaPaymentForm');
                        if (select && form) {
                            select.addEventListener('change', function () { form.action = this.value; });
                            form.action = select.value;
                        }
                    })();
                </script>
            @endcan
        </div>
    </div>

</x-admin-layout>
