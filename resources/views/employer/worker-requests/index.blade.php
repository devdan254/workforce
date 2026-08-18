<x-employer-layout title="Worker Requests">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Worker Requests</h2>
        <a href="{{ route('employer.worker-requests.create') }}" class="btn btn-primary">+ Request Workers</a>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Job Title</th>
                        <th>Quantity</th>
                        <th>Employment Type</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $workerRequest)
                        <tr>
                            <td class="fw-semibold">{{ $workerRequest->job_title }}</td>
                            <td>{{ $workerRequest->quantity }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $workerRequest->employment_type) }}</td>
                            <td class="small text-secondary">{{ $workerRequest->created_at->format('d M Y') }}</td>
                            <td>
                                @php
                                    $badgeClass = match($workerRequest->status) {
                                        'approved', 'converted' => 'bg-success-subtle text-success-emphasis',
                                        'rejected' => 'bg-danger-subtle text-danger-emphasis',
                                        'clarification_required' => 'bg-warning-subtle text-warning-emphasis',
                                        default => 'bg-primary-subtle text-primary-emphasis',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $workerRequest->status)) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employer.worker-requests.show', $workerRequest) }}" class="btn btn-sm btn-outline-primary">View</a>
                                @if($workerRequest->job_posting_id)
                                    <a href="{{ route('employer.jobs.index') }}" class="btn btn-sm btn-outline-success">View Job →</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                No worker requests yet. <a href="{{ route('employer.worker-requests.create') }}">Submit your first request →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $requests->links() }}</div>

</x-employer-layout>
