<x-employer-layout title="Appointments">

    <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Appointments</h2>
    <p class="text-secondary mb-4">Recruitment consultations, interviews, visa briefings, and onboarding sessions with Altura.</p>

    <div class="card stat-card mb-4">
        <div class="p-3 border-bottom bg-light">
            <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Upcoming</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Candidate</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcoming as $appointment)
                        <tr>
                            <td class="fw-semibold small">{{ $appointment->type }}</td>
                            <td class="small">{{ $appointment->jobApplication?->jobSeeker->name ?? '—' }}</td>
                            <td class="small">{{ $appointment->scheduled_at->format('d M Y') }}</td>
                            <td class="small">{{ $appointment->scheduled_at->format('g:ia') }}</td>
                            <td class="small text-capitalize">{{ $appointment->mode }}</td>
                            <td>
                                <span class="badge {{ $appointment->status === 'confirmed' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($appointment->status === 'confirmed' && $appointment->meeting_link)
                                        <a href="{{ $appointment->meeting_link }}" target="_blank" class="btn btn-sm btn-success">Join</a>
                                    @endif
                                    @if($appointment->job_application_id)
                                        <a href="{{ route('employer.candidates.show', $appointment->job_application_id) }}" class="btn btn-sm btn-outline-primary">View Candidate</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">No upcoming appointments.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card stat-card">
        <div class="p-3 border-bottom bg-light">
            <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Past</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Candidate</th>
                        <th>Date</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($past as $appointment)
                        <tr>
                            <td class="fw-semibold small">{{ $appointment->type }}</td>
                            <td class="small">{{ $appointment->jobApplication?->jobSeeker->name ?? '—' }}</td>
                            <td class="small">{{ $appointment->scheduled_at->format('d M Y') }}</td>
                            <td class="small text-capitalize">{{ $appointment->mode }}</td>
                            <td>
                                <span class="badge {{ $appointment->status === 'completed' ? 'bg-secondary-subtle text-secondary-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td class="text-end">
                                @if($appointment->job_application_id)
                                    <a href="{{ route('employer.candidates.show', $appointment->job_application_id) }}" class="btn btn-sm btn-outline-primary">View Candidate</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No past appointments.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-employer-layout>
