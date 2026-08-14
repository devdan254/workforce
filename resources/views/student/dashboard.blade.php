<x-student-layout title="Dashboard">

    {{-- Welcome --}}
    <div class="mb-4">
        <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
            Welcome back, {{ explode(' ', $student->name)[0] }}!
        </h2>
        <p class="text-secondary mb-0">
            @if($primaryApplication)
                Your application to study abroad is progressing. Here's what needs your attention.
            @else
                Let's get your study abroad journey started.
            @endif
        </p>
    </div>

    {{-- Top Statistics --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Application Status</div>
                <div class="stat-value">{{ $primaryApplication?->currentStatus?->label ?? 'Not Started' }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Visa Status</div>
                <div class="stat-value">{{ $primaryApplication?->visaApplication?->currentStatus?->label ?? '—' }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Documents</div>
                <div class="stat-value">{{ $documentStats['uploaded'] }} Uploaded / {{ $documentStats['pending'] }} Pending</div>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="card stat-card p-3 mb-4">
        <div class="row text-center">
            <div class="col-4">
                <div class="stat-label">Total Amount</div>
                <div class="stat-value">{{ $financialSummary['currency'] }} {{ number_format($financialSummary['total'], 0) }}</div>
            </div>
            <div class="col-4">
                <div class="stat-label">Amount Paid</div>
                <div class="stat-value text-success">{{ $financialSummary['currency'] }} {{ number_format($financialSummary['paid'], 0) }}</div>
            </div>
            <div class="col-4">
                <div class="stat-label">Balance Due</div>
                <div class="stat-value {{ $financialSummary['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $financialSummary['currency'] }} {{ number_format($financialSummary['balance'], 0) }}
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            {{-- Application Progress --}}
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Application Progress</h3>

                @if($primaryApplication)
                    <div class="altura-timeline">
                        @php
                            $histories = $primaryApplication->statusHistories()->with('toStatus')->oldest()->get();
                            $currentStatusId = $primaryApplication->status_id;
                            $reachedIds = $histories->pluck('to_status_id')->push($currentStatusId)->unique();
                        @endphp

                        @foreach($histories as $history)
                            @php
                                $isCurrent = $history->to_status_id === $currentStatusId;
                            @endphp
                            <div class="altura-timeline-step {{ $isCurrent ? 'is-current' : 'is-done' }}">
                                <div class="altura-timeline-dot">
                                    <i class="fa-solid {{ $isCurrent ? 'fa-hourglass-half' : 'fa-check' }}"></i>
                                </div>
                                <div class="fw-semibold">{{ $history->toStatus->label }}</div>
                                <div class="text-secondary small">{{ $history->created_at->format('d F Y') }}</div>
                            </div>
                        @endforeach

                        @php
                            $nextStatuses = app(\App\Services\ApplicationStatusService::class)->allowedNextStatuses($primaryApplication);
                        @endphp
                        @foreach($nextStatuses as $next)
                            <div class="altura-timeline-step">
                                <div class="altura-timeline-dot"><i class="fa-solid fa-circle" style="font-size:6px;"></i></div>
                                <div class="text-secondary">{{ $next->label }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-secondary mb-3">You haven't started an application yet.</p>
                    <a href="{{ route('student.applications.index') }}" class="btn btn-primary">+ Start New Study Application</a>
                @endif
            </div>

            {{-- Outstanding Actions --}}
            @if(count($outstandingActions))
                <div class="card stat-card p-4 border-warning-subtle">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">
                        <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Your Attention Is Required
                    </h3>
                    <ul class="list-unstyled mb-3">
                        @foreach($outstandingActions as $action)
                            <li class="mb-2">
                                <a href="{{ $action['link'] }}" class="text-decoration-none">
                                    <i class="fa-solid fa-circle-exclamation text-warning me-2"></i>{{ $action['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('student.applications.index') }}" class="btn btn-outline-primary btn-sm">Complete Required Actions →</a>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            {{-- Recent Notifications --}}
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Recent Notifications</h3>
                @forelse($notifications as $notification)
                    <div class="d-flex align-items-start gap-2 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <i class="fa-solid fa-{{ $notification->data['icon'] ?? 'bell' }} text-primary mt-1"></i>
                        <div>
                            <div class="fw-semibold small">{{ $notification->data['title'] ?? 'Notification' }}</div>
                            <div class="text-secondary small">{{ $notification->data['body'] ?? '' }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-secondary small mb-0">No notifications yet.</p>
                @endforelse
                <a href="{{ route('student.notifications.index') }}" class="small">View All Notifications →</a>
            </div>

            {{-- Upcoming Appointments --}}
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Upcoming Appointments</h3>
                @forelse($upcomingAppointments as $appointment)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold small">{{ $appointment->type }}</div>
                            <div class="text-secondary small">{{ $appointment->scheduled_at->format('d M, g:ia') }}</div>
                        </div>
                        <span class="badge {{ $appointment->status === 'confirmed' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-secondary small mb-0">No upcoming appointments.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-student-layout>
