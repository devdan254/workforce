<x-job-seeker-layout title="Dashboard">

    {{-- Welcome --}}
    <div class="mb-4">
        <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
            Welcome back, {{ explode(' ', $jobSeeker->name)[0] }}!
        </h2>
        <p class="text-secondary mb-0">
            You have {{ $applicationsCount }} active job application{{ $applicationsCount === 1 ? '' : 's' }}
            and {{ $upcomingInterviews->count() }} upcoming interview{{ $upcomingInterviews->count() === 1 ? '' : 's' }}.
        </p>
    </div>

    {{-- Top Statistics --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Applications</div>
                <div class="stat-value">{{ $applicationsCount }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Interviews</div>
                <div class="stat-value">{{ $upcomingInterviews->count() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Job Offers</div>
                <div class="stat-value">{{ $offersCount }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Visa Status</div>
                <div class="stat-value" style="font-size:1.1rem;">
                    {{ $primaryApplication?->visaApplication?->currentStatus?->label ?? 'Not Started' }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Documents</div>
                <div class="stat-value">{{ $documentStats['uploaded'] }}/{{ $documentStats['uploaded'] + $documentStats['pending'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Profile</div>
                <div class="stat-value">{{ $jobSeeker->jobSeekerProfile?->profile_completion_percent ?? 0 }}% Complete</div>
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
        @if($financialSummary['total'] == 0)
            <p class="text-secondary small text-center mb-0 mt-2">No fees have been applied to your account yet.</p>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            {{-- My Active Applications --}}
            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">My Active Applications</h3>
                    <a href="{{ route('job-seeker.applications.index') }}" class="small">View All →</a>
                </div>

                @if($recentApplications->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Job</th>
                                    <th>Country</th>
                                    <th>Applied</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentApplications as $application)
                                    <tr>
                                        <td class="fw-semibold small">{{ $application->jobPosting->title }}</td>
                                        <td class="small">{{ $application->jobPosting->country }}</td>
                                        <td class="small">{{ $application->applied_at?->format('d M') ?? '—' }}</td>
                                        <td class="small">
                                            @if($application->jobPosting->salary_min)
                                                {{ $application->jobPosting->currency }} {{ number_format($application->jobPosting->salary_min, 0) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td><span class="badge bg-primary-subtle text-primary-emphasis">{{ $application->currentStatus->label }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('job-seeker.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-secondary mb-3">You haven't applied to any jobs yet.</p>
                    <a href="{{ route('job-seeker.jobs.index') }}" class="btn btn-primary">Find Jobs →</a>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            {{-- Recent Notifications --}}
            <div class="card stat-card p-4">
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
                <a href="{{ route('job-seeker.notifications.index') }}" class="small">View All Notifications →</a>
            </div>
        </div>
    </div>

</x-job-seeker-layout>