<x-employer-layout title="Interviews">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Interviews</h2>

    <div class="card stat-card mb-4">
        <div class="p-3 border-bottom bg-light">
            <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Upcoming Interviews</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Candidate</th>
                        <th>Job</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcoming as $appointment)
                        <tr>
                            <td class="fw-semibold small">{{ $appointment->jobApplication->jobSeeker->name }}</td>
                            <td class="small">{{ $appointment->jobApplication->jobPosting->title }}</td>
                            <td class="small">{{ $appointment->scheduled_at->format('d M Y') }}</td>
                            <td class="small">{{ $appointment->scheduled_at->format('g:ia') }}</td>
                            <td class="small text-capitalize">{{ $appointment->mode }}</td>
                            <td>
                                <span class="badge {{ $appointment->status === 'confirmed' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($appointment->status === 'confirmed' && $appointment->meeting_link)
                                        @can('employer_interviews.conduct')
                                            <a href="{{ $appointment->meeting_link }}" target="_blank" class="btn btn-sm btn-success">Join</a>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis align-self-center" title="Altura will conduct this interview">Altura Conducting</span>
                                        @endcan
                                    @endif
                                    <a href="{{ route('employer.candidates.show', $appointment->job_application_id) }}" class="btn btn-sm btn-outline-primary">View Candidate</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">No upcoming interviews.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card stat-card">
        <div class="p-3 border-bottom bg-light">
            <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Past Interviews</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Candidate</th>
                        <th>Job</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($past as $appointment)
                        <tr>
                            <td class="fw-semibold small">{{ $appointment->jobApplication->jobSeeker->name }}</td>
                            <td class="small">{{ $appointment->jobApplication->jobPosting->title }}</td>
                            <td class="small">{{ $appointment->scheduled_at->format('d M Y') }}</td>
                            <td class="small text-capitalize">{{ $appointment->mode }}</td>
                            <td>
                                <span class="badge {{ $appointment->status === 'completed' ? 'bg-secondary-subtle text-secondary-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employer.candidates.show', $appointment->job_application_id) }}" class="btn btn-sm btn-outline-primary">View Candidate</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No past interviews.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-employer-layout>
