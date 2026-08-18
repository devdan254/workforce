<x-admin-layout title="Worker Requests">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Worker Requests</h2>

    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.worker-requests.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['submitted', 'under_review', 'clarification_required', 'approved', 'rejected', 'converted'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Company</th>
                        <th>Job Title</th>
                        <th>Quantity</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $workerRequest)
                        <tr>
                            <td class="fw-semibold small">{{ $workerRequest->employer->employerProfile?->company_name ?? $workerRequest->employer->name }}</td>
                            <td class="small">{{ $workerRequest->job_title }}</td>
                            <td class="small">{{ $workerRequest->quantity }}</td>
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
                                <a href="{{ route('admin.worker-requests.show', $workerRequest) }}" class="btn btn-sm btn-outline-primary">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No worker requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $requests->links() }}</div>

</x-admin-layout>
