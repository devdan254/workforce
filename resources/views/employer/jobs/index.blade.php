<x-employer-layout title="My Jobs">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">My Jobs</h2>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Job</th>
                        <th>Country</th>
                        <th>Openings</th>
                        <th>Applicants</th>
                        <th>Shortlisted</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postings as $posting)
                        <tr>
                            <td class="fw-semibold">{{ $posting->title }}</td>
                            <td class="small">{{ $posting->country }}</td>
                            <td class="small">{{ $posting->vacancies }}</td>
                            <td class="small">{{ $posting->applications_total }}</td>
                            <td class="small">{{ $posting->shortlisted_total }}</td>
                            <td>
                                @php
                                    $badgeClass = match($posting->status) {
                                        'open' => 'bg-success-subtle text-success-emphasis',
                                        'closed' => 'bg-warning-subtle text-warning-emphasis',
                                        'filled' => 'bg-primary-subtle text-primary-emphasis',
                                        'archived' => 'bg-danger-subtle text-danger-emphasis',
                                        default => 'bg-secondary-subtle text-secondary-emphasis',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($posting->status) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employer.jobs.show', $posting) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">
                                No jobs yet. Once Altura approves and publishes a job from one of your Worker Requests, it'll show up here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $postings->links() }}</div>

</x-employer-layout>
