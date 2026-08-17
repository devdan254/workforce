<x-job-seeker-layout title="Application Details">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('job-seeker.applications.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to My Applications</a>

    {{-- Header --}}
    <div class="card stat-card p-4 mb-4">
        <div class="row">
            <div class="col-md-8">
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $application->jobPosting->title }}
                </h2>
                <p class="text-secondary mb-3">{{ $application->jobPosting->country }}</p>
                <span class="badge bg-primary-subtle text-primary-emphasis fs-6">{{ $application->currentStatus->label }}</span>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="small text-secondary">Reference</div>
                <div class="fw-semibold mb-2">{{ $application->reference_number }}</div>
                <div class="small text-secondary">Salary</div>
                <div class="fw-semibold">
                    {{ $application->jobPosting->currency }} {{ number_format($application->jobPosting->salary_min, 0) }}@if($application->jobPosting->salary_max) – {{ number_format($application->jobPosting->salary_max, 0) }}@endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            {{-- Application Timeline --}}
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Application Timeline</h3>
                <div class="altura-timeline">
                    @foreach($application->statusHistories->sortBy('created_at') as $history)
                        <div class="altura-timeline-step {{ $history->to_status_id === $application->status_id ? 'is-current' : 'is-done' }}">
                            <div class="altura-timeline-dot">
                                <i class="fa-solid {{ $history->to_status_id === $application->status_id ? 'fa-hourglass-half' : 'fa-check' }}"></i>
                            </div>
                            <div class="fw-semibold">{{ $history->toStatus->label }}</div>
                            <div class="text-secondary small">{{ $history->created_at->format('d F Y, g:ia') }}</div>
                        </div>
                    @endforeach
                    @foreach($nextStatuses as $next)
                        <div class="altura-timeline-step">
                            <div class="altura-timeline-dot"><i class="fa-solid fa-circle" style="font-size:6px;"></i></div>
                            <div class="text-secondary">{{ $next->label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Application Documents --}}
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Application Documents</h3>
                @forelse($application->documents as $document)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold">{{ $document->name }}</div>
                            <div class="small text-secondary">
                                @switch($document->status)
                                    @case('required')
                                        <span class="text-warning">Required — not yet uploaded</span>
                                        @break
                                    @case('under_review')
                                        <span class="text-primary">Uploaded {{ $document->uploaded_at?->format('d M Y') }} — under review</span>
                                        @break
                                    @case('verified')
                                        <span class="text-success">Verified {{ $document->verified_at?->format('d M Y') }}</span>
                                        @break
                                    @case('rejected')
                                        <span class="text-danger">Rejected: {{ $document->rejection_reason }}</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if(in_array($document->status, ['under_review', 'verified']))
                                <a href="{{ route('job-seeker.documents.download', $document) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                            @endif
                            @if(in_array($document->status, ['required', 'rejected']))
                                <form method="POST" action="{{ route('job-seeker.applications.documents.upload', [$application, $document]) }}" enctype="multipart/form-data" class="d-flex gap-2">
                                    @csrf
                                    <input type="file" name="file" class="form-control form-control-sm" style="max-width: 200px;" required>
                                    <button type="submit" class="btn btn-sm btn-primary">{{ $document->status === 'rejected' ? 'Replace' : 'Upload' }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No documents tied to this application yet.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-5">
            {{-- Job Information --}}
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Job Information</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Category</dt><dd class="col-7">{{ $application->jobPosting->category->name }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Employment Type</dt><dd class="col-7 text-capitalize">{{ $application->jobPosting->employment_type }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Experience</dt><dd class="col-7">{{ $application->jobPosting->experience_required ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Deadline</dt><dd class="col-7">{{ $application->jobPosting->application_deadline?->format('d M Y') ?? '—' }}</dd>
                </dl>
                <a href="{{ route('job-seeker.jobs.show', $application->jobPosting) }}" class="small d-inline-block mt-2">View Full Job Posting →</a>
            </div>

            {{-- Interview --}}
            @if($interview)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Interview</h3>
                    <div class="fw-semibold">{{ $interview->scheduled_at->format('d F Y') }}</div>
                    <div class="text-secondary small mb-2">{{ $interview->scheduled_at->format('g:ia') }} · {{ ucfirst($interview->mode) }}</div>
                    <span class="badge {{ $interview->status === 'confirmed' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">{{ ucfirst($interview->status) }}</span>
                    @if($interview->meeting_link && $interview->status === 'confirmed')
                        <a href="{{ $interview->meeting_link }}" target="_blank" class="btn btn-primary btn-sm w-100 mt-3">Join Interview →</a>
                    @endif
                </div>
            @endif

            {{-- Job Offer summary --}}
            @if($application->offer)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Job Offer</h3>
                    <div class="fw-semibold text-primary fs-5 mb-1">{{ $application->offer->currency }} {{ number_format($application->offer->salary, 0) }}</div>
                    <span class="badge {{ $application->offer->status === 'accepted' ? 'bg-success-subtle text-success-emphasis' : ($application->offer->status === 'declined' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis') }}">{{ ucfirst($application->offer->status) }}</span>
                    <p class="text-secondary small mt-2 mb-0">Full offer details and Accept/Decline actions are on the Job Offers page.</p>
                </div>
            @endif

            {{-- Visa --}}
            @if($application->visaApplication)
                <div class="card stat-card p-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Visa Status</h3>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">{{ $application->visaApplication->destination_country }}</span>
                        <span class="fw-semibold">{{ $application->visaApplication->currentStatus->label }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-job-seeker-layout>
