<x-employer-layout title="Hired Workers">

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-1">
        <div>
            <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Hired Workers</h2>
            <p class="text-secondary mb-0">Candidates who have accepted an offer, tracked through visa and deployment.</p>
        </div>

        <form method="GET" action="{{ route('employer.workers.index') }}" class="d-flex align-items-center gap-2">
            <label class="form-label small fw-semibold mb-0 text-nowrap">Job</label>
            <select name="job_posting_id" class="form-select form-select-sm" style="min-width:220px;" onchange="this.form.submit()">
                <option value="">All Jobs</option>
                @foreach($jobPostings as $posting)
                    <option value="{{ $posting->id }}" @selected((int) request('job_posting_id') === $posting->id)>{{ $posting->title }} — {{ $posting->country }}</option>
                @endforeach
            </select>
            @if(request('job_posting_id'))
                <a href="{{ route('employer.workers.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    <div class="card stat-card mt-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Worker</th>
                        <th>Job</th>
                        <th>Country</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th>Visa Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workers as $application)
                        <tr>
                            <td class="fw-semibold">{{ $application->jobSeeker->name }}</td>
                            <td class="small">{{ $application->jobPosting->title }}</td>
                            <td class="small">{{ $application->jobPosting->country }}</td>
                            <td class="small">{{ $application->offer?->start_date?->format('d M Y') ?? '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = $application->currentStatus->slug === 'deployed'
                                        ? 'bg-success-subtle text-success-emphasis'
                                        : 'bg-primary-subtle text-primary-emphasis';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $application->currentStatus->label }}</span>
                            </td>
                            <td class="small">
                                {{ $application->visaApplication?->currentStatus?->label ?? 'Not Started' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employer.candidates.show', $application) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">
                                No hired workers match these filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $workers->links() }}</div>

</x-employer-layout>
