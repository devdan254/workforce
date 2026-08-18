<x-employer-layout title="Job Details">

    <a href="{{ route('employer.jobs.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to My Jobs</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $posting->title }}</h2>
                <p class="text-secondary mb-0">{{ $posting->category->name }} · {{ $posting->city ? $posting->city.', ' : '' }}{{ $posting->country }}</p>
            </div>
            @php
                $badgeClass = match($posting->status) {
                    'open' => 'bg-success-subtle text-success-emphasis',
                    'closed' => 'bg-warning-subtle text-warning-emphasis',
                    'filled' => 'bg-primary-subtle text-primary-emphasis',
                    'archived' => 'bg-danger-subtle text-danger-emphasis',
                    default => 'bg-secondary-subtle text-secondary-emphasis',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($posting->status) }}</span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card stat-card p-3 text-center">
                <div class="stat-label">Applications</div>
                <div class="stat-value">{{ $stats['applications'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card p-3 text-center">
                <div class="stat-label">Shortlisted</div>
                <div class="stat-value">{{ $stats['shortlisted'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card p-3 text-center">
                <div class="stat-label">Interviews</div>
                <div class="stat-value">{{ $stats['interviews'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card p-3 text-center">
                <div class="stat-label">Offers</div>
                <div class="stat-value">{{ $stats['offers'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card p-3 text-center">
                <div class="stat-label">Hired</div>
                <div class="stat-value">{{ $stats['hired'] }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Job Information</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Salary</dt>
                    <dd class="col-7">{{ $posting->currency }} {{ number_format($posting->salary_min, 0) }}@if($posting->salary_max) – {{ number_format($posting->salary_max, 0) }}@endif</dd>
                    <dt class="col-5 text-secondary fw-normal">Employment Type</dt><dd class="col-7 text-capitalize">{{ $posting->employment_type }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Positions</dt><dd class="col-7">{{ $posting->vacancies }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Experience</dt><dd class="col-7">{{ $posting->experience_required ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Education</dt><dd class="col-7">{{ $posting->education_requirement ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Working Hours</dt><dd class="col-7">{{ $posting->working_hours ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Application Deadline</dt><dd class="col-7">{{ $posting->application_deadline?->format('d M Y') ?? '—' }}</dd>
                </dl>

                @if($posting->description)
                    <hr>
                    <strong class="small">Description</strong>
                    <p class="small mb-0 mt-1">{{ $posting->description }}</p>
                @endif
                @if($posting->requirements)
                    <hr>
                    <strong class="small">Requirements</strong>
                    <p class="small mb-0 mt-1">{{ $posting->requirements }}</p>
                @endif
                @if($posting->benefits)
                    <hr>
                    <strong class="small">Benefits</strong>
                    <p class="small mb-0 mt-1">{{ $posting->benefits }}</p>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Recruitment Timeline</h3>
                @php
                    $steps = [
                        ['label' => 'Job Published', 'done' => in_array($posting->status, ['open', 'closed', 'filled'])],
                        ['label' => 'Candidates Received', 'done' => $stats['applications'] > 0],
                        ['label' => 'Candidates Shortlisted', 'done' => $stats['shortlisted'] > 0],
                        ['label' => 'Interviews', 'done' => $stats['interviews'] > 0],
                        ['label' => 'Job Offers', 'done' => $stats['offers'] > 0],
                        ['label' => 'Deployment', 'done' => $stats['hired'] > 0],
                    ];
                @endphp
                @foreach($steps as $step)
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fa-solid {{ $step['done'] ? 'fa-circle-check text-success' : 'fa-circle text-secondary' }}"></i>
                        <span class="{{ $step['done'] ? '' : 'text-secondary' }}">{{ $step['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Coming Soon</h3>
                <p class="text-secondary small mb-0">Viewing individual candidates, shortlists, interviews, and offers for this job will be available in an upcoming update.</p>
            </div>
        </div>
    </div>

</x-employer-layout>
