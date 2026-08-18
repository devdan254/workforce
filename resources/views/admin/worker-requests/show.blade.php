<x-admin-layout title="Worker Request Details">

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

    <a href="{{ route('admin.worker-requests.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Worker Requests</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $workerRequest->quantity }}x {{ $workerRequest->job_title }}
                </h2>
                <p class="text-secondary mb-0">
                    {{ $workerRequest->employer->employerProfile?->company_name ?? $workerRequest->employer->name }}
                    · Submitted {{ $workerRequest->created_at->format('d M Y') }}
                </p>
            </div>
            @php
                $badgeClass = match($workerRequest->status) {
                    'approved', 'converted' => 'bg-success-subtle text-success-emphasis',
                    'rejected' => 'bg-danger-subtle text-danger-emphasis',
                    'clarification_required' => 'bg-warning-subtle text-warning-emphasis',
                    default => 'bg-primary-subtle text-primary-emphasis',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6">{{ ucwords(str_replace('_', ' ', $workerRequest->status)) }}</span>
        </div>
    </div>

    @if($workerRequest->review_notes)
        <div class="alert alert-warning">
            <strong>Your note to the employer:</strong> {{ $workerRequest->review_notes }}
        </div>
    @endif

    @if($workerRequest->employer_response)
        <div class="alert alert-info">
            <strong>Employer's response ({{ $workerRequest->employer_responded_at?->format('d M Y, g:ia') }}):</strong>
            <p class="mb-0 mt-1">{{ $workerRequest->employer_response }}</p>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Hiring Requirements</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Job Title</dt><dd class="col-7">{{ $workerRequest->job_title }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Quantity</dt><dd class="col-7">{{ $workerRequest->quantity }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Employment Type</dt><dd class="col-7 text-capitalize">{{ str_replace('_', ' ', $workerRequest->employment_type) }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Experience</dt><dd class="col-7">{{ $workerRequest->preferred_experience ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Age Range</dt><dd class="col-7">{{ $workerRequest->age_range ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Start Date</dt><dd class="col-7">{{ $workerRequest->preferred_start_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Location</dt><dd class="col-7">{{ $workerRequest->work_location ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Salary</dt>
                    <dd class="col-7">
                        @if($workerRequest->salary_min || $workerRequest->salary_max)
                            {{ $workerRequest->currency }} {{ number_format($workerRequest->salary_min ?? 0, 0) }}@if($workerRequest->salary_max) – {{ number_format($workerRequest->salary_max, 0) }}@endif
                        @else
                            —
                        @endif
                    </dd>
                </dl>

                @if($workerRequest->responsibilities)
                    <hr><strong class="small">Responsibilities</strong><p class="small mb-0 mt-1">{{ $workerRequest->responsibilities }}</p>
                @endif
                @if($workerRequest->requirements)
                    <hr><strong class="small">Requirements</strong><p class="small mb-0 mt-1">{{ $workerRequest->requirements }}</p>
                @endif
                @if($workerRequest->skills)
                    <hr><strong class="small">Skills</strong><p class="small mb-0 mt-1">{{ $workerRequest->skills }}</p>
                @endif
                @if($workerRequest->benefits)
                    <hr><strong class="small">Benefits</strong><p class="small mb-0 mt-1">{{ $workerRequest->benefits }}</p>
                @endif
                @if($workerRequest->additional_requirements)
                    <hr><strong class="small">Additional Information</strong><p class="small mb-0 mt-1">{{ $workerRequest->additional_requirements }}</p>
                @endif
            </div>

            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Company &amp; Contact</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Company</dt><dd class="col-7">{{ $workerRequest->employer->employerProfile?->company_name ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Industry</dt><dd class="col-7">{{ $workerRequest->employer->employerProfile?->industry ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Country</dt><dd class="col-7">{{ $workerRequest->employer->employerProfile?->country ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Contact</dt><dd class="col-7">{{ $workerRequest->contact_name ?? $workerRequest->employer->name }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Email</dt><dd class="col-7">{{ $workerRequest->contact_email ?? $workerRequest->employer->email }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Phone</dt><dd class="col-7">{{ $workerRequest->contact_phone ?? $workerRequest->employer->phone ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        <div class="col-lg-5">
            @if(! $workerRequest->job_posting_id)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Review</h3>
                    <form method="POST" action="{{ route('admin.worker-requests.review', $workerRequest) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" class="form-select form-select-sm" required>
                                @foreach(['under_review' => 'Under Review', 'clarification_required' => 'Needs Clarification', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                                    <option value="{{ $value }}" @selected($workerRequest->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Notes (visible to the employer if clarification is needed or the request is rejected)</label>
                            <textarea name="review_notes" class="form-control form-control-sm" rows="3">{{ $workerRequest->review_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">Save Review</button>
                    </form>
                </div>

                @if($workerRequest->status === 'approved')
                    <div class="card stat-card p-4">
                        <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Create Job Posting</h3>
                        <p class="text-secondary small mb-3">Creates a draft posting pre-filled with everything this request collected (salary, responsibilities, skills, benefits, requirements) — you'll add a description and publish from the posting's own page.</p>
                        <form method="POST" action="{{ route('admin.worker-requests.convert', $workerRequest) }}">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Category</label>
                                <select name="job_category_id" class="form-select form-select-sm" required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Country</label>
                                <input type="text" name="country" class="form-control form-control-sm" value="{{ $workerRequest->employer->employerProfile?->country }}" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success w-100">Create Draft Job Posting →</button>
                        </form>
                    </div>
                @endif
            @else
                <div class="card stat-card p-4">
                    <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Converted</h3>
                    <p class="text-secondary small mb-3">This request has been converted into a job posting.</p>
                    <a href="{{ route('admin.job-postings.edit', $workerRequest->job_posting_id) }}" class="btn btn-sm btn-primary w-100">View / Edit Job Posting →</a>
                </div>
            @endif
        </div>
    </div>

</x-admin-layout>
