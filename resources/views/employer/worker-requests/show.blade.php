<x-employer-layout title="Worker Request Details">

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

    <a href="{{ route('employer.worker-requests.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Worker Requests</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $workerRequest->quantity }}x {{ $workerRequest->job_title }}
                </h2>
                <p class="text-secondary mb-0">Submitted {{ $workerRequest->created_at->format('d M Y') }}</p>
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

    @if($workerRequest->status === 'clarification_required' && $workerRequest->review_notes)
        <div class="alert alert-warning">
            <strong>Altura needs more information:</strong> {{ $workerRequest->review_notes }}
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Your Response</h3>
            <form method="POST" action="{{ route('employer.worker-requests.respond', $workerRequest) }}">
                @csrf
                <textarea name="employer_response" class="form-control mb-3" rows="4" placeholder="Answer Altura's question here..." required>{{ old('employer_response') }}</textarea>
                <button type="submit" class="btn btn-primary">Send Response</button>
            </form>
        </div>
    @elseif($workerRequest->status === 'rejected' && $workerRequest->review_notes)
        <div class="alert alert-danger">
            <strong>This request was not approved:</strong> {{ $workerRequest->review_notes }}
        </div>
    @endif

    @if($workerRequest->employer_response)
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Your Previous Response</h3>
            <p class="small text-secondary mb-1">Sent {{ $workerRequest->employer_responded_at?->format('d M Y, g:ia') }}</p>
            <p class="mb-0">{{ $workerRequest->employer_response }}</p>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
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
            </div>

            @if($workerRequest->responsibilities)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Responsibilities</h3>
                    <p class="small mb-0">{{ $workerRequest->responsibilities }}</p>
                </div>
            @endif

            @if($workerRequest->requirements)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Requirements</h3>
                    <p class="small mb-0">{{ $workerRequest->requirements }}</p>
                </div>
            @endif
        </div>

        <div class="col-lg-6">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Contact Person</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Name</dt><dd class="col-7">{{ $workerRequest->contact_name ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Job Title</dt><dd class="col-7">{{ $workerRequest->contact_job_title ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Email</dt><dd class="col-7">{{ $workerRequest->contact_email ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Phone</dt><dd class="col-7">{{ $workerRequest->contact_phone ?? '—' }}</dd>
                </dl>
            </div>

            @if($workerRequest->skills || $workerRequest->benefits)
                <div class="card stat-card p-4 mb-4">
                    @if($workerRequest->skills)
                        <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Skills</h3>
                        <p class="small">{{ $workerRequest->skills }}</p>
                    @endif
                    @if($workerRequest->benefits)
                        <h3 class="h6 fw-semibold mb-2 {{ $workerRequest->skills ? 'mt-3' : '' }}" style="font-family:'Poppins',sans-serif;">Benefits</h3>
                        <p class="small mb-0">{{ $workerRequest->benefits }}</p>
                    @endif
                </div>
            @endif

            @if($workerRequest->additional_requirements)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Additional Information</h3>
                    <p class="small mb-0">{{ $workerRequest->additional_requirements }}</p>
                </div>
            @endif

            @if($workerRequest->job_posting_id)
                <div class="card stat-card p-4">
                    <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Converted to Job Posting</h3>
                    <p class="text-secondary small mb-3">Altura has created an official job posting from this request.</p>
                    <a href="{{ route('employer.jobs.index') }}" class="btn btn-sm btn-primary">View My Jobs →</a>
                </div>
            @endif
        </div>
    </div>

</x-employer-layout>
