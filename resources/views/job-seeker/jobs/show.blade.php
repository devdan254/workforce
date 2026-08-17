<x-job-seeker-layout title="Job Details">

    <a href="{{ route('job-seeker.jobs.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Available Jobs</a>

    <div class="card stat-card mb-4 overflow-hidden">
        <div class="d-flex align-items-center justify-content-center" style="height:220px;background:{{ $jobPosting->colorForCategory() }};">
            @if($jobPosting->image_url)
                <img src="{{ $jobPosting->image_url }}" alt="{{ $jobPosting->title }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                <i class="fa-solid {{ $jobPosting->iconForCategory() }} text-white" style="font-size:4.5rem;opacity:.9;"></i>
            @endif
        </div>
        <div class="p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                @if($jobPosting->is_featured)
                    <span class="badge bg-primary-subtle text-primary-emphasis mb-2">Featured</span>
                @endif
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $jobPosting->title }}</h2>
                <p class="text-secondary mb-0">{{ $jobPosting->category->name }} · {{ $jobPosting->city ? $jobPosting->city.', ' : '' }}{{ $jobPosting->country }}</p>
            </div>
            <div class="text-end">
                <div class="fs-4 fw-semibold text-primary">
                    {{ $jobPosting->currency }} {{ number_format($jobPosting->salary_min, 0) }}@if($jobPosting->salary_max) – {{ number_format($jobPosting->salary_max, 0) }}@endif
                </div>
                <div class="text-secondary small">per month</div>
            </div>
        </div>

        <hr>

        @if($alreadyApplied)
            <div class="alert alert-info mb-0">
                <i class="fa-solid fa-circle-check"></i> You've already applied to this job.
                <a href="{{ route('job-seeker.applications.index') }}">View your applications →</a>
            </div>
        @else
            <a href="{{ route('job-seeker.jobs.apply', $jobPosting) }}" class="btn btn-primary btn-lg">Apply Now</a>
        @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Description</h3>
                <p class="mb-0">{{ $jobPosting->description }}</p>
            </div>

            @if($jobPosting->requirements)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Requirements</h3>
                    <p class="mb-0">{{ $jobPosting->requirements }}</p>
                </div>
            @endif

            @if($jobPosting->benefits)
                <div class="card stat-card p-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Benefits</h3>
                    <p class="mb-0">{{ $jobPosting->benefits }}</p>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Job Information</h3>
                <dl class="row small mb-0">
                    <dt class="col-6 text-secondary fw-normal">Employment Type</dt><dd class="col-6 text-capitalize">{{ $jobPosting->employment_type }}</dd>
                    <dt class="col-6 text-secondary fw-normal">Experience</dt><dd class="col-6">{{ $jobPosting->experience_required ?? '—' }}</dd>
                    <dt class="col-6 text-secondary fw-normal">Education</dt><dd class="col-6">{{ $jobPosting->education_requirement ?? '—' }}</dd>
                    <dt class="col-6 text-secondary fw-normal">Vacancies</dt><dd class="col-6">{{ $jobPosting->vacancies }}</dd>
                    <dt class="col-6 text-secondary fw-normal">Working Hours</dt><dd class="col-6">{{ $jobPosting->working_hours ?? '—' }}</dd>
                    <dt class="col-6 text-secondary fw-normal">Deadline</dt><dd class="col-6">{{ $jobPosting->application_deadline?->format('d M Y') ?? '—' }}</dd>
                </dl>
            </div>

            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">What's Included</h3>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><i class="fa-solid {{ $jobPosting->accommodation_provided ? 'fa-circle-check text-success' : 'fa-circle-xmark text-secondary' }} me-2"></i>Accommodation</li>
                    <li class="mb-2"><i class="fa-solid {{ $jobPosting->meals_provided ? 'fa-circle-check text-success' : 'fa-circle-xmark text-secondary' }} me-2"></i>Meals</li>
                    <li class="mb-2"><i class="fa-solid {{ $jobPosting->visa_support_provided ? 'fa-circle-check text-success' : 'fa-circle-xmark text-secondary' }} me-2"></i>Visa Support</li>
                    <li class="mb-0"><i class="fa-solid {{ $jobPosting->air_ticket_provided ? 'fa-circle-check text-success' : 'fa-circle-xmark text-secondary' }} me-2"></i>Air Ticket</li>
                </ul>
            </div>
        </div>
    </div>

</x-job-seeker-layout>
