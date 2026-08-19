<x-admin-layout title="Job Posting Applicants">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.job-postings.show', $posting) }}" class="text-decoration-none small mb-3 d-inline-block">← Back to {{ $posting->title }}</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $posting->title }}</h2>
                <p class="text-secondary mb-0">{{ $posting->category->name }} · {{ $posting->city ? $posting->city.', ' : '' }}{{ $posting->country }} · {{ $applications->count() }} applicant{{ $applications->count() !== 1 ? 's' : '' }}</p>
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
    </div>

    <div class="card stat-card">
        <div class="p-3 border-bottom bg-light">
            <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Applicants</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Candidate</th>
                        <th>Country</th>
                        <th>Applied</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td class="fw-semibold">{{ $application->jobSeeker->name }}</td>
                            <td class="small">{{ $application->jobSeeker->jobSeekerProfile?->country ?? '—' }}</td>
                            <td class="small text-secondary">{{ $application->applied_at?->format('d M Y') ?? '—' }}</td>
                            <td>
                                @php
                                    $statusBadge = match(true) {
                                        in_array($application->currentStatus->slug, ['rejected', 'withdrawn']) => 'bg-danger-subtle text-danger-emphasis',
                                        $application->currentStatus->slug === 'deployed' => 'bg-success-subtle text-success-emphasis',
                                        in_array($application->currentStatus->slug, ['offer_extended', 'offer_accepted', 'selected']) => 'bg-warning-subtle text-warning-emphasis',
                                        default => 'bg-primary-subtle text-primary-emphasis',
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }}">{{ $application->currentStatus->label }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.job-seekers.show', $application->job_seeker_id) }}" class="btn btn-sm btn-outline-primary">View in Workspace</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-5">No applications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-admin-layout>
