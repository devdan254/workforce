<x-student-layout title="Visa Status">

    <a href="{{ route('student.applications.show', $application) }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Application</a>

    <div class="card stat-card p-4 mb-4">
        <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
            {{ $visa?->destination_country ?? $application->university->country }} Student Visa
        </h2>
        <p class="text-secondary mb-3">{{ $application->university->name }} — {{ $application->course->name }}</p>
        <span class="badge bg-primary-subtle text-primary-emphasis fs-6">
            {{ $visa?->currentStatus?->label ?? 'Not Yet Started' }}
        </span>
    </div>

    @if($visa)
        <div class="card stat-card p-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Visa Timeline</h3>
            <div class="altura-timeline">
                @foreach($visa->statusHistories->sortBy('created_at') as $history)
                    <div class="altura-timeline-step {{ $history->to_status_id === $visa->status_id ? 'is-current' : 'is-done' }}">
                        <div class="altura-timeline-dot">
                            <i class="fa-solid {{ $history->to_status_id === $visa->status_id ? 'fa-hourglass-half' : 'fa-check' }}"></i>
                        </div>
                        <div class="fw-semibold">{{ $history->toStatus->label }}</div>
                        <div class="text-secondary small">{{ $history->created_at->format('d F Y, g:ia') }}</div>
                        @if($history->note)
                            <div class="text-secondary small fst-italic mt-1">{{ $history->note }}</div>
                        @endif
                    </div>
                @endforeach
                @foreach($nextStatuses as $next)
                    <div class="altura-timeline-step">
                        <div class="altura-timeline-dot"><i class="fa-solid fa-circle" style="font-size:6px;"></i></div>
                        <div class="text-secondary">{{ $next->label }}</div>
                    </div>
                @endforeach
            </div>

            @if($visa->embassy_appointment_at)
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-secondary">Embassy Appointment</span>
                    <span class="fw-semibold">{{ $visa->embassy_appointment_at->format('d F Y, g:ia') }}</span>
                </div>
            @endif
        </div>
    @else
        <div class="card stat-card p-4 text-center text-secondary">
            Your visa process begins once your admission is accepted. Nothing to show yet.
        </div>
    @endif

</x-student-layout>
