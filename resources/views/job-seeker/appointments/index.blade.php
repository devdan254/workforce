<x-job-seeker-layout title="Interviews & Appointments">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Interviews &amp; Appointments</h2>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Job</th>
                        <th>Type</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td class="small">{{ $appointment->scheduled_at->format('d M Y, g:ia') }}</td>
                            <td class="small fw-semibold">{{ $appointment->jobApplication?->jobPosting->title ?? '—' }}</td>
                            <td class="small">{{ $appointment->type }}</td>
                            <td class="small text-capitalize">{{ $appointment->mode }}</td>
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
                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                    @if($appointment->status === 'confirmed' && $appointment->meeting_link && $appointment->scheduled_at->isFuture())
                                        <a href="{{ $appointment->meeting_link }}" target="_blank" class="btn btn-sm btn-success">Join</a>
                                    @endif
                                    @if($appointment->jobApplication)
                                        <a href="{{ route('job-seeker.applications.show', $appointment->jobApplication) }}" class="btn btn-sm btn-outline-secondary">View Details</a>
                                    @endif
                                    @if(in_array($appointment->status, ['requested', 'confirmed']) && $appointment->scheduled_at->isFuture())
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reschedule-{{ $appointment->id }}">Reschedule</button>
                                        <form method="POST" action="{{ route('job-seeker.appointments.cancel', $appointment) }}" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        @if(in_array($appointment->status, ['requested', 'confirmed']) && $appointment->scheduled_at->isFuture())
                            <div class="modal fade" id="reschedule-{{ $appointment->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('job-seeker.appointments.reschedule', $appointment) }}">
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
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No interviews or appointments scheduled yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $appointments->links() }}</div>

</x-job-seeker-layout>
