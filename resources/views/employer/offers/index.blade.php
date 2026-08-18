<x-employer-layout title="Job Offers">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Job Offers</h2>

        <form method="GET" action="{{ route('employer.offers.index') }}" class="d-flex align-items-center gap-2">
            <label class="form-label small fw-semibold mb-0 text-nowrap">Job</label>
            <select name="job_posting_id" class="form-select form-select-sm" style="min-width:220px;" onchange="this.form.submit()">
                <option value="">All Jobs</option>
                @foreach($jobPostings as $posting)
                    <option value="{{ $posting->id }}" @selected((int) request('job_posting_id') === $posting->id)>{{ $posting->title }} — {{ $posting->country }}</option>
                @endforeach
            </select>
            @if(request('job_posting_id'))
                <a href="{{ route('employer.offers.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
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
                        <th>Salary</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr>
                            <td class="fw-semibold small">{{ $offer->jobApplication->jobSeeker->name }}</td>
                            <td class="small">{{ $offer->jobApplication->jobPosting->title }}</td>
                            <td class="small">{{ $offer->currency }} {{ number_format($offer->salary, 0) }}</td>
                            <td class="small">{{ $offer->start_date?->format('d M Y') ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $offer->status === 'accepted' ? 'bg-success-subtle text-success-emphasis' : ($offer->status === 'declined' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis') }}">
                                    {{ ucfirst($offer->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employer.offers.show', $offer) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                No job offers match these filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $offers->links() }}</div>

</x-employer-layout>
