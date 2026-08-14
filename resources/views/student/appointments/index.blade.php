<x-student-layout title="Appointments">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Appointments</h2>
        <a href="{{ route('student.appointments.create') }}" class="btn btn-primary">+ Book Appointment</a>
    </div>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Appointment</th>
                        <th>Advisor</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->scheduled_at->format('d M Y, g:ia') }}</td>
                            <td class="fw-semibold">{{ $appointment->type }}</td>
                            <td>{{ $appointment->staff?->name ?? 'Not yet assigned' }}</td>
                            <td class="text-capitalize">{{ $appointment->mode }}</td>
                            <td>
                                @php
                                    $badgeClass = match($appointment->status) {
                                        'confirmed' => 'bg-success-subtle text-success-emphasis',
                                        'completed' => 'bg-secondary-subtle text-secondary-emphasis',
                                        'cancelled' => 'bg-danger-subtle text-danger-emphasis',
                                        default => 'bg-warning-subtle text-warning-emphasis',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td class="text-end">
                                @if($appointment->status === 'confirmed' && $appointment->mode === 'video')
                                    <span class="small text-secondary">Meeting link shared before the appointment</span>
                                @endif
                                @if(in_array($appointment->status, ['requested', 'confirmed']) && $appointment->scheduled_at->isFuture())
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reschedule-{{ $appointment->id }}">Reschedule</button>
                                    <form method="POST" action="{{ route('student.appointments.cancel', $appointment) }}" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        {{-- Reschedule modal --}}
                        <div class="modal fade" id="reschedule-{{ $appointment->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('student.appointments.reschedule', $appointment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Reschedule {{ $appointment->type }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label fw-semibold">New Date &amp; Time</label>
                                            <input type="datetime-local" name="scheduled_at" class="form-control" required>
                                            <div class="form-text">This will need to be re-confirmed by our team.</div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Request New Time</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                No appointments yet. <a href="{{ route('student.appointments.create') }}">Book one →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $appointments->links() }}</div>

</x-student-layout>
