<x-admin-layout title="Support Tickets">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Support Tickets</h2>

    <div class="card stat-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.support.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Subject or ticket #">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['open', 'in_progress', 'waiting_for_student', 'resolved', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Priority</label>
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    @foreach(['low', 'medium', 'high', 'urgent'] as $priority)
                        <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ ucfirst($priority) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach(['Applications', 'Documents', 'Payments', 'Visa', 'Travel', 'Other'] as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 form-check ms-2">
                <input type="checkbox" name="mine" value="1" class="form-check-input" id="mine-filter" @checked(request('mine')) onchange="this.form.submit()">
                <label class="form-check-label small" for="mine-filter">Assigned to me</label>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ticket #</th>
                        <th>Submitted By</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="fw-semibold small">{{ $ticket->ticket_number }}</td>
                            <td class="small">{{ $ticket->student?->name ?? '—' }}</td>
                            <td class="small">{{ $ticket->subject }}</td>
                            <td class="small text-secondary">{{ $ticket->category ?? '—' }}</td>
                            <td>
                                <span class="badge {{ match($ticket->priority) { 'urgent' => 'bg-danger-subtle text-danger-emphasis', 'high' => 'bg-warning-subtle text-warning-emphasis', default => 'bg-secondary-subtle text-secondary-emphasis' } }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td class="small text-secondary">{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</td>
                            <td>
                                <span class="badge {{ match($ticket->status) { 'closed' => 'bg-secondary-subtle text-secondary-emphasis', 'resolved' => 'bg-success-subtle text-success-emphasis', 'open' => 'bg-danger-subtle text-danger-emphasis', default => 'bg-primary-subtle text-primary-emphasis' } }}">{{ ucwords(str_replace('_', ' ', $ticket->status)) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-5">No tickets match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $tickets->links() }}</div>

</x-admin-layout>
