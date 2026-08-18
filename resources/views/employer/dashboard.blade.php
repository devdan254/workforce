<x-employer-layout title="Dashboard">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
            Welcome back, {{ $employer->employerProfile?->company_name ?? $employer->name }}!
        </h2>
        <p class="text-secondary mb-0">Your recruitment activity is being managed by Altura Workforce Solutions.</p>
    </div>

    @unless($employer->employerProfile)
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <span>Complete your Company Profile so Altura can start processing your worker requests.</span>
            <a href="{{ route('employer.profile.edit') }}" class="btn btn-sm btn-warning">Complete Profile →</a>
        </div>
    @endunless

    {{-- Recruitment stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Active Jobs</div>
                <div class="stat-value">{{ $activeJobs }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Applications</div>
                <div class="stat-value">{{ $totalApplications }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Shortlisted</div>
                <div class="stat-value">{{ $shortlisted }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Interviews</div>
                <div class="stat-value">{{ $interviews }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Job Offers</div>
                <div class="stat-value">{{ $offers }}</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card stat-card p-3 h-100">
                <div class="stat-label">Employees Hired</div>
                <div class="stat-value">{{ $hired }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left column: Action Center + Worker Requests --}}
        <div class="col-lg-7">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Action Required</h3>
                @forelse($actionItems as $item)
                    <a href="{{ $item['route'] }}" class="d-flex align-items-center gap-3 py-2 text-decoration-none {{ !$loop->last ? 'border-bottom' : '' }}">
                        <i class="fa-solid fa-{{ $item['icon'] }} text-warning"></i>
                        <span class="text-dark">{{ $item['text'] }}</span>
                        <i class="fa-solid fa-chevron-right ms-auto text-secondary" style="font-size:.75rem;"></i>
                    </a>
                @empty
                    <p class="text-secondary mb-0">Nothing needs your attention right now.</p>
                @endforelse
            </div>

            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;">Recent Worker Requests</h3>
                    <a href="{{ route('employer.worker-requests.index') }}" class="small">View All →</a>
                </div>
                @forelse($recentWorkerRequests as $workerRequest)
                    @php
                        $badgeClass = match($workerRequest->status) {
                            'approved', 'converted' => 'bg-success-subtle text-success-emphasis',
                            'rejected' => 'bg-danger-subtle text-danger-emphasis',
                            'clarification_required' => 'bg-warning-subtle text-warning-emphasis',
                            default => 'bg-primary-subtle text-primary-emphasis',
                        };
                    @endphp
                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <a href="{{ route('employer.worker-requests.show', $workerRequest) }}" class="fw-semibold text-decoration-none">
                                {{ $workerRequest->quantity }}x {{ $workerRequest->job_title }}
                            </a>
                            <div class="text-secondary small">{{ $workerRequest->created_at->format('d M Y') }}</div>
                        </div>
                        <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $workerRequest->status)) }}</span>
                    </div>
                @empty
                    <p class="text-secondary mb-2">You haven't requested any workers yet.</p>
                    <a href="{{ route('employer.worker-requests.create') }}" class="btn btn-sm btn-primary">+ Request Workers</a>
                @endforelse
            </div>
        </div>

        {{-- Right column: Payments summary --}}
        <div class="col-lg-5">
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Payments</h3>
                <div class="row text-center mb-3">
                    <div class="col-4">
                        <div class="stat-label">Total</div>
                        <div class="fw-semibold">{{ $financials['currency'] }} {{ number_format($financials['total'], 0) }}</div>
                    </div>
                    <div class="col-4">
                        <div class="stat-label">Paid</div>
                        <div class="fw-semibold text-success">{{ $financials['currency'] }} {{ number_format($financials['paid'], 0) }}</div>
                    </div>
                    <div class="col-4">
                        <div class="stat-label">Outstanding</div>
                        <div class="fw-semibold {{ $financials['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $financials['currency'] }} {{ number_format($financials['balance'], 0) }}
                        </div>
                    </div>
                </div>
                @if($financials['total'] == 0)
                    <p class="text-secondary small text-center mb-0">No fees have been applied to your account yet.</p>
                @elseif($financials['balance'] > 0)
                    <a href="{{ route('employer.invoices.index') }}" class="btn btn-primary btn-sm w-100">Pay Outstanding Balance →</a>
                @else
                    <p class="text-success small text-center mb-0"><i class="fa-solid fa-circle-check"></i> All invoices paid up.</p>
                @endif
                <hr>
                <a href="{{ route('employer.invoices.index') }}" class="small d-block">View Invoices →</a>
                <a href="{{ route('employer.payments.index') }}" class="small d-block">View Payment History →</a>
            </div>

            @if($upcomingInterviews > 0)
                <div class="card stat-card p-4 mt-4">
                    <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Interviews</h3>
                    <p class="text-secondary small mb-3">{{ $upcomingInterviews }} interview{{ $upcomingInterviews > 1 ? 's' : '' }} scheduled with your candidates.</p>
                    <a href="{{ route('employer.interviews.index') }}" class="btn btn-sm btn-outline-primary w-100">View Interviews →</a>
                </div>
            @endif
        </div>
    </div>

</x-employer-layout>
