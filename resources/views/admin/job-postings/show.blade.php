<x-admin-layout title="Job Posting">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.job-postings.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Job Postings</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $posting->title }}
                    @if($posting->is_featured)
                        <span class="badge bg-warning-subtle text-warning-emphasis ms-1">Featured</span>
                    @endif
                </h2>
                <p class="text-secondary mb-0">{{ $posting->category->name }} · {{ $posting->city ? $posting->city.', ' : '' }}{{ $posting->country }}</p>
                @if($posting->employer_id)
                    <p class="text-secondary small mt-1 mb-0">
                        Employer:
                        @if(\Illuminate\Support\Facades\Route::has('admin.employers.show'))
                            <a href="{{ route('admin.employers.show', $posting->employer_id) }}">{{ $posting->employer?->employerProfile?->company_name ?? $posting->employer?->name }}</a>
                        @else
                            {{ $posting->employer?->employerProfile?->company_name ?? $posting->employer?->name }}
                        @endif
                    </p>
                @endif
            </div>
            @php
                $badgeClass = match($posting->status) {
                    'open' => 'bg-success-subtle text-success-emphasis',
                    'draft' => 'bg-secondary-subtle text-secondary-emphasis',
                    'closed' => 'bg-warning-subtle text-warning-emphasis',
                    'filled' => 'bg-primary-subtle text-primary-emphasis',
                    'archived' => 'bg-danger-subtle text-danger-emphasis',
                    default => 'bg-light text-dark',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($posting->status) }}</span>
        </div>

        {{-- Same action set as the listing page, all reusing the exact same routes.
             View Applicants is its own separate page, not a section of this one. --}}
        <hr>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.job-postings.applicants', $posting) }}" class="btn btn-sm btn-primary">View Applicants ({{ $posting->applications_count }})</a>
            @can('update', $posting)
                <a href="{{ route('admin.job-postings.edit', $posting) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            @endcan

            @can('publish', $posting)
                @if($posting->status === 'draft')
                    <form method="POST" action="{{ route('admin.job-postings.publish', $posting) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Publish</button>
                    </form>
                @elseif($posting->status === 'open')
                    <form method="POST" action="{{ route('admin.job-postings.unpublish', $posting) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Unpublish</button>
                    </form>
                    <form method="POST" action="{{ route('admin.job-postings.close', $posting) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning">Close</button>
                    </form>
                @endif
                @if($posting->status !== 'archived')
                    <form method="POST" action="{{ route('admin.job-postings.archive', $posting) }}" onsubmit="return confirm('Archive this posting?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Archive</button>
                    </form>
                @endif
            @endcan

            <form method="POST" action="{{ route('admin.job-postings.feature', $posting) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">{{ $posting->is_featured ? 'Unfeature' : 'Feature' }}</button>
            </form>

            <form method="POST" action="{{ route('admin.job-postings.duplicate', $posting) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">Duplicate</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Job Details</h3>
                <dl class="row small mb-0">
                    <dt class="col-4 text-secondary fw-normal">Positions</dt><dd class="col-8">{{ $posting->vacancies }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Employment Type</dt><dd class="col-8 text-capitalize">{{ $posting->employment_type }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Salary</dt>
                    <dd class="col-8">
                        {{ $posting->currency }} {{ number_format($posting->salary_min, 0) }}@if($posting->salary_max) – {{ number_format($posting->salary_max, 0) }}@endif
                    </dd>
                    <dt class="col-4 text-secondary fw-normal">Experience</dt><dd class="col-8">{{ $posting->experience_required ?? '—' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Education</dt><dd class="col-8">{{ $posting->education_requirement ?? '—' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Working Hours</dt><dd class="col-8">{{ $posting->working_hours ?? '—' }}</dd>
                    <dt class="col-4 text-secondary fw-normal">Application Deadline</dt><dd class="col-8">{{ $posting->application_deadline?->format('d M Y') ?? '—' }}</dd>
                </dl>

                @if($posting->description)
                    <hr><strong class="small">Description</strong><p class="small mb-0 mt-1">{{ $posting->description }}</p>
                @endif
                @if($posting->responsibilities)
                    <hr><strong class="small">Responsibilities</strong><p class="small mb-0 mt-1">{{ $posting->responsibilities }}</p>
                @endif
                @if($posting->requirements)
                    <hr><strong class="small">Requirements</strong><p class="small mb-0 mt-1">{{ $posting->requirements }}</p>
                @endif
                @if($posting->skills)
                    <hr><strong class="small">Skills</strong><p class="small mb-0 mt-1">{{ $posting->skills }}</p>
                @endif
                @if($posting->languages)
                    <hr><strong class="small">Languages</strong><p class="small mb-0 mt-1">{{ $posting->languages }}</p>
                @endif
                @if($posting->benefits)
                    <hr><strong class="small">Benefits</strong><p class="small mb-0 mt-1">{{ $posting->benefits }}</p>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">What's Included</h3>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="fa-solid {{ $posting->accommodation_provided ? 'fa-circle-check text-success' : 'fa-circle-xmark text-secondary' }}"></i> Accommodation</li>
                    <li class="mb-2"><i class="fa-solid {{ $posting->meals_provided ? 'fa-circle-check text-success' : 'fa-circle-xmark text-secondary' }}"></i> Meals</li>
                    <li class="mb-2"><i class="fa-solid {{ $posting->visa_support_provided ? 'fa-circle-check text-success' : 'fa-circle-xmark text-secondary' }}"></i> Visa Support</li>
                    <li class="mb-0"><i class="fa-solid {{ $posting->air_ticket_provided ? 'fa-circle-check text-success' : 'fa-circle-xmark text-secondary' }}"></i> Air Ticket</li>
                </ul>
            </div>

            @if($posting->image_url)
                <div class="card stat-card p-3">
                    <img src="{{ $posting->image_url }}" alt="{{ $posting->title }}" class="w-100 rounded" style="max-height:180px;object-fit:cover;">
                </div>
            @endif
        </div>
    </div>

</x-admin-layout>
