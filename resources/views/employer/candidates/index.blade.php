<x-employer-layout title="Candidates">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Candidates</h2>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        {{-- Status filter tabs — "Shortlisted Candidates" from the spec is
             this same page filtered, not a separate one. Each tab preserves
             the current Job filter so the two filters compose. --}}
        <ul class="nav nav-pills flex-wrap gap-1 mb-0">
            @foreach(['all' => 'All', 'shortlisted' => 'Shortlisted', 'interview' => 'Interview', 'offers' => 'Offers', 'hired' => 'Hired'] as $key => $label)
                <li class="nav-item">
                    <a class="nav-link {{ $activeFilter === $key ? 'active' : '' }}" href="{{ route('employer.candidates.index', ['filter' => $key, 'job_posting_id' => request('job_posting_id')]) }}">{{ $label }}</a>
                </li>
            @endforeach
        </ul>

        {{-- Job filter — preserves the current status filter when changed. --}}
        <form method="GET" action="{{ route('employer.candidates.index') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="filter" value="{{ $activeFilter }}">
            <label class="form-label small fw-semibold mb-0 text-nowrap">Job</label>
            <select name="job_posting_id" class="form-select form-select-sm" style="min-width:220px;" onchange="this.form.submit()">
                <option value="">All Jobs</option>
                @foreach($jobPostings as $posting)
                    <option value="{{ $posting->id }}" @selected((int) request('job_posting_id') === $posting->id)>{{ $posting->title }} — {{ $posting->country }}</option>
                @endforeach
            </select>
            @if(request('job_posting_id'))
                <a href="{{ route('employer.candidates.index', ['filter' => $activeFilter]) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Candidate</th>
                        <th>Job</th>
                        <th>Experience</th>
                        <th>Country</th>
                        <th>Applied</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $application)
                        @php
                            $topExperience = $application->jobSeeker->jobSeekerProfile?->experiences?->first();
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $application->jobSeeker->name }}</td>
                            <td class="small">{{ $application->jobPosting->title }}</td>
                            <td class="small">{{ $topExperience?->years_of_experience ? $topExperience->years_of_experience.' yrs' : '—' }}</td>
                            <td class="small">{{ $application->jobSeeker->jobSeekerProfile?->country ?? '—' }}</td>
                            <td class="small text-secondary">{{ $application->applied_at?->format('d M Y') ?? '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = match(true) {
                                        in_array($application->currentStatus->slug, ['rejected', 'withdrawn']) => 'bg-danger-subtle text-danger-emphasis',
                                        $application->currentStatus->slug === 'deployed' => 'bg-success-subtle text-success-emphasis',
                                        in_array($application->currentStatus->slug, ['offer_extended', 'offer_accepted', 'selected']) => 'bg-warning-subtle text-warning-emphasis',
                                        default => 'bg-primary-subtle text-primary-emphasis',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $application->currentStatus->label }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employer.candidates.show', $application) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">
                                No candidates match these filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $candidates->links() }}</div>

</x-employer-layout>
