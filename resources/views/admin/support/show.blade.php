<x-admin-layout title="Ticket Details">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.support.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Support Tickets</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $ticket->subject }}</h2>
                <p class="text-secondary mb-0">
                    {{ $ticket->ticket_number }} · {{ $ticket->category }} · <span class="text-capitalize">{{ $ticket->priority }}</span> priority
                    · Submitted by {{ $ticket->student?->name ?? '—' }}
                    · Assigned to {{ $ticket->assignedTo?->name ?? 'Unassigned' }}
                </p>
            </div>
            <div class="text-end">
                <span class="badge bg-primary-subtle text-primary-emphasis fs-6 d-block mb-2">{{ ucwords(str_replace('_', ' ', $ticket->status)) }}</span>
                <div class="d-flex gap-1">
                    @can('respond', $ticket)
                        <form method="POST" action="{{ route('admin.support.assign', $ticket) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Assign to Me</button>
                        </form>
                    @endcan
                    @can('close', $ticket)
                        @if($ticket->status !== 'closed')
                            <form method="POST" action="{{ route('admin.support.close', $ticket) }}" onsubmit="return confirm('Close this ticket?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">Close Ticket</button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- Message thread --}}
    <div class="card stat-card p-4 mb-4">
        @foreach($ticket->messages as $message)
            @php $isStaff = $message->author?->isStaff() ?? false; @endphp
            <div class="d-flex {{ $isStaff ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                <div style="max-width: 75%;">
                    <div class="small text-secondary mb-1 {{ $isStaff ? 'text-end' : '' }}">
                        {{ $message->author->name }} · {{ $message->created_at->format('d M Y, g:ia') }}
                    </div>
                    <div class="p-3 rounded-3 {{ $isStaff ? 'bg-primary text-white' : 'bg-light' }}">
                        {{ $message->body }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Reply --}}
    @if($ticket->status !== 'closed')
        <div class="card stat-card p-4">
            <form method="POST" action="{{ route('admin.support.reply', $ticket) }}">
                @csrf
                <div class="mb-3">
                    <textarea name="body" class="form-control" rows="3" required placeholder="Type a reply..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Reply</button>
            </form>
        </div>
    @else
        <div class="text-center text-secondary">This ticket is closed.</div>
    @endif

</x-admin-layout>
