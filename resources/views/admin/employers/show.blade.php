<x-admin-layout title="Employer Workspace">

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

    <a href="{{ route('admin.employers.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Employers</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $employer->employerProfile?->company_name ?? $employer->name }}
                </h2>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-primary-subtle text-primary-emphasis">Employer</span>
                    <span class="badge {{ $employer->is_active ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                        {{ $employer->is_active ? 'Active' : 'Suspended' }}
                    </span>
                    @if($employer->employerProfile?->industry)
                        <span class="text-secondary small">{{ $employer->employerProfile->industry }}</span>
                    @endif
                    <span class="text-secondary small">Assigned Officer: {{ $employer->employerProfile?->assignedOfficer?->name ?? 'Not yet assigned' }}</span>
                </div>
            </div>
            <div class="text-end">
                <div class="small text-secondary">Profile Completion</div>
                <div class="fw-semibold fs-5">{{ $employer->employerProfile?->profile_completion_percent ?? 0 }}%</div>
                @can('updateEmployer', $employer)
                    <a href="{{ route('admin.employers.edit', $employer) }}" class="btn btn-sm btn-outline-secondary mt-1">Edit Account</a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tab navigation — Worker Requests/Jobs/Candidates and Documents/
         Payments/Invoices/Appointments/Support/Notes/Activity land here in
         subsequent deliveries, same incremental pattern used throughout
         this build. --}}
    <ul class="nav nav-pills mb-4 flex-wrap gap-1" id="workspaceTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-overview" type="button">Overview</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button">Company Profile</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-worker-requests" type="button">Worker Requests</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-jobs" type="button">Jobs</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-candidates" type="button">Candidates</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-documents" type="button">Documents</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-payments" type="button">Payments</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-invoices" type="button">Invoices</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-appointments" type="button">Appointments</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-support" type="button">Support</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-notes" type="button">Notes</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-activity" type="button">Activity</button></li>
    </ul>

    <div class="tab-content">

        {{-- ============ OVERVIEW ============ --}}
        <div class="tab-pane fade show active" id="tab-overview">

            <div class="row g-3 mb-4">
                <div class="col-md-4 col-lg-2">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Active Jobs</div>
                        <div class="fw-semibold fs-5">{{ $activeJobs }} <span class="text-secondary fs-6">/ {{ $totalJobs }}</span></div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Applicants</div>
                        <div class="fw-semibold fs-5">{{ $totalApplicants }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Shortlisted</div>
                        <div class="fw-semibold fs-5">{{ $shortlisted }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Workers Hired</div>
                        <div class="fw-semibold fs-5">{{ $hired }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Total Invoiced</div>
                        <div class="fw-semibold fs-5">{{ $financials['currency'] }} {{ number_format($financials['total'], 0) }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card stat-card p-3 h-100">
                        <div class="stat-label">Outstanding</div>
                        <div class="fw-semibold fs-5 {{ $financials['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $financials['currency'] }} {{ number_format($financials['balance'], 0) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card stat-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Recent Worker Requests</h3>
                            @if(\Illuminate\Support\Facades\Route::has('admin.worker-requests.index'))
                                <a href="{{ route('admin.worker-requests.index') }}" class="small">View All →</a>
                            @endif
                        </div>
                        @forelse($recentWorkerRequests as $workerRequest)
                            @php
                                $badgeClass = match($workerRequest->status) {
                                    'approved', 'converted' => 'bg-success-subtle text-success-emphasis',
                                    'rejected' => 'bg-danger-subtle text-danger-emphasis',
                                    'clarification_required' => 'bg-warning-subtle text-warning-emphasis',
                                    default => 'bg-primary-subtle text-primary-emphasis',
                                };
                            @endphp
                            <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div>
                                    <a href="{{ route('admin.worker-requests.show', $workerRequest) }}" class="fw-semibold small text-decoration-none">{{ $workerRequest->quantity }}x {{ $workerRequest->job_title }}</a>
                                    <div class="text-secondary" style="font-size:.75rem;">{{ $workerRequest->created_at->format('d M Y') }}</div>
                                </div>
                                <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $workerRequest->status)) }}</span>
                            </div>
                        @empty
                            <p class="text-secondary mb-0">No worker requests yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card stat-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Job Postings</h3>
                            <a href="{{ route('admin.job-postings.index') }}" class="small">View All →</a>
                        </div>
                        @forelse($recentJobPostings as $posting)
                            <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div>
                                    <a href="{{ route('admin.job-postings.show', $posting) }}" class="fw-semibold small text-decoration-none">{{ $posting->title }}</a>
                                    <div class="text-secondary" style="font-size:.75rem;">{{ $posting->country }} · {{ $posting->applications_count }} applicant{{ $posting->applications_count !== 1 ? 's' : '' }}</div>
                                </div>
                                <span class="badge {{ $posting->status === 'open' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">{{ ucfirst($posting->status) }}</span>
                            </div>
                        @empty
                            <p class="text-secondary mb-0">No job postings linked to this employer yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Permissions — the ONLY two optional grants; everything else in
                 the Employer Portal is baseline access, automatic, no toggle
                 needed. Restricted by default until checked here. --}}
            <div class="row g-4 mt-1">
                <div class="col-lg-6">
                    <div class="card stat-card p-4">
                        <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Permissions</h3>
                        <p class="text-secondary small mb-3">Everything else in the Employer Portal is available automatically — these two are the only things you decide per employer.</p>
                        <form method="POST" action="{{ route('admin.employers.permissions.update', $employer) }}">
                            @csrf
                            @method('PATCH')
                            <div class="form-check mb-3">
                                <input type="checkbox" name="conduct_interviews" value="1" class="form-check-input" id="conductInterviews" @checked($conductInterviews)>
                                <label class="form-check-label" for="conductInterviews">
                                    <strong>Conduct Interviews</strong>
                                    <div class="text-secondary small">Lets this employer join scheduled interviews with their candidates.</div>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="view_candidate_documents" value="1" class="form-check-input" id="viewCandidateDocuments" @checked($viewCandidateDocuments)>
                                <label class="form-check-label" for="viewCandidateDocuments">
                                    <strong>View / Download Candidate Documents</strong>
                                    <div class="text-secondary small">Lets this employer view and download documents (CV, certificates, etc.) for their own candidates.</div>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Save Permissions</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ COMPANY PROFILE ============ --}}
        <div class="tab-pane fade" id="tab-profile">
            <div class="d-flex justify-content-end mb-3">
                @can('editEmployerProfile', $employer)
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        {{ $employer->employerProfile ? 'Edit' : 'Add' }} Company Profile
                    </button>
                @endcan
            </div>
            <div class="card stat-card p-4">
                @if($employer->employerProfile)
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Company</h4>
                            <dl class="row small mb-0">
                                <dt class="col-5 text-secondary fw-normal">Company Name</dt><dd class="col-7">{{ $employer->employerProfile->company_name }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Industry</dt><dd class="col-7">{{ $employer->employerProfile->industry ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Country</dt><dd class="col-7">{{ $employer->employerProfile->country }}</dd>
                                <dt class="col-5 text-secondary fw-normal">City</dt><dd class="col-7">{{ $employer->employerProfile->city ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Website</dt><dd class="col-7">{{ $employer->employerProfile->company_website ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Company Size</dt><dd class="col-7">{{ $employer->employerProfile->company_size ?? '—' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h4 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Contact Person</h4>
                            <dl class="row small mb-0">
                                <dt class="col-5 text-secondary fw-normal">Name</dt><dd class="col-7">{{ $employer->name }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Job Title</dt><dd class="col-7">{{ $employer->employerProfile->contact_job_title ?? '—' }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Email</dt><dd class="col-7">{{ $employer->email }}</dd>
                                <dt class="col-5 text-secondary fw-normal">Phone</dt><dd class="col-7">{{ $employer->phone ?? '—' }}</dd>
                            </dl>
                        </div>
                        @if($employer->employerProfile->company_description)
                            <div class="col-12">
                                <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Description</h4>
                                <p class="small mb-0">{{ $employer->employerProfile->company_description }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-secondary mb-0">This employer hasn't completed their profile yet — use "Add Company Profile" above to fill it in on their behalf.</p>
                @endif
            </div>

            @can('editEmployerProfile', $employer)
                <div class="modal fade" id="editProfileModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.employers.profile.update', $employer) }}">
                                @csrf
                                @method('PATCH')
                                <div class="modal-header">
                                    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">{{ $employer->employerProfile ? 'Edit' : 'Add' }} Company Profile</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Company Name</label>
                                            <input type="text" name="company_name" class="form-control form-control-sm" value="{{ old('company_name', $employer->employerProfile?->company_name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Industry</label>
                                            <input type="text" name="industry" class="form-control form-control-sm" value="{{ old('industry', $employer->employerProfile?->industry) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Country</label>
                                            <input type="text" name="country" class="form-control form-control-sm" value="{{ old('country', $employer->employerProfile?->country) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">City</label>
                                            <input type="text" name="city" class="form-control form-control-sm" value="{{ old('city', $employer->employerProfile?->city) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Website</label>
                                            <input type="url" name="company_website" class="form-control form-control-sm" value="{{ old('company_website', $employer->employerProfile?->company_website) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Company Size</label>
                                            <select name="company_size" class="form-select form-select-sm">
                                                <option value="">Select</option>
                                                @foreach(['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'] as $size)
                                                    <option value="{{ $size }}" @selected(old('company_size', $employer->employerProfile?->company_size) === $size)>{{ $size }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Contact Job Title</label>
                                            <input type="text" name="contact_job_title" class="form-control form-control-sm" value="{{ old('contact_job_title', $employer->employerProfile?->contact_job_title) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Phone</label>
                                            <input type="tel" name="phone" class="form-control form-control-sm" value="{{ old('phone', $employer->phone) }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Company Description</label>
                                            <textarea name="company_description" class="form-control form-control-sm" rows="2">{{ old('company_description', $employer->employerProfile?->company_description) }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Assigned Officer</label>
                                            <select name="assigned_officer_id" class="form-select form-select-sm">
                                                <option value="">Not assigned</option>
                                                @foreach($officers as $officer)
                                                    <option value="{{ $officer->id }}" @selected((int) old('assigned_officer_id', $employer->employerProfile?->assigned_officer_id) === $officer->id)>{{ $officer->name }}</option>
                                                @endforeach
                                            </select>
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

        {{-- ============ WORKER REQUESTS ============ --}}
        <div class="tab-pane fade" id="tab-worker-requests">
            <div class="card stat-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <th>Quantity</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allWorkerRequests as $workerRequest)
                                <tr>
                                    <td class="fw-semibold small">{{ $workerRequest->job_title }}</td>
                                    <td class="small">{{ $workerRequest->quantity }}</td>
                                    <td class="small text-secondary">{{ $workerRequest->created_at->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $wrBadge = match($workerRequest->status) {
                                                'approved', 'converted' => 'bg-success-subtle text-success-emphasis',
                                                'rejected' => 'bg-danger-subtle text-danger-emphasis',
                                                'clarification_required' => 'bg-warning-subtle text-warning-emphasis',
                                                default => 'bg-primary-subtle text-primary-emphasis',
                                            };
                                        @endphp
                                        <span class="badge {{ $wrBadge }}">{{ ucwords(str_replace('_', ' ', $workerRequest->status)) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.worker-requests.show', $workerRequest) }}" class="btn btn-sm btn-outline-primary">Review →</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-5">No worker requests from this employer yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-secondary small mt-2 mb-0">Reviewing, approving, and converting requests to job postings happens on each request's own page — click "Review" above.</p>
        </div>

        {{-- ============ JOBS ============ --}}
        <div class="tab-pane fade" id="tab-jobs">
            <div class="card stat-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Job</th>
                                <th>Country</th>
                                <th>Applicants</th>
                                <th>Shortlisted</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allJobPostings as $posting)
                                <tr>
                                    <td class="fw-semibold small">{{ $posting->title }}</td>
                                    <td class="small">{{ $posting->country }}</td>
                                    <td class="small">{{ $posting->applications_count }}</td>
                                    <td class="small">{{ $posting->shortlisted_count }}</td>
                                    <td>
                                        @php
                                            $jpBadge = match($posting->status) {
                                                'open' => 'bg-success-subtle text-success-emphasis',
                                                'draft' => 'bg-secondary-subtle text-secondary-emphasis',
                                                'closed' => 'bg-warning-subtle text-warning-emphasis',
                                                'filled' => 'bg-primary-subtle text-primary-emphasis',
                                                'archived' => 'bg-danger-subtle text-danger-emphasis',
                                                default => 'bg-light text-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $jpBadge }}">{{ ucfirst($posting->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('admin.job-postings.applicants', $posting) }}" class="btn btn-sm btn-outline-primary">Applicants</a>
                                            <a href="{{ route('admin.job-postings.edit', $posting) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @if($posting->status === 'draft')
                                                <form method="POST" action="{{ route('admin.job-postings.publish', $posting) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Publish</button>
                                                </form>
                                            @elseif($posting->status === 'open')
                                                <form method="POST" action="{{ route('admin.job-postings.close', $posting) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning">Close</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-5">No job postings linked to this employer yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ CANDIDATES ============ --}}
        <div class="tab-pane fade" id="tab-candidates">
            <div class="card stat-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Candidate</th>
                                <th>Job</th>
                                <th>Country</th>
                                <th>Applied</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allCandidates as $application)
                                <tr>
                                    <td class="fw-semibold small">{{ $application->jobSeeker->name }}</td>
                                    <td class="small">{{ $application->jobPosting->title }}</td>
                                    <td class="small">{{ $application->jobSeeker->jobSeekerProfile?->country ?? '—' }}</td>
                                    <td class="small text-secondary">{{ $application->applied_at?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        @php
                                            $candBadge = match(true) {
                                                in_array($application->currentStatus->slug, ['rejected', 'withdrawn']) => 'bg-danger-subtle text-danger-emphasis',
                                                $application->currentStatus->slug === 'deployed' => 'bg-success-subtle text-success-emphasis',
                                                in_array($application->currentStatus->slug, ['offer_extended', 'offer_accepted', 'selected']) => 'bg-warning-subtle text-warning-emphasis',
                                                default => 'bg-primary-subtle text-primary-emphasis',
                                            };
                                        @endphp
                                        <span class="badge {{ $candBadge }}">{{ $application->currentStatus->label }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.job-seekers.show', $application->job_seeker_id) }}" class="btn btn-sm btn-outline-primary">View in Workspace</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-5">No candidates have applied to this employer's jobs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ DOCUMENTS (full vault) ============ --}}
        <div class="tab-pane fade" id="tab-documents">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <span class="badge bg-primary-subtle text-primary-emphasis fs-6">{{ $documentCompleted }} of {{ $documentTotal }} documents uploaded</span>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestDocumentModal">+ Request Document</button>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <ul class="nav nav-pills flex-wrap gap-1 mb-0">
                    @foreach(['' => 'All', 'required' => 'Required', 'under_review' => 'Under Review', 'verified' => 'Verified', 'rejected' => 'Rejected'] as $key => $label)
                        <li class="nav-item">
                            <a class="nav-link {{ $activeDocStatus === $key ? 'active' : '' }}" href="{{ route('admin.employers.show', $employer) }}?doc_status={{ $key }}&doc_job_posting_id={{ request('doc_job_posting_id') }}#tab-documents">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>

                <form method="GET" action="{{ route('admin.employers.show', $employer) }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="doc_status" value="{{ $activeDocStatus }}">
                    <label class="form-label small fw-semibold mb-0 text-nowrap">Job</label>
                    <select name="doc_job_posting_id" class="form-select form-select-sm" style="min-width:200px;" onchange="this.form.submit()">
                        <option value="">All / General</option>
                        @foreach($allJobPostings as $posting)
                            <option value="{{ $posting->id }}" @selected((int) request('doc_job_posting_id') === $posting->id)>{{ $posting->title }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @forelse($groupedDocuments as $category => $documents)
                <div class="card stat-card mb-4">
                    <div class="p-3 border-bottom bg-light">
                        <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">{{ $category }}</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Document</th><th>Job</th><th>Status</th><th>Uploaded</th><th>Verified By</th><th class="text-end">Action</th></tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td class="fw-semibold small">{{ $document->name }}</td>
                                        <td class="small text-secondary">{{ $document->jobPosting?->title ?? 'General' }}</td>
                                        <td><span class="badge {{ match($document->status) { 'verified' => 'bg-success-subtle text-success-emphasis', 'rejected' => 'bg-danger-subtle text-danger-emphasis', 'under_review' => 'bg-primary-subtle text-primary-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucwords(str_replace('_', ' ', $document->status)) }}</span></td>
                                        <td class="small text-secondary">{{ $document->uploaded_at?->format('d M Y') ?? '—' }}</td>
                                        <td class="small text-secondary">{{ $document->verifiedBy?->name ?? '—' }}</td>
                                        <td class="text-end">
                                            @include('admin.employers.partials._document-actions', ['document' => $document, 'employer' => $employer, 'categories' => $documentCategories, 'prefix' => 'vault'])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card stat-card p-5 text-center text-secondary">No documents match these filters.</div>
            @endforelse

            <div class="modal fade" id="requestDocumentModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.employers.documents.request', $employer) }}">
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
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Document Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Business License" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Related Job (optional)</label>
                                    <select name="job_posting_id" class="form-select">
                                        <option value="">General — not tied to a specific job</option>
                                        @foreach($allJobPostings as $posting)
                                            <option value="{{ $posting->id }}">{{ $posting->title }} — {{ $posting->country }}</option>
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
                                                <form method="POST" action="{{ route('admin.employers.payments.confirm', [$employer, $payment]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                                </form>
                                            @elseif($payment->status === 'confirmed')
                                                <form method="POST" action="{{ route('admin.employers.payments.refund', [$employer, $payment]) }}" class="d-inline" onsubmit="return confirm('Refund this payment?');">
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
                                                <form method="POST" action="{{ route('admin.employers.payments.update', [$employer, $payment]) }}">
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
                        <form method="POST" action="{{ $allInvoices->isNotEmpty() ? route('admin.employers.invoices.payments.store', [$employer, $allInvoices->first()]) : '#' }}" id="recordPaymentForm">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Record Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @if($allInvoices->isEmpty())
                                    <p class="text-secondary small mb-0">This employer has no invoices yet — create one first on the Invoices tab.</p>
                                @else
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Invoice</label>
                                        <select name="invoice_select" class="form-select form-select-sm" id="recordPaymentInvoice" required>
                                            @foreach($allInvoices as $invoice)
                                                <option value="{{ route('admin.employers.invoices.payments.store', [$employer, $invoice]) }}" data-balance="{{ $invoice->balance }}" data-currency="{{ $invoice->currency }}">
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
                                        <input type="text" name="reference" class="form-control form-control-sm" placeholder="e.g. bank transfer reference">
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
                                            $invBadge = match($invoice->status) {
                                                'paid' => 'bg-success-subtle text-success-emphasis',
                                                'sent' => 'bg-primary-subtle text-primary-emphasis',
                                                'cancelled' => 'bg-secondary-subtle text-secondary-emphasis',
                                                default => 'bg-warning-subtle text-warning-emphasis',
                                            };
                                        @endphp
                                        <span class="badge {{ $invBadge }}">{{ ucfirst($invoice->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($invoice->status === 'draft')
                                                <form method="POST" action="{{ route('admin.employers.invoices.send', [$employer, $invoice]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">Send</button>
                                                </form>
                                            @endif
                                            @if(!in_array($invoice->status, ['paid', 'cancelled']))
                                                <form method="POST" action="{{ route('admin.employers.invoices.cancel', [$employer, $invoice]) }}" class="d-inline" onsubmit="return confirm('Cancel this invoice?');">
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
                        <form method="POST" action="{{ route('admin.employers.invoices.store', $employer) }}" id="createInvoiceForm">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Create Invoice</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Description</label>
                                        <input type="text" name="description" class="form-control form-control-sm" placeholder="e.g. Recruitment Service Fee" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Currency</label>
                                        <input type="text" name="currency" class="form-control form-control-sm" value="USD" maxlength="3" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Due Date</label>
                                        <input type="date" name="due_date" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Related Candidate (optional)</label>
                                        <select name="job_application_id" class="form-select form-select-sm">
                                            <option value="">General — not tied to a specific candidate</option>
                                            @foreach($allCandidates as $application)
                                                <option value="{{ $application->id }}">{{ $application->jobSeeker->name }} — {{ $application->jobPosting->title }}</option>
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
                            <tr><th>Date</th><th>Type</th><th>Candidate</th><th>Mode</th><th>Staff</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse($allAppointments as $appointment)
                                <tr>
                                    <td class="small">{{ $appointment->scheduled_at->format('d M Y, g:ia') }}</td>
                                    <td class="small fw-semibold">{{ $appointment->type }}</td>
                                    <td class="small">{{ $appointment->jobApplication?->jobSeeker->name ?? '—' }}</td>
                                    <td class="small text-capitalize">{{ $appointment->mode }}</td>
                                    <td class="small">{{ $appointment->staff?->name ?? '—' }}</td>
                                    <td><span class="badge {{ match($appointment->status) { 'confirmed' => 'bg-success-subtle text-success-emphasis', 'completed' => 'bg-secondary-subtle text-secondary-emphasis', 'cancelled' => 'bg-danger-subtle text-danger-emphasis', default => 'bg-warning-subtle text-warning-emphasis' } }}">{{ ucfirst($appointment->status) }}</span></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if(in_array($appointment->status, ['requested', 'confirmed']))
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editAppointment-{{ $appointment->id }}">Edit</button>
                                            @endif
                                            @if($appointment->status === 'requested')
                                                <form method="POST" action="{{ route('admin.employers.appointments.confirm', [$employer, $appointment]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                                </form>
                                            @endif
                                            @if($appointment->status === 'confirmed' && $appointment->scheduled_at->isPast())
                                                <form method="POST" action="{{ route('admin.employers.appointments.complete', [$employer, $appointment]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Mark Completed</button>
                                                </form>
                                            @endif
                                            @if(in_array($appointment->status, ['requested', 'confirmed']))
                                                <form method="POST" action="{{ route('admin.employers.appointments.cancel', [$employer, $appointment]) }}" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
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
                                                <form method="POST" action="{{ route('admin.employers.appointments.update', [$employer, $appointment]) }}">
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
                                <tr><td colspan="7" class="text-center text-secondary py-4">No appointments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Book Appointment modal --}}
            <div class="modal fade" id="bookAppointmentModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.employers.appointments.store', $employer) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Book Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @php
                                    $existingUpcoming = $allAppointments->filter(fn ($a) => $a->scheduled_at->isFuture() && in_array($a->status, ['requested', 'confirmed']))->sortBy('scheduled_at');
                                @endphp
                                @if($existingUpcoming->isNotEmpty())
                                    <div class="alert alert-warning py-2 px-3 small mb-3">
                                        <strong>{{ $employer->name }} already has {{ $existingUpcoming->count() }} upcoming appointment{{ $existingUpcoming->count() > 1 ? 's' : '' }}:</strong>
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
                                        <option value="Contract Discussion">Contract Discussion</option>
                                        <option value="Deployment Meeting">Deployment Meeting</option>
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
                                @if($allCandidates->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Related Candidate (optional)</label>
                                        <select name="job_application_id" class="form-select">
                                            <option value="">Not related to a specific candidate</option>
                                            @foreach($allCandidates as $application)
                                                <option value="{{ $application->id }}">{{ $application->jobSeeker->name }} — {{ $application->jobPosting->title }}</option>
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

        {{-- ============ SUPPORT (support tickets) ============ --}}
        <div class="tab-pane fade" id="tab-support">
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
                        <form method="POST" action="{{ route('admin.employers.tickets.status', [$employer, $ticket]) }}" class="d-flex align-items-center gap-2">
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
                            @php $isStaff = $message->user_id !== $employer->id; @endphp
                            <div class="d-flex {{ $isStaff ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                                <div style="max-width:75%;">
                                    <div class="small text-secondary mb-1 {{ $isStaff ? 'text-end' : '' }}">{{ $message->author->name }} · {{ $message->created_at->format('d M, g:ia') }}</div>
                                    <div class="p-2 rounded-3 small {{ $isStaff ? 'bg-primary text-white' : 'bg-light' }}">{{ $message->body }}</div>
                                </div>
                            </div>
                        @endforeach

                        @if($ticket->status !== 'closed')
                            <form method="POST" action="{{ route('admin.employers.tickets.reply', [$employer, $ticket]) }}" class="mt-3 d-flex gap-2">
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
                        <form method="POST" action="{{ route('admin.employers.tickets.store', $employer) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">New Message to {{ $employer->name }}</h5>
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
                                            <option value="Worker Requests">Worker Requests</option>
                                            <option value="Candidates">Candidates</option>
                                            <option value="Job Postings">Job Postings</option>
                                            <option value="Documents">Documents</option>
                                            <option value="Payments">Payments</option>
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
                        <form method="POST" action="{{ route('admin.employers.notes.store', $employer) }}">
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

    {{-- The Documents status filter reloads the page with query params
         (doc_status/doc_job_posting_id), same as the Job filter form does —
         without this, the reload would silently land back on Overview
         instead of staying on Documents, since Bootstrap's pill tabs don't
         know about URL query strings on their own. Job filter's own
         onchange-submit hits the same reload path, so both need this. --}}
    @if(request()->hasAny(['doc_status', 'doc_job_posting_id']))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var trigger = document.querySelector('[data-bs-target="#tab-documents"]');
                if (trigger) {
                    new bootstrap.Tab(trigger).show();
                }
            });
        </script>
    @endif

</x-admin-layout>
