<x-admin-layout title="Student Workspace">

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

    <a href="{{ route('admin.students.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Students</a>

    {{-- Workspace header --}}
    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $student->name }}</h2>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-primary-subtle text-primary-emphasis">Student</span>
                    <span class="badge {{ $student->is_active ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                        {{ $student->is_active ? 'Active' : 'Suspended' }}
                    </span>
                    <span class="text-secondary small">Assigned Officer: {{ $primaryApplication?->assignedOfficer?->name ?? 'Not yet assigned' }}</span>
                </div>
            </div>
            <div class="text-end">
                <div class="small text-secondary">Profile Completion</div>
                <div class="fw-semibold fs-5">{{ $student->studentProfile?->profile_completion_percent ?? 0 }}%</div>
                @can('update', $student)
                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-secondary mt-1">Edit Account</a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tab navigation --}}
    <ul class="nav nav-pills mb-4 flex-wrap gap-1" id="workspaceTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-overview" type="button">Overview</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-personal" type="button">Personal Information</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-applications" type="button">Applications</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-documents" type="button">Documents</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-payments" type="button">Payments</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-invoices" type="button">Invoices</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-appointments" type="button">Appointments</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-messages" type="button">Messages</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-notifications" type="button">Notifications</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-tasks" type="button">Tasks</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-notes" type="button">Notes</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-activity" type="button">Activity</button></li>
    </ul>

    <div class="tab-content">

        {{-- ============ OVERVIEW ============ --}}
        <div class="tab-pane fade show active" id="tab-overview">

            <div class="row g-3 mb-4">
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Active Application</div>
                        <div class="fw-semibold">{{ $primaryApplication?->university->name ?? 'None yet' }}</div>
                        <div class="small text-secondary">{{ $primaryApplication?->currentStatus->label ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Visa Status</div>
                        <div class="fw-semibold">{{ $primaryApplication?->visaApplication?->currentStatus?->label ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Documents</div>
                        <div class="fw-semibold">{{ $documentStats['completed'] }} / {{ $documentStats['total'] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Total Invoiced</div>
                        <div class="fw-semibold">{{ $financials['currency'] }} {{ number_format($financials['total'], 0) }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Paid</div>
                        <div class="fw-semibold text-success">{{ $financials['currency'] }} {{ number_format($financials['paid'], 0) }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Outstanding</div>
                        <div class="fw-semibold {{ $financials['balance'] > 0 ? 'text-danger' : 'text-success' }}">{{ $financials['currency'] }} {{ number_format($financials['balance'], 0) }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Open Tasks</div>
                        <div class="fw-semibold">{{ $openTasks }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Unread Messages</div>
                        <div class="fw-semibold {{ $unreadMessages > 0 ? 'text-warning' : '' }}">{{ $unreadMessages }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Open Tickets</div>
                        <div class="fw-semibold {{ $openTickets > 0 ? 'text-warning' : '' }}">{{ $openTickets }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Next Appointment</div>
                        <div class="fw-semibold small">{{ $nextAppointment?->scheduled_at->format('d M Y, g:ia') ?? 'None scheduled' }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Last Activity</div>
                        <div class="fw-semibold small">{{ $student->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Documents Awaiting Review — actionable --}}
                <div class="col-lg-6">
                    <div class="card stat-card p-4">
                        <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Documents Awaiting Review</h3>
                        @forelse($documentsAwaitingReview as $document)
                            <div class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <div class="fw-semibold small">{{ $document->name }}</div>
                                        <div class="text-secondary" style="font-size:.75rem;">{{ $document->category->name ?? '' }} · Uploaded {{ $document->uploaded_at?->format('d M Y') }}</div>
                                    </div>
                                    <a href="{{ route('admin.documents.preview', $document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View File</a>
                                </div>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('admin.students.documents.verify', [$student, $document]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $document->id }}">Reject</button>
                                </div>
                            </div>

                            <div class="modal fade" id="reject-{{ $document->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.students.documents.reject', [$student, $document]) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Reject "{{ $document->name }}"</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label fw-semibold">Reason (the student will see this)</label>
                                                <textarea name="reason" class="form-control" rows="3" required placeholder="e.g. Document is blurry, please re-scan and re-upload."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject Document</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-secondary mb-0">Nothing awaiting review.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Pending Payments — actionable --}}
                <div class="col-lg-6">
                    <div class="card stat-card p-4">
                        <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Payments Pending Confirmation</h3>
                        @forelse($pendingPayments as $payment)
                            <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div>
                                    <div class="fw-semibold small">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</div>
                                    <div class="text-secondary" style="font-size:.75rem;">{{ ucfirst($payment->method) }} · {{ $payment->invoice->invoice_number }} · {{ $payment->created_at->format('d M Y') }}</div>
                                    @if($payment->transactions->isNotEmpty())
                                        <div class="text-secondary" style="font-size:.75rem;">Ref: {{ $payment->transactions->first()->gateway_reference }}</div>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('admin.students.payments.confirm', [$student, $payment]) }}" onsubmit="return confirm('Confirm this payment of {{ $payment->currency }} {{ number_format($payment->amount, 2) }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-secondary mb-0">No payments pending confirmation.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ PERSONAL INFORMATION ============ --}}
        <div class="tab-pane fade" id="tab-personal">
            <div class="card stat-card p-4">
                @if($student->studentProfile)
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Personal</h4>
                            <dl class="row small mb-0">
                                <dt class="col-5 text-secondary fw-normal">Email</dt><dd class="col-7">{{ $student->email }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Phone</dt><dd class="col-7">{{ $student->phone ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Date of Birth</dt><dd class="col-7">{{ $student->studentProfile->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Gender</dt><dd class="col-7">{{ $student->studentProfile->gender ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Nationality</dt><dd class="col-7">{{ $student->studentProfile->nationality ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Address</dt><dd class="col-7">{{ $student->studentProfile->address_line ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">City / Country</dt><dd class="col-7">{{ $student->studentProfile->city }}{{ $student->studentProfile->city && $student->studentProfile->country ? ', ' : '' }}{{ $student->studentProfile->country }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Academic</h4>
                            <dl class="row small mb-4">
                                <dt class="col-5 text-secondary fw-normal">Highest Qualification</dt><dd class="col-7">{{ $student->studentProfile->highest_qualification ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Institution</dt><dd class="col-7">{{ $student->studentProfile->institution ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Graduation Year</dt><dd class="col-7">{{ $student->studentProfile->graduation_year ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Field of Study</dt><dd class="col-7">{{ $student->studentProfile->field_of_study ?? '—' }}</dd>
                            </dl>
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Passport</h4>
                            <dl class="row small mb-0">
                                <dt class="col-5 text-secondary fw-normal">Number</dt><dd class="col-7">{{ $student->studentProfile->passport_number ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Expiry</dt><dd class="col-7">{{ $student->studentProfile->passport_expiry_date?->format('d M Y') ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>
                @else
                    <p class="text-secondary mb-0">This student hasn't completed their profile yet.</p>
                @endif
            </div>
        </div>

        {{-- ============ APPLICATIONS (+ Documents/Admission/Visa per application) ============ --}}
        <div class="tab-pane fade" id="tab-applications">
            @forelse($applications as $application)
                @php
                    $nextAppStatuses = app(\App\Services\ApplicationStatusService::class)->allowedNextStatuses($application);
                    $nextVisaStatuses = $application->visaApplication ? app(\App\Services\ApplicationStatusService::class)->allowedNextStatuses($application->visaApplication) : collect();
                @endphp
                <div class="card stat-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h3 class="h6 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;">{{ $application->university->name }} — {{ $application->course->name }}</h3>
                            <div class="text-secondary small">{{ $application->reference_number }} · {{ $application->intake ?? 'No intake set' }}</div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary-emphasis fs-6">{{ $application->currentStatus->label }}</span>
                    </div>

                    <div class="row g-4">
                        {{-- Change Status --}}
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Change Status</h4>
                            @if($nextAppStatuses->isNotEmpty())
                                <form method="POST" action="{{ route('admin.students.applications.status', [$student, $application]) }}" class="d-flex gap-2 mb-2">
                                    @csrf
                                    <select name="to_status_id" class="form-select form-select-sm" required>
                                        <option value="">Move to...</option>
                                        @foreach($nextAppStatuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary text-nowrap">Update</button>
                                </form>
                            @else
                                <p class="text-secondary small">This application is in a terminal status — no further transitions.</p>
                            @endif
                        </div>

                        {{-- Assign Officer --}}
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Assigned Officer</h4>
                            <form method="POST" action="{{ route('admin.students.applications.assign_officer', [$student, $application]) }}" class="d-flex gap-2">
                                @csrf
                                <select name="officer_id" class="form-select form-select-sm" required>
                                    <option value="">Select officer...</option>
                                    @foreach($officers as $officer)
                                        <option value="{{ $officer->id }}" @selected($application->assigned_officer_id === $officer->id)>{{ $officer->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">Assign</button>
                            </form>
                        </div>

                        {{-- Admission --}}
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Admission</h4>
                            <form method="POST" action="{{ route('admin.students.applications.admission', [$student, $application]) }}">
                                @csrf
                                <div class="d-flex gap-2 mb-2">
                                    <select name="decision" class="form-select form-select-sm" required>
                                        @foreach(['pending', 'offered', 'accepted', 'rejected'] as $decision)
                                            <option value="{{ $decision }}" @selected($application->admission?->decision === $decision)>{{ ucfirst($decision) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">Save</button>
                                </div>
                                <textarea name="conditions" class="form-control form-control-sm" rows="2" placeholder="Conditions (optional)">{{ $application->admission?->conditions }}</textarea>
                            </form>
                        </div>

                        {{-- Visa --}}
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Visa</h4>
                            <form method="POST" action="{{ route('admin.students.applications.visa', [$student, $application]) }}">
                                @csrf
                                @if(! $application->visaApplication)
                                    <input type="text" name="destination_country" class="form-control form-control-sm mb-2" placeholder="Destination country" value="{{ $application->university->country }}" required>
                                @else
                                    <input type="hidden" name="existing_visa" value="1">
                                    <div class="small text-secondary mb-2">Current: {{ $application->visaApplication->currentStatus->label }}</div>
                                @endif
                                <div class="d-flex gap-2 mb-2">
                                    @if($nextVisaStatuses->isNotEmpty())
                                        <select name="to_status_id" class="form-select form-select-sm">
                                            <option value="">Move to...</option>
                                            @foreach($nextVisaStatuses as $status)
                                                <option value="{{ $status->id }}">{{ $status->label }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">{{ $application->visaApplication ? 'Update' : 'Start Visa' }}</button>
                                </div>
                                <input type="datetime-local" name="embassy_appointment_at" class="form-control form-control-sm">
                            </form>
                        </div>
                    </div>

                    {{-- Documents for this application --}}
                    <hr>
                    <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Documents ({{ $application->documents->count() }})</h4>
                    @forelse($application->documents as $document)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <span class="fw-semibold small">{{ $document->name }}</span>
                                <span class="badge ms-1 {{ match($document->status) { 'verified' => 'bg-success-subtle text-success-emphasis', 'rejected' => 'bg-danger-subtle text-danger-emphasis', 'under_review' => 'bg-primary-subtle text-primary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucwords(str_replace('_', ' ', $document->status)) }}</span>
                            </div>
                            <div class="d-flex gap-1">
                                @if($document->file_path)
                                    <a href="{{ route('admin.documents.preview', $document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                                @endif
                                @if($document->status === 'under_review')
                                    <form method="POST" action="{{ route('admin.students.documents.verify', [$student, $document]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-app-{{ $document->id }}">Reject</button>
                                @endif
                            </div>
                        </div>
                        <div class="modal fade" id="reject-app-{{ $document->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.students.documents.reject', [$student, $document]) }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Reject "{{ $document->name }}"</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea name="reason" class="form-control" rows="3" required placeholder="Reason the student will see"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-secondary small mb-0">No documents tied to this application yet.</p>
                    @endforelse
                </div>
            @empty
                <div class="card stat-card p-5 text-center text-secondary">This student has no applications yet.</div>
            @endforelse
        </div>

        {{-- ============ DOCUMENTS (full vault) ============ --}}
        <div class="tab-pane fade" id="tab-documents">
            @forelse($allDocuments as $category => $documents)
                <div class="card stat-card mb-4">
                    <div class="p-3 border-bottom bg-light">
                        <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">{{ $category }}</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Document</th><th>Application</th><th>Status</th><th>Uploaded</th><th>Verified By</th><th class="text-end">Action</th></tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td class="fw-semibold small">{{ $document->name }}</td>
                                        <td class="small text-secondary">{{ $document->studyApplication?->university->name ?? '—' }}</td>
                                        <td><span class="badge {{ match($document->status) { 'verified' => 'bg-success-subtle text-success-emphasis', 'rejected' => 'bg-danger-subtle text-danger-emphasis', 'under_review' => 'bg-primary-subtle text-primary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucwords(str_replace('_', ' ', $document->status)) }}</span></td>
                                        <td class="small text-secondary">{{ $document->uploaded_at?->format('d M Y') ?? '—' }}</td>
                                        <td class="small text-secondary">{{ $document->verifiedBy?->name ?? '—' }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                @if($document->file_path)
                                                    <a href="{{ route('admin.documents.preview', $document) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                                                @endif
                                                @if($document->status === 'under_review')
                                                    <form method="POST" action="{{ route('admin.students.documents.verify', [$student, $document]) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-vault-{{ $document->id }}">Reject</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="reject-vault-{{ $document->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.students.documents.reject', [$student, $document]) }}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Reject "{{ $document->name }}"</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <textarea name="reason" class="form-control" rows="3" required placeholder="Reason the student will see"></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card stat-card p-5 text-center text-secondary">No documents yet.</div>
            @endforelse
        </div>

        {{-- ============ PAYMENTS (full) ============ --}}
        <div class="tab-pane fade" id="tab-payments">
            <div class="card stat-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>Amount</th><th>Method</th><th>Invoice</th><th>Status</th><th>Confirmed By</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($allPayments as $payment)
                                <tr>
                                    <td class="small">{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</td>
                                    <td class="fw-semibold small">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                    <td class="small text-capitalize">{{ $payment->method }}</td>
                                    <td class="small">{{ $payment->invoice->invoice_number }}</td>
                                    <td><span class="badge {{ $payment->status === 'confirmed' ? 'bg-success-subtle text-success-emphasis' : ($payment->status === 'refunded' ? 'bg-secondary-subtle text-secondary-emphasis' : 'bg-warning-subtle text-warning-emphasis') }}">{{ ucfirst($payment->status) }}</span></td>
                                    <td class="small text-secondary">{{ $payment->confirmedBy?->name ?? '—' }}</td>
                                    <td class="text-end">
                                        @if($payment->status === 'pending')
                                            <form method="POST" action="{{ route('admin.students.payments.confirm', [$student, $payment]) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                            </form>
                                        @elseif($payment->status === 'confirmed')
                                            <form method="POST" action="{{ route('admin.students.payments.refund', [$student, $payment]) }}" class="d-inline" onsubmit="return confirm('Refund this payment?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Refund</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-secondary py-4">No payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ INVOICES (full + create) ============ --}}
        <div class="tab-pane fade" id="tab-invoices">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">+ Create Invoice</button>
            </div>
            <div class="card stat-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Invoice</th><th>Description</th><th>Total</th><th>Paid</th><th>Due Date</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($allInvoices as $invoice)
                                <tr>
                                    <td class="fw-semibold small">{{ $invoice->invoice_number }}</td>
                                    <td class="small">{{ $invoice->description }}</td>
                                    <td class="small">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                                    <td class="small text-success">{{ $invoice->currency }} {{ number_format($invoice->amount_paid, 2) }}</td>
                                    <td class="small">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                    <td><span class="badge {{ match($invoice->status) { 'paid' => 'bg-success-subtle text-success-emphasis', 'overdue' => 'bg-danger-subtle text-danger-emphasis', 'cancelled' => 'bg-secondary-subtle text-secondary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucfirst($invoice->status) }}</span></td>
                                    <td class="text-end">
                                        @if($invoice->status === 'draft')
                                            <form method="POST" action="{{ route('admin.students.invoices.send', [$student, $invoice]) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Send</button>
                                            </form>
                                        @endif
                                        @if(! in_array($invoice->status, ['paid', 'cancelled']))
                                            <form method="POST" action="{{ route('admin.students.invoices.cancel', [$student, $invoice]) }}" class="d-inline" onsubmit="return confirm('Cancel this invoice?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-secondary py-4">No invoices yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Create Invoice modal --}}
            <div class="modal fade" id="createInvoiceModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.students.invoices.store', $student) }}" id="createInvoiceForm">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Create Invoice</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-semibold">Description</label>
                                        <input type="text" name="description" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Currency</label>
                                        <input type="text" name="currency" class="form-control" value="KES" maxlength="3" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Due Date</label>
                                        <input type="date" name="due_date" class="form-control">
                                    </div>
                                </div>

                                @if($applications->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Related Application (optional)</label>
                                        <select name="study_application_id" class="form-select">
                                            <option value="">Not tied to a specific application</option>
                                            @foreach($applications as $application)
                                                <option value="{{ $application->id }}">{{ $application->university->name }} — {{ $application->course->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <label class="form-label small fw-semibold">Line Items</label>
                                <div id="invoiceItems">
                                    <div class="row g-2 mb-2 invoice-item-row">
                                        <div class="col-6"><input type="text" name="items[0][description]" class="form-control form-control-sm" placeholder="Description" required></div>
                                        <div class="col-2"><input type="number" name="items[0][quantity]" class="form-control form-control-sm" value="1" min="1" required></div>
                                        <div class="col-3"><input type="number" name="items[0][unit_price]" class="form-control form-control-sm" placeholder="Unit price" step="0.01" min="0" required></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="addInvoiceItem">+ Add Line Item</button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create Invoice (Draft)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                (function () {
                    var itemIndex = 1;
                    var addBtn = document.getElementById('addInvoiceItem');
                    var container = document.getElementById('invoiceItems');
                    if (addBtn) {
                        addBtn.addEventListener('click', function () {
                            var row = document.createElement('div');
                            row.className = 'row g-2 mb-2 invoice-item-row';
                            row.innerHTML =
                                '<div class="col-6"><input type="text" name="items[' + itemIndex + '][description]" class="form-control form-control-sm" placeholder="Description" required></div>' +
                                '<div class="col-2"><input type="number" name="items[' + itemIndex + '][quantity]" class="form-control form-control-sm" value="1" min="1" required></div>' +
                                '<div class="col-3"><input type="number" name="items[' + itemIndex + '][unit_price]" class="form-control form-control-sm" placeholder="Unit price" step="0.01" min="0" required></div>';
                            container.appendChild(row);
                            itemIndex++;
                        });
                    }
                })();
            </script>
        </div>

        {{-- ============ APPOINTMENTS (full) ============ --}}
        <div class="tab-pane fade" id="tab-appointments">
            <div class="card stat-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>Type</th><th>Mode</th><th>Staff</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr>
                                    <td class="small">{{ $appointment->scheduled_at->format('d M Y, g:ia') }}</td>
                                    <td class="small fw-semibold">{{ $appointment->type }}</td>
                                    <td class="small text-capitalize">{{ $appointment->mode }}</td>
                                    <td class="small">{{ $appointment->staff?->name ?? '—' }}</td>
                                    <td><span class="badge {{ match($appointment->status) { 'confirmed' => 'bg-success-subtle text-success-emphasis', 'completed' => 'bg-secondary-subtle text-secondary-emphasis', 'cancelled' => 'bg-danger-subtle text-danger-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucfirst($appointment->status) }}</span></td>
                                    <td class="text-end">
                                        @if($appointment->status === 'requested')
                                            <form method="POST" action="{{ route('admin.students.appointments.confirm', [$student, $appointment]) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                            </form>
                                        @endif
                                        @if($appointment->status === 'confirmed' && $appointment->scheduled_at->isPast())
                                            <form method="POST" action="{{ route('admin.students.appointments.complete', [$student, $appointment]) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Mark Completed</button>
                                            </form>
                                        @endif
                                        @if(in_array($appointment->status, ['requested', 'confirmed']))
                                            <form method="POST" action="{{ route('admin.students.appointments.cancel', [$student, $appointment]) }}" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-4">No appointments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ MESSAGES (support tickets) ============ --}}
        <div class="tab-pane fade" id="tab-messages">
            @forelse($tickets as $ticket)
                <div class="card stat-card mb-3">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="fw-semibold">{{ $ticket->subject }}</span>
                            <span class="text-secondary small ms-2">{{ $ticket->ticket_number }} · {{ $ticket->category }}</span>
                        </div>
                        <form method="POST" action="{{ route('admin.students.tickets.status', [$student, $ticket]) }}" class="d-flex align-items-center gap-2">
                            @csrf
                            <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                                @foreach(['open', 'in_progress', 'waiting_for_student', 'resolved', 'closed'] as $status)
                                    <option value="{{ $status }}" @selected($ticket->status === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="p-3">
                        @foreach($ticket->messages as $message)
                            @php $isStaff = $message->user_id !== $student->id; @endphp
                            <div class="d-flex {{ $isStaff ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                                <div style="max-width:75%;">
                                    <div class="small text-secondary mb-1 {{ $isStaff ? 'text-end' : '' }}">{{ $message->author->name }} · {{ $message->created_at->format('d M, g:ia') }}</div>
                                    <div class="p-2 rounded-3 small {{ $isStaff ? 'bg-primary text-white' : 'bg-light' }}">{{ $message->body }}</div>
                                </div>
                            </div>
                        @endforeach

                        @if($ticket->status !== 'closed')
                            <form method="POST" action="{{ route('admin.students.tickets.reply', [$student, $ticket]) }}" class="mt-3 d-flex gap-2">
                                @csrf
                                <input type="text" name="body" class="form-control form-control-sm" placeholder="Type a reply..." required>
                                <button type="submit" class="btn btn-sm btn-primary text-nowrap">Reply</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card stat-card p-5 text-center text-secondary">No support tickets.</div>
            @endforelse
        </div>

        {{-- ============ NOTIFICATIONS ============ --}}
        <div class="tab-pane fade" id="tab-notifications">
            <div class="card stat-card">
                @forelse($notifications as $notification)
                    <div class="d-flex align-items-start gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <i class="fa-solid fa-{{ $notification->data['icon'] ?? 'bell' }} text-primary mt-1"></i>
                        <div>
                            <div class="fw-semibold small">{{ $notification->data['title'] ?? 'Notification' }}</div>
                            <div class="text-secondary small">{{ $notification->data['body'] ?? '' }}</div>
                            <div class="text-secondary" style="font-size:.7rem;">{{ $notification->created_at->format('d M Y, g:ia') }} {{ $notification->read_at ? '· Read' : '· Unread' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-secondary">No notifications sent to this student yet.</div>
                @endforelse
            </div>
        </div>

        {{-- ============ TASKS ============ --}}
        <div class="tab-pane fade" id="tab-tasks">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card stat-card">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Title</th><th>Assigned To</th><th>Due</th><th>Priority</th><th>Status</th><th class="text-end">Action</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($tasks as $task)
                                        <tr>
                                            <td class="small fw-semibold">{{ $task->title }}</td>
                                            <td class="small">{{ $task->assignedTo->name }}</td>
                                            <td class="small">{{ $task->due_date?->format('d M Y') ?? '—' }}</td>
                                            <td class="small text-capitalize">{{ $task->priority }}</td>
                                            <td><span class="badge {{ $task->status === 'completed' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">{{ ucfirst($task->status) }}</span></td>
                                            <td class="text-end">
                                                @if($task->status !== 'completed')
                                                    <form method="POST" action="{{ route('admin.students.tasks.complete', [$student, $task]) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success">Complete</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-secondary py-4">No tasks yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card stat-card p-4">
                        <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">New Task</h3>
                        <form method="POST" action="{{ route('admin.students.tasks.store', $student) }}">
                            @csrf
                            <div class="mb-2"><input type="text" name="title" class="form-control form-control-sm" placeholder="Task title" required></div>
                            <div class="mb-2"><textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Description (optional)"></textarea></div>
                            <div class="mb-2">
                                <select name="assigned_to" class="form-select form-select-sm" required>
                                    <option value="">Assign to...</option>
                                    @foreach($officers as $officer)
                                        <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6"><input type="date" name="due_date" class="form-control form-control-sm"></div>
                                <div class="col-6">
                                    <select name="priority" class="form-select form-select-sm">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Create Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ NOTES ============ --}}
        <div class="tab-pane fade" id="tab-notes">
            <div class="row g-4">
                <div class="col-lg-7">
                    @forelse($notes as $note)
                        <div class="card stat-card p-3 mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold small">{{ $note->createdBy->name }}</span>
                                <span class="text-secondary" style="font-size:.75rem;">{{ $note->created_at->format('d M Y, g:ia') }}</span>
                            </div>
                            <p class="small mb-0">{{ $note->body }}</p>
                        </div>
                    @empty
                        <div class="card stat-card p-5 text-center text-secondary">No notes yet.</div>
                    @endforelse
                </div>
                <div class="col-lg-5">
                    <div class="card stat-card p-4">
                        <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Add Note</h3>
                        <form method="POST" action="{{ route('admin.students.notes.store', $student) }}">
                            @csrf
                            <textarea name="body" class="form-control form-control-sm mb-3" rows="4" placeholder="Internal note..." required></textarea>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Add Note</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ ACTIVITY ============ --}}
        <div class="tab-pane fade" id="tab-activity">
            <div class="card stat-card p-4">
                @forelse($activities as $activity)
                    <div class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="small">
                            <span class="fw-semibold">{{ $activity->causer?->name ?? 'System' }}</span>
                            {{ $activity->description }}
                            <span class="text-secondary">({{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }})</span>
                        </div>
                        <div class="text-secondary" style="font-size:.75rem;">{{ $activity->created_at->format('d M Y, g:ia') }}</div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No recorded activity yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</x-admin-layout>
