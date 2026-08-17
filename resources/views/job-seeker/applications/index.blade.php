<x-job-seeker-layout title="My Applications">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">My Applications</h2>
        <a href="{{ route('job-seeker.jobs.index') }}" class="btn btn-primary">Find More Jobs →</a>
    </div>

    {{-- Filter tabs --}}
    <ul class="nav nav-pills mb-4 flex-wrap gap-1">
        @foreach(['all' => 'All', 'active' => 'Active', 'interview' => 'Interview', 'offers' => 'Offers', 'completed' => 'Completed', 'not_selected' => 'Not Selected'] as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ $activeFilter === $key ? 'active' : '' }}" href="{{ route('job-seeker.applications.index', ['filter' => $key]) }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Job</th>
                        <th>Country</th>
                        <th>Applied</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td class="fw-semibold">{{ $application->jobPosting->title }}</td>
                            <td>{{ $application->jobPosting->country }}</td>
                            <td>{{ $application->applied_at?->format('d M Y') ?? '—' }}</td>
                            <td>
                                {{ $application->jobPosting->currency }} {{ number_format($application->jobPosting->salary_min, 0) }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = match(true) {
                                        in_array($application->currentStatus->slug, ['rejected', 'withdrawn']) => 'bg-danger-subtle text-danger-emphasis',
                                        in_array($application->currentStatus->slug, ['deployed']) => 'bg-success-subtle text-success-emphasis',
                                        in_array($application->currentStatus->slug, ['offer_extended', 'offer_accepted', 'selected']) => 'bg-warning-subtle text-warning-emphasis',
                                        default => 'bg-primary-subtle text-primary-emphasis',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $application->currentStatus->label }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('job-seeker.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                No applications in this category yet. <a href="{{ route('job-seeker.jobs.index') }}">Browse jobs →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $applications->links() }}</div>

</x-job-seeker-layout>
