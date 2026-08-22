<x-admin-layout title="Job Seeker Workspace">

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

    <a href="{{ route('admin.job-seekers.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Job Seekers</a>

    {{-- Workspace header --}}
    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $jobSeeker->name }}</h2>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-primary-subtle text-primary-emphasis">Job Seeker</span>
                    <span class="badge {{ $jobSeeker->is_active ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                        {{ $jobSeeker->is_active ? 'Active' : 'Suspended' }}
                    </span>
                    @if($jobSeeker->jobSeekerProfile?->professional_title)
                        <span class="text-secondary small">{{ $jobSeeker->jobSeekerProfile->professional_title }}</span>
                    @endif
                    <span class="text-secondary small">Assigned Officer: {{ $primaryApplication?->assignedOfficer?->name ?? 'Not yet assigned' }}</span>
                </div>
            </div>
            <div class="text-end">
                <div class="small text-secondary">Profile Completion</div>
                <div class="fw-semibold fs-5">{{ $jobSeeker->jobSeekerProfile?->profile_completion_percent ?? 0 }}%</div>
                @can('updateJobSeeker', $jobSeeker)
                    <a href="{{ route('admin.job-seekers.edit', $jobSeeker) }}" class="btn btn-sm btn-outline-secondary mt-1">Edit Account</a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tab navigation — Applications/Appointments/Support/Tasks/Notes/Activity
         land here in subsequent deliveries, same incremental pattern used
         throughout Stage 2. --}}
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
                        <div class="fw-semibold small">{{ $primaryApplication?->jobPosting->title ?? 'None yet' }}</div>
                        <div class="small text-secondary">{{ $primaryApplication?->currentStatus->label ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Total Applications</div>
                        <div class="fw-semibold">{{ $applications->count() }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Job Offers</div>
                        <div class="fw-semibold">{{ $offersCount }}</div>
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
                        <div class="stat-label">Next Appointment</div>
                        <div class="fw-semibold small">{{ $nextAppointment?->scheduled_at->format('d M Y, g:ia') ?? 'None scheduled' }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Last Activity</div>
                        <div class="fw-semibold small">{{ $jobSeeker->updated_at->diffForHumans() }}</div>
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
                                    <form method="POST" action="{{ route('admin.job-seekers.documents.verify', [$jobSeeker, $document]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reject-ov-{{ $document->id }}">Reject</button>
                                </div>
                            </div>

                            <div class="modal fade" id="reject-ov-{{ $document->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.job-seekers.documents.reject', [$jobSeeker, $document]) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Reject "{{ $document->name }}"</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label fw-semibold">Reason (the candidate will see this)</label>
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
                                <form method="POST" action="{{ route('admin.job-seekers.payments.confirm', [$jobSeeker, $payment]) }}" onsubmit="return confirm('Confirm this payment of {{ $payment->currency }} {{ number_format($payment->amount, 2) }}?');">
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

            {{-- Upcoming Appointments — read-only summary this delivery; full
                 management (confirm/cancel/schedule) lands with the
                 Appointments tab in a later delivery. --}}
            <div class="card stat-card p-4 mt-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Upcoming Appointments</h3>
                @php
                    $upcomingAppointments = $appointments->filter(fn ($a) => $a->scheduled_at->isFuture() && in_array($a->status, ['requested', 'confirmed']))->sortBy('scheduled_at');
                @endphp
                @forelse($upcomingAppointments as $appointment)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold small">{{ $appointment->type }}</div>
                            <div class="text-secondary" style="font-size:.75rem;">{{ $appointment->scheduled_at->format('d M Y, g:ia') }} · {{ ucfirst($appointment->mode) }}{{ $appointment->staff ? ' · '.$appointment->staff->name : '' }}</div>
                        </div>
                        <span class="badge {{ $appointment->status === 'confirmed' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">{{ ucfirst($appointment->status) }}</span>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No upcoming appointments.</p>
                @endforelse
            </div>
        </div>

        {{-- ============ PERSONAL INFORMATION ============ --}}
        <div class="tab-pane fade" id="tab-personal">
            <div class="d-flex justify-content-end mb-3">
                @can('editJobSeekerProfile', $jobSeeker)
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPersonalModal">
                        {{ $jobSeeker->jobSeekerProfile ? 'Edit' : 'Add' }} Personal Information
                    </button>
                @endcan
            </div>
            <div class="card stat-card p-4">
                @if($jobSeeker->jobSeekerProfile)
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Personal</h4>
                            <dl class="row small mb-0">
                                <dt class="col-5 text-secondary fw-normal">Email</dt><dd class="col-7">{{ $jobSeeker->email }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Phone</dt><dd class="col-7">{{ $jobSeeker->phone ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Date of Birth</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Gender</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->gender ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Nationality</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->nationality ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Address</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->address_line ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">City / Country</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->city }}{{ $jobSeeker->jobSeekerProfile->city && $jobSeeker->jobSeekerProfile->country ? ', ' : '' }}{{ $jobSeeker->jobSeekerProfile->country }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Professional</h4>
                            <dl class="row small mb-4">
                                <dt class="col-5 text-secondary fw-normal">Title</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->professional_title ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Industry</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->industry ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Skills</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->skills ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Languages</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->languages ?? '—' }}</dd>
                            </dl>
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Passport</h4>
                            <dl class="row small mb-0">
                                <dt class="col-5 text-secondary fw-normal">Number</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->passport_number ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Country of Issue</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->passport_country ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Issue Date</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->passport_issue_date?->format('d M Y') ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Expiry</dt><dd class="col-7">{{ $jobSeeker->jobSeekerProfile->passport_expiry_date?->format('d M Y') ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>

                    <hr>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Employment History</h4>
                            @forelse($jobSeeker->jobSeekerProfile->experiences as $experience)
                                <div class="d-flex justify-content-between align-items-start small py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <strong>{{ $experience->occupation }}</strong> — {{ $experience->employer ?? '—' }}
                                        @if($experience->years_of_experience) ({{ $experience->years_of_experience }} yrs) @endif
                                    </div>
                                    @can('editJobSeekerProfile', $jobSeeker)
                                        <form method="POST" action="{{ route('admin.job-seekers.experience.destroy', [$jobSeeker, $experience]) }}" onsubmit="return confirm('Remove this experience?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">Remove</button>
                                        </form>
                                    @endcan
                                </div>
                            @empty
                                <p class="text-secondary small mb-0">None added.</p>
                            @endforelse

                            @can('editJobSeekerProfile', $jobSeeker)
                                <form method="POST" action="{{ route('admin.job-seekers.experience.store', $jobSeeker) }}" class="row g-2 mt-3 pt-3 border-top">
                                    @csrf
                                    <div class="col-12"><input type="text" name="occupation" class="form-control form-control-sm" placeholder="Occupation" required></div>
                                    <div class="col-7"><input type="text" name="employer" class="form-control form-control-sm" placeholder="Employer"></div>
                                    <div class="col-5"><input type="number" step="0.5" name="years_of_experience" class="form-control form-control-sm" placeholder="Years"></div>
                                    <div class="col-12"><button type="submit" class="btn btn-sm btn-outline-primary w-100">+ Add Experience</button></div>
                                </form>
                            @endcan
                        </div>
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Education</h4>
                            @forelse($jobSeeker->jobSeekerProfile->educations as $education)
                                <div class="d-flex justify-content-between align-items-start small py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $education->level)) }}</strong> — {{ $education->institution ?? '—' }}
                                        @if($education->course) · {{ $education->course }} @endif
                                        @if($education->graduation_year) ({{ $education->graduation_year }}) @endif
                                    </div>
                                    @can('editJobSeekerProfile', $jobSeeker)
                                        <form method="POST" action="{{ route('admin.job-seekers.education.destroy', [$jobSeeker, $education]) }}" onsubmit="return confirm('Remove this education record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">Remove</button>
                                        </form>
                                    @endcan
                                </div>
                            @empty
                                <p class="text-secondary small mb-0">None added.</p>
                            @endforelse

                            @can('editJobSeekerProfile', $jobSeeker)
                                <form method="POST" action="{{ route('admin.job-seekers.education.store', $jobSeeker) }}" class="row g-2 mt-3 pt-3 border-top">
                                    @csrf
                                    <div class="col-12">
                                        <select name="level" class="form-select form-select-sm" required>
                                            <option value="">Select level</option>
                                            @foreach(['primary' => 'Primary', 'secondary' => 'Secondary', 'diploma' => 'Diploma', 'bachelor' => "Bachelor's Degree", 'master' => "Master's Degree", 'not_applicable' => 'Not Applicable'] as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-7"><input type="text" name="institution" class="form-control form-control-sm" placeholder="Institution"></div>
                                    <div class="col-5"><input type="number" name="graduation_year" class="form-control form-control-sm" placeholder="Year"></div>
                                    <div class="col-12"><input type="text" name="course" class="form-control form-control-sm" placeholder="Course"></div>
                                    <div class="col-12"><button type="submit" class="btn btn-sm btn-outline-primary w-100">+ Add Education</button></div>
                                </form>
                            @endcan
                        </div>
                    </div>
                @else
                    <p class="text-secondary mb-0">This candidate hasn't completed their profile yet — use "Add Personal Information" above to fill it in on their behalf.</p>
                @endif
            </div>

            {{-- Edit/Add Personal Information modal --}}
            @can('editJobSeekerProfile', $jobSeeker)
                <div class="modal fade" id="editPersonalModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.job-seekers.profile.update', $jobSeeker) }}">
                                @csrf
                                @method('PATCH')
                                <div class="modal-header">
                                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">{{ $jobSeeker->jobSeekerProfile ? 'Edit' : 'Add' }} Personal Information</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Personal</h6>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Phone</label>
                                            <input type="tel" name="phone" class="form-control form-control-sm" value="{{ old('phone', $jobSeeker->phone) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Date of Birth</label>
                                            <input type="date" name="date_of_birth" class="form-control form-control-sm" value="{{ old('date_of_birth', $jobSeeker->jobSeekerProfile?->date_of_birth?->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Gender</label>
                                            <select name="gender" class="form-select form-select-sm">
                                                <option value="">Select</option>
                                                <option value="Male" @selected(old('gender', $jobSeeker->jobSeekerProfile?->gender) === 'Male')>Male</option>
                                                <option value="Female" @selected(old('gender', $jobSeeker->jobSeekerProfile?->gender) === 'Female')>Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Nationality</label>
                                            <input type="text" name="nationality" class="form-control form-control-sm" value="{{ old('nationality', $jobSeeker->jobSeekerProfile?->nationality) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Address</label>
                                            <input type="text" name="address_line" class="form-control form-control-sm" value="{{ old('address_line', $jobSeeker->jobSeekerProfile?->address_line) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">City</label>
                                            <input type="text" name="city" class="form-control form-control-sm" value="{{ old('city', $jobSeeker->jobSeekerProfile?->city) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">Country</label>
                                            <input type="text" name="country" class="form-control form-control-sm" value="{{ old('country', $jobSeeker->jobSeekerProfile?->country) }}">
                                        </div>
                                    </div>

                                    <h6 class="fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Professional</h6>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Professional Title</label>
                                            <input type="text" name="professional_title" class="form-control form-control-sm" value="{{ old('professional_title', $jobSeeker->jobSeekerProfile?->professional_title) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Industry</label>
                                            <input type="text" name="industry" class="form-control form-control-sm" value="{{ old('industry', $jobSeeker->jobSeekerProfile?->industry) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Skills</label>
                                            <input type="text" name="skills" class="form-control form-control-sm" value="{{ old('skills', $jobSeeker->jobSeekerProfile?->skills) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Languages</label>
                                            <input type="text" name="languages" class="form-control form-control-sm" value="{{ old('languages', $jobSeeker->jobSeekerProfile?->languages) }}">
                                        </div>
                                    </div>

                                    <h6 class="fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Passport</h6>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Passport Number</label>
                                            <input type="text" name="passport_number" class="form-control form-control-sm" value="{{ old('passport_number', $jobSeeker->jobSeekerProfile?->passport_number) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Country of Issue</label>
                                            <input type="text" name="passport_country" class="form-control form-control-sm" value="{{ old('passport_country', $jobSeeker->jobSeekerProfile?->passport_country) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Issue Date</label>
                                            <input type="date" name="passport_issue_date" class="form-control form-control-sm" value="{{ old('passport_issue_date', $jobSeeker->jobSeekerProfile?->passport_issue_date?->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Expiry Date</label>
                                            <input type="date" name="passport_expiry_date" class="form-control form-control-sm" value="{{ old('passport_expiry_date', $jobSeeker->jobSeekerProfile?->passport_expiry_date?->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        {{-- ============ APPLICATIONS (+ Interview/Offer/Visa/Documents per application) ============ --}}
        <div class="tab-pane fade" id="tab-applications">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createApplicationModal">+ Create Application</button>
            </div>
            @forelse($applications as $application)
                @php
                    $nextAppStatuses = app(\App\Services\ApplicationStatusService::class)->allowedNextStatuses($application);
                    $nextVisaStatuses = $application->visaApplication ? app(\App\Services\ApplicationStatusService::class)->allowedNextStatuses($application->visaApplication) : collect();
                @endphp
                <div class="card stat-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h3 class="h6 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;">{{ $application->jobPosting->title }} — {{ $application->jobPosting->country }}</h3>
                            <div class="text-secondary small">{{ $application->reference_number }} · Applied {{ $application->applied_at?->format('d M Y') }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary-emphasis fs-6">{{ $application->currentStatus->label }}</span>
                            @can('delete', $application)
                                <form method="POST" action="{{ route('admin.job-seekers.applications.destroy', [$jobSeeker, $application]) }}" onsubmit="return confirm('Delete this application? This cannot be undone from the UI.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- Change Status --}}
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Change Status</h4>
                            @if($nextAppStatuses->isNotEmpty())
                                <form method="POST" action="{{ route('admin.job-seekers.applications.status', [$jobSeeker, $application]) }}" class="d-flex gap-2 mb-2">
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
                            <form method="POST" action="{{ route('admin.job-seekers.applications.assign_officer', [$jobSeeker, $application]) }}" class="d-flex gap-2">
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

                        {{-- Schedule Interview --}}
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Interview</h4>
                            <form method="POST" action="{{ route('admin.job-seekers.applications.interview', [$jobSeeker, $application]) }}">
                                @csrf
                                <div class="row g-2 mb-2">
                                    <div class="col-7">
                                        <input type="datetime-local" name="scheduled_at" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-5">
                                        <select name="mode" class="form-select form-select-sm" required>
                                            <option value="video">Video</option>
                                            <option value="physical">In Person</option>
                                            <option value="phone">Phone</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-8">
                                        <input type="url" name="meeting_link" class="form-control form-control-sm" placeholder="Meeting link (if video)">
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Schedule</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Job Offer --}}
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Job Offer</h4>
                            <form method="POST" action="{{ route('admin.job-seekers.applications.offer', [$jobSeeker, $application]) }}">
                                @csrf
                                <div class="row g-2 mb-2">
                                    <div class="col-8">
                                        <input type="number" step="0.01" name="salary" class="form-control form-control-sm" placeholder="Salary" value="{{ $application->offer?->salary }}" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="text" name="currency" class="form-control form-control-sm" placeholder="Currency" maxlength="3" value="{{ $application->offer?->currency ?? $application->jobPosting->currency }}" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <input type="text" name="contract_duration" class="form-control form-control-sm" placeholder="Contract duration" value="{{ $application->offer?->contract_duration }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $application->offer?->start_date?->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">{{ $application->offer ? 'Update Offer' : 'Create Offer' }}</button>
                                    @if($application->offer && ! $application->offer->sent_at)
                                        <button type="submit" formaction="{{ route('admin.job-seekers.applications.offer.send', [$jobSeeker, $application]) }}" class="btn btn-sm btn-success text-nowrap">Send Offer</button>
                                    @elseif($application->offer?->sent_at)
                                        <span class="badge bg-success-subtle text-success-emphasis align-self-center">Sent {{ $application->offer->sent_at->format('d M') }} · {{ ucfirst($application->offer->status) }}</span>
                                    @endif
                                </div>
                            </form>
                        </div>

                        {{-- Visa --}}
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Visa</h4>
                            <form method="POST" action="{{ route('admin.job-seekers.applications.visa', [$jobSeeker, $application]) }}">
                                @csrf
                                @if(! $application->visaApplication)
                                    <input type="text" name="destination_country" class="form-control form-control-sm mb-2" placeholder="Destination country" value="{{ $application->jobPosting->country }}" required>
                                @else
                                    <input type="hidden" name="existing_visa" value="1">
                                    <div class="small text-secondary mb-2">
                                        Current: {{ $application->visaApplication->currentStatus->label }}
                                        @can('view', $application->visaApplication)
                                            · <a href="{{ route('admin.visa-management.show', $application->visaApplication) }}">View in Visa Management →</a>
                                        @endcan
                                    </div>
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
                            @include('admin.job-seekers.partials._document-actions', ['document' => $document, 'prefix' => 'app'])
                        </div>
                    @empty
                        <p class="text-secondary small mb-0">No documents tied to this application yet.</p>
                    @endforelse
                </div>
            @empty
                <div class="card stat-card p-5 text-center text-secondary">This candidate has no applications yet.</div>
            @endforelse

            {{-- Create Application modal --}}
            <div class="modal fade" id="createApplicationModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.job-seekers.applications.store', $jobSeeker) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Create Application</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label small fw-semibold">Job Posting</label>
                                <select name="job_posting_id" class="form-select form-select-sm" required>
                                    <option value="">Select a job posting...</option>
                                    @foreach(\App\Models\JobPosting::open()->orderBy('title')->get() as $posting)
                                        <option value="{{ $posting->id }}">{{ $posting->title }} — {{ $posting->country }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Job postings are managed under Job Posting Management — this only links the candidate to an existing one.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create Application</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ DOCUMENTS (full vault) ============ --}}
        <div class="tab-pane fade" id="tab-documents">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestDocumentModal">+ Request Document</button>
            </div>
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
                                        <td class="small text-secondary">{{ $document->jobApplication?->jobPosting->title ?? '—' }}</td>
                                        <td><span class="badge {{ match($document->status) { 'verified' => 'bg-success-subtle text-success-emphasis', 'rejected' => 'bg-danger-subtle text-danger-emphasis', 'under_review' => 'bg-primary-subtle text-primary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucwords(str_replace('_', ' ', $document->status)) }}</span></td>
                                        <td class="small text-secondary">{{ $document->uploaded_at?->format('d M Y') ?? '—' }}</td>
                                        <td class="small text-secondary">{{ $document->verifiedBy?->name ?? '—' }}</td>
                                        <td class="text-end">
                                            @include('admin.job-seekers.partials._document-actions', ['document' => $document, 'prefix' => 'vault'])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card stat-card p-5 text-center text-secondary">No documents yet.</div>
            @endforelse

            <div class="modal fade" id="requestDocumentModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.job-seekers.documents.request', $jobSeeker) }}">
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
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Document Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Professional License" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Related Application (optional)</label>
                                    <select name="job_application_id" class="form-select">
                                        <option value="">General — not tied to a specific application</option>
                                        @foreach($applications as $application)
                                            <option value="{{ $application->id }}">{{ $application->jobPosting->title }} — {{ $application->jobPosting->country }}</option>
                                        @endforeach
                                    </select>
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
        </div>

        {{-- ============ PAYMENTS (full) ============ --}}
        <div class="tab-pane fade" id="tab-payments">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">+ Record Payment</button>
            </div>
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
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($payment->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPayment-{{ $payment->id }}">Edit</button>
                                                <form method="POST" action="{{ route('admin.job-seekers.payments.confirm', [$jobSeeker, $payment]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                                </form>
                                            @elseif($payment->status === 'confirmed')
                                                <form method="POST" action="{{ route('admin.job-seekers.payments.refund', [$jobSeeker, $payment]) }}" class="d-inline" onsubmit="return confirm('Refund this payment?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Refund</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @if($payment->status === 'pending')
                                    <div class="modal fade" id="editPayment-{{ $payment->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.job-seekers.payments.update', [$jobSeeker, $payment]) }}">
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
                                                        <p class="text-secondary small mb-0">Only editable while pending — once confirmed, changing it would silently rewrite the financial trail.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr><td colspan="7" class="text-center text-secondary py-4">No payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="recordPaymentModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ $allInvoices->isNotEmpty() ? route('admin.job-seekers.invoices.payments.store', [$jobSeeker, $allInvoices->first()]) : '#' }}" id="recordPaymentForm">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Record Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @if($allInvoices->isEmpty())
                                    <p class="text-secondary small mb-0">This candidate has no invoices yet — create one first on the Invoices tab.</p>
                                @else
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Invoice</label>
                                        <select name="invoice_select" class="form-select form-select-sm" id="recordPaymentInvoice" required>
                                            @foreach($allInvoices as $invoice)
                                                <option value="{{ route('admin.job-seekers.invoices.payments.store', [$jobSeeker, $invoice]) }}" data-balance="{{ $invoice->balance }}" data-currency="{{ $invoice->currency }}">
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
                                        <input type="text" name="reference" class="form-control form-control-sm" placeholder="e.g. M-Pesa code, receipt number">
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="confirm_immediately" value="1" class="form-check-input" id="confirmImmediately">
                                        <label class="form-check-label small" for="confirmImmediately">Already received in full — confirm immediately</label>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                @if($allInvoices->isNotEmpty())
                                    <button type="submit" class="btn btn-primary">Record Payment</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                (function () {
                    var select = document.getElementById('recordPaymentInvoice');
                    var form = document.getElementById('recordPaymentForm');
                    if (select && form) {
                        select.addEventListener('change', function () {
                            form.action = this.value;
                        });
                        form.action = select.value;
                    }
                })();
            </script>
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
                            <tr><th>Invoice</th><th>Description</th><th>Amount</th><th>Due Date</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($allInvoices as $invoice)
                                <tr>
                                    <td class="fw-semibold small">{{ $invoice->invoice_number }}</td>
                                    <td class="small">{{ $invoice->description }}</td>
                                    <td class="small">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                                    <td class="small">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($invoice->status) {
                                                'paid' => 'bg-success-subtle text-success-emphasis',
                                                'sent' => 'bg-primary-subtle text-primary-emphasis',
                                                'cancelled' => 'bg-secondary-subtle text-secondary-emphasis',
                                                default => 'bg-warning-subtle text-warning-emphasis',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($invoice->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($invoice->status === 'draft')
                                                <form method="POST" action="{{ route('admin.job-seekers.invoices.send', [$jobSeeker, $invoice]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">Send</button>
                                                </form>
                                            @endif
                                            @if(!in_array($invoice->status, ['paid', 'cancelled']))
                                                <form method="POST" action="{{ route('admin.job-seekers.invoices.cancel', [$jobSeeker, $invoice]) }}" class="d-inline" onsubmit="return confirm('Cancel this invoice?');">
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

            <div class="modal fade" id="createInvoiceModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.job-seekers.invoices.store', $jobSeeker) }}" id="createInvoiceForm">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Create Invoice</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Description</label>
                                        <input type="text" name="description" class="form-control form-control-sm" placeholder="e.g. Application Processing Fee" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Currency</label>
                                        <input type="text" name="currency" class="form-control form-control-sm" value="KES" maxlength="3" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Due Date</label>
                                        <input type="date" name="due_date" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Related Application (optional)</label>
                                        <select name="job_application_id" class="form-select form-select-sm">
                                            <option value="">General — not tied to a specific application</option>
                                            @foreach($applications as $application)
                                                <option value="{{ $application->id }}">{{ $application->jobPosting->title }} — {{ $application->jobPosting->country }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <label class="form-label small fw-semibold">Line Items</label>
                                <div id="invoiceItems">
                                    <div class="row g-2 mb-2 invoice-item-row">
                                        <div class="col-6"><input type="text" name="items[0][description]" class="form-control form-control-sm" placeholder="Description" required></div>
                                        <div class="col-2"><input type="number" name="items[0][quantity]" class="form-control form-control-sm" placeholder="Qty" value="1" min="1" required></div>
                                        <div class="col-4"><input type="number" step="0.01" name="items[0][unit_price]" class="form-control form-control-sm" placeholder="Unit Price" required></div>
                                    </div>
                                </div>
                                <button type="button" id="addInvoiceItemBtn" class="btn btn-sm btn-outline-secondary">+ Add Line Item</button>
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
                    var index = 1;
                    var btn = document.getElementById('addInvoiceItemBtn');
                    var container = document.getElementById('invoiceItems');
                    if (btn && container) {
                        btn.addEventListener('click', function () {
                            var row = document.createElement('div');
                            row.className = 'row g-2 mb-2 invoice-item-row';
                            row.innerHTML =
                                '<div class="col-6"><input type="text" name="items[' + index + '][description]" class="form-control form-control-sm" placeholder="Description" required></div>' +
                                '<div class="col-2"><input type="number" name="items[' + index + '][quantity]" class="form-control form-control-sm" placeholder="Qty" value="1" min="1" required></div>' +
                                '<div class="col-4"><input type="number" step="0.01" name="items[' + index + '][unit_price]" class="form-control form-control-sm" placeholder="Unit Price" required></div>';
                            container.appendChild(row);
                            index++;
                        });
                    }
                })();
            </script>
        </div>

        {{-- ============ APPOINTMENTS (full) ============ --}}
        <div class="tab-pane fade" id="tab-appointments">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bookAppointmentModal">+ Book Appointment</button>
            </div>
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
                                        <div class="d-flex justify-content-end gap-1">
                                            @if(in_array($appointment->status, ['requested', 'confirmed']))
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editAppointment-{{ $appointment->id }}">Edit</button>
                                            @endif
                                            @if($appointment->status === 'requested')
                                                <form method="POST" action="{{ route('admin.job-seekers.appointments.confirm', [$jobSeeker, $appointment]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                                </form>
                                            @endif
                                            @if($appointment->status === 'confirmed' && $appointment->scheduled_at->isPast())
                                                <form method="POST" action="{{ route('admin.job-seekers.appointments.complete', [$jobSeeker, $appointment]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Mark Completed</button>
                                                </form>
                                            @endif
                                            @if(in_array($appointment->status, ['requested', 'confirmed']))
                                                <form method="POST" action="{{ route('admin.job-seekers.appointments.cancel', [$jobSeeker, $appointment]) }}" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @if(in_array($appointment->status, ['requested', 'confirmed']))
                                    <div class="modal fade" id="editAppointment-{{ $appointment->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.job-seekers.appointments.update', [$jobSeeker, $appointment]) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Edit Appointment</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-2">
                                                            <label class="form-label small fw-semibold">Type</label>
                                                            <input type="text" name="type" class="form-control form-control-sm" value="{{ $appointment->type }}" required>
                                                        </div>
                                                        <div class="row g-2 mb-2">
                                                            <div class="col-8">
                                                                <label class="form-label small fw-semibold">Date &amp; Time</label>
                                                                <input type="datetime-local" name="scheduled_at" class="form-control form-control-sm" value="{{ $appointment->scheduled_at->format('Y-m-d\TH:i') }}" required>
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label small fw-semibold">Mode</label>
                                                                <select name="mode" class="form-select form-select-sm" required>
                                                                    @foreach(['video', 'physical', 'phone'] as $mode)
                                                                        <option value="{{ $mode }}" @selected($appointment->mode === $mode)>{{ ucfirst($mode) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="mb-1">
                                                            <label class="form-label small fw-semibold">Staff Member</label>
                                                            <select name="staff_id" class="form-select form-select-sm">
                                                                @foreach($officers as $officer)
                                                                    <option value="{{ $officer->id }}" @selected($appointment->staff_id === $officer->id)>{{ $officer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-4">No appointments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Book Appointment modal --}}
            <div class="modal fade" id="bookAppointmentModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.job-seekers.appointments.store', $jobSeeker) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Book Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @php
                                    $existingUpcoming = $appointments->filter(fn ($a) => $a->scheduled_at->isFuture() && in_array($a->status, ['requested', 'confirmed']))->sortBy('scheduled_at');
                                @endphp
                                @if($existingUpcoming->isNotEmpty())
                                    <div class="alert alert-warning py-2 px-3 small mb-3">
                                        <strong>{{ $jobSeeker->name }} already has {{ $existingUpcoming->count() }} upcoming appointment{{ $existingUpcoming->count() > 1 ? 's' : '' }}:</strong>
                                        <ul class="mb-0 mt-1 ps-3">
                                            @foreach($existingUpcoming as $existing)
                                                <li>{{ $existing->type }} — {{ $existing->scheduled_at->format('d M Y, g:ia') }} ({{ ucfirst($existing->status) }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Type</label>
                                    <select name="type" class="form-select" required>
                                        <option value="Recruitment Consultation">Recruitment Consultation</option>
                                        <option value="Interview">Interview</option>
                                        <option value="Visa Interview">Visa Interview</option>
                                        <option value="Pre-Departure">Pre-Departure</option>
                                        <option value="Onboarding">Onboarding</option>
                                        <option value="General Question">General Question</option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Date &amp; Time</label>
                                        <input type="datetime-local" name="scheduled_at" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Mode</label>
                                        <select name="mode" class="form-select" required>
                                            <option value="video">Video Call</option>
                                            <option value="physical">In Person</option>
                                            <option value="phone">Phone Call</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Meeting Link (optional)</label>
                                    <input type="url" name="meeting_link" class="form-control" placeholder="https://...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Staff Member</label>
                                    <select name="staff_id" class="form-select">
                                        <option value="">Me ({{ auth()->user()->name }})</option>
                                        @foreach($officers as $officer)
                                            <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($applications->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Related Application (optional)</label>
                                        <select name="job_application_id" class="form-select">
                                            <option value="">Not related to a specific application</option>
                                            @foreach($applications as $application)
                                                <option value="{{ $application->id }}">{{ $application->jobPosting->title }} — {{ $application->jobPosting->country }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Notes (optional)</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="form-text">Booked directly by staff, so this starts as <strong>Confirmed</strong> — not "Requested" like a candidate's own booking.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Book Appointment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ MESSAGES (support tickets) ============ --}}
        <div class="tab-pane fade" id="tab-messages">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newMessageModal">+ New Message</button>
            </div>
            @forelse($tickets as $ticket)
                <div class="card stat-card mb-3">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="fw-semibold">{{ $ticket->subject }}</span>
                            <span class="text-secondary small ms-2">{{ $ticket->ticket_number }} · {{ $ticket->category }}</span>
                        </div>
                        <form method="POST" action="{{ route('admin.job-seekers.tickets.status', [$jobSeeker, $ticket]) }}" class="d-flex align-items-center gap-2">
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
                            @php $isStaff = $message->user_id !== $jobSeeker->id; @endphp
                            <div class="d-flex {{ $isStaff ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                                <div style="max-width:75%;">
                                    <div class="small text-secondary mb-1 {{ $isStaff ? 'text-end' : '' }}">{{ $message->author->name }} · {{ $message->created_at->format('d M, g:ia') }}</div>
                                    <div class="p-2 rounded-3 small {{ $isStaff ? 'bg-primary text-white' : 'bg-light' }}">{{ $message->body }}</div>
                                </div>
                            </div>
                        @endforeach

                        @if($ticket->status !== 'closed')
                            <form method="POST" action="{{ route('admin.job-seekers.tickets.reply', [$jobSeeker, $ticket]) }}" class="mt-3 d-flex gap-2">
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

            <div class="modal fade" id="newMessageModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.job-seekers.tickets.store', $jobSeeker) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">New Message to {{ $jobSeeker->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Subject</label>
                                    <input type="text" name="subject" class="form-control form-control-sm" required>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Category</label>
                                        <select name="category" class="form-select form-select-sm" required>
                                            <option value="Applications">Applications</option>
                                            <option value="Documents">Documents</option>
                                            <option value="Payments">Payments</option>
                                            <option value="Visa">Visa</option>
                                            <option value="Job Offers">Job Offers</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Priority</label>
                                        <select name="priority" class="form-select form-select-sm" required>
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label small fw-semibold">Message</label>
                                    <textarea name="message" class="form-control form-control-sm" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                    <div class="p-5 text-center text-secondary">No notifications sent to this candidate yet.</div>
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
                                                    <form method="POST" action="{{ route('admin.job-seekers.tasks.complete', [$jobSeeker, $task]) }}">
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
                        <form method="POST" action="{{ route('admin.job-seekers.tasks.store', $jobSeeker) }}">
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
                        <form method="POST" action="{{ route('admin.job-seekers.notes.store', $jobSeeker) }}">
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
