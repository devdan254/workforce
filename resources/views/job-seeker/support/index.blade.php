<x-job-seeker-layout title="Support">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Support</h2>
        <a href="{{ route('job-seeker.support.create') }}" class="btn btn-primary">+ Raise Ticket</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card p-3 h-100 text-center">
                <i class="fa-solid fa-comments fs-3 text-primary mb-2"></i>
                <div class="fw-semibold">Live Chat</div>
                <a href="#" class="small">Start Chat</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 h-100 text-center">
                <i class="fa-solid fa-phone fs-3 text-primary mb-2"></i>
                <div class="fw-semibold">Call Altura</div>
                <a href="tel:+254758434825" class="small">+254 758 434 825</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 h-100 text-center">
                <i class="fa-solid fa-envelope fs-3 text-primary mb-2"></i>
                <div class="fw-semibold">Email Support</div>
                <a href="mailto:alturaworkforcesolutions@gmail.com" class="small">Send Email</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 h-100 text-center">
                <i class="fa-solid fa-ticket fs-3 text-primary mb-2"></i>
                <div class="fw-semibold">Raise Ticket</div>
                <a href="{{ route('job-seeker.support.create') }}" class="small">Create Ticket</a>
            </div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ticket</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="fw-semibold">{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->category }}</td>
                            <td class="text-capitalize">{{ $ticket->priority }}</td>
                            <td>
                                @php
                                    $badgeClass = match($ticket->status) {
                                        'resolved', 'closed' => 'bg-success-subtle text-success-emphasis',
                                        'waiting_for_student' => 'bg-warning-subtle text-warning-emphasis',
                                        default => 'bg-primary-subtle text-primary-emphasis',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $ticket->status)) }}</span>
                            </td>
                            <td>{{ $ticket->updated_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('job-seeker.support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">
                                No support tickets yet. <a href="{{ route('job-seeker.support.create') }}">Raise one →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $tickets->links() }}</div>

</x-job-seeker-layout>
