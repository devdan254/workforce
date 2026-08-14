<x-admin-layout title="Dashboard">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Operations Overview</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Total Students</div>
                <div class="stat-value">{{ number_format($stats['total_students']) }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Active Applications</div>
                <div class="stat-value">{{ number_format($stats['active_applications']) }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Documents Awaiting Review</div>
                <div class="stat-value {{ $stats['documents_awaiting_review'] > 0 ? 'text-warning' : '' }}">{{ number_format($stats['documents_awaiting_review']) }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Pending Payments</div>
                <div class="stat-value {{ $stats['pending_payments'] > 0 ? 'text-warning' : '' }}">{{ number_format($stats['pending_payments']) }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Visa Applications</div>
                <div class="stat-value">{{ number_format($stats['visa_applications']) }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Upcoming Appointments</div>
                <div class="stat-value">{{ number_format($stats['upcoming_appointments']) }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Open Tickets</div>
                <div class="stat-value {{ $stats['open_tickets'] > 0 ? 'text-danger' : '' }}">{{ number_format($stats['open_tickets']) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Recently Registered Students</h3>
                    <a href="{{ route('admin.students.index') }}" class="small">View All →</a>
                </div>
                @forelse($recentStudents as $student)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold">{{ $student->name }}</div>
                            <div class="small text-secondary">{{ $student->email }} · {{ $student->studentProfile?->country ?? 'Country not set' }}</div>
                        </div>
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No students yet.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Open Support Tickets</h3>
                @forelse($recentTickets as $ticket)
                    <div class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold small">{{ $ticket->subject }}</span>
                            <span class="badge bg-warning-subtle text-warning-emphasis">{{ ucwords(str_replace('_', ' ', $ticket->status)) }}</span>
                        </div>
                        <div class="text-secondary small">{{ $ticket->student->name }} · {{ $ticket->ticket_number }}</div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No open tickets.</p>
                @endforelse
            </div>
        </div>
    </div>

</x-admin-layout>
