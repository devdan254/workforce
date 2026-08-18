<x-employer-layout title="Notifications">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Notifications</h2>
        @if($notifications->contains(fn($n) => is_null($n->read_at)))
            <form method="POST" action="{{ route('employer.notifications.read_all') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">Mark All as Read</button>
            </form>
        @endif
    </div>

    <div class="card stat-card">
        @forelse($notifications as $notification)
            <form method="POST" action="{{ route('employer.notifications.read', $notification->id) }}"
                  class="d-flex align-items-start gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }} {{ is_null($notification->read_at) ? 'bg-primary-subtle bg-opacity-10' : '' }}">
                @csrf
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;">
                    <i class="fa-solid fa-{{ $notification->data['icon'] ?? 'bell' }}"></i>
                </div>
                <button type="submit" class="btn btn-link text-start text-decoration-none p-0 flex-grow-1">
                    <div class="fw-semibold {{ is_null($notification->read_at) ? 'text-dark' : 'text-secondary' }}">
                        {{ $notification->data['title'] ?? 'Notification' }}
                        @if(is_null($notification->read_at))
                            <span class="badge bg-primary ms-1" style="font-size:.6rem;">NEW</span>
                        @endif
                    </div>
                    <div class="text-secondary small">{{ $notification->data['body'] ?? '' }}</div>
                    <div class="text-secondary" style="font-size:.75rem;">{{ $notification->created_at->diffForHumans() }}</div>
                </button>
            </form>
        @empty
            <div class="p-5 text-center text-secondary">No notifications yet.</div>
        @endforelse
    </div>

    <div class="mt-3">{{ $notifications->links() }}</div>

</x-employer-layout>
