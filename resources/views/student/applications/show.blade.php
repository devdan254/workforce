<x-student-layout title="Application Details">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('student.applications.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to My Applications</a>

    {{-- Header --}}
    <div class="card stat-card p-4 mb-4">
        <div class="row">
            <div class="col-md-8">
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $application->university->name }} — {{ $application->course->name }}
                </h2>
                <p class="text-secondary mb-3">{{ $application->university->country }}</p>
                <span class="badge bg-primary-subtle text-primary-emphasis fs-6">{{ $application->currentStatus->label }}</span>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="small text-secondary">Reference</div>
                <div class="fw-semibold mb-2">{{ $application->reference_number }}</div>
                <div class="small text-secondary">Assigned Officer</div>
                <div class="fw-semibold">{{ $application->assignedOfficer?->name ?? 'Not yet assigned' }}</div>
            </div>
        </div>
        <hr>
        <div class="row text-center">
            <div class="col-6 col-md-3">
                <div class="stat-label">Intake</div>
                <div class="fw-semibold">{{ $application->intake ?? '—' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-label">Study Level</div>
                <div class="fw-semibold text-capitalize">{{ $application->course->study_level }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-label">Application Date</div>
                <div class="fw-semibold">{{ $application->submitted_at?->format('d M Y') ?? '—' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-label">Application Deadline</div>
                <div class="fw-semibold">{{ $application->application_deadline?->format('d M Y') ?? '—' }}</div>
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
            </div>

            {{-- Documents --}}
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Documents</h3>
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
                                <a href="{{ route('student.documents.download', $document) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                            @endif
                            @if(in_array($document->status, ['required', 'rejected']))
                                <form method="POST" action="{{ route('student.documents.upload', $document) }}" enctype="multipart/form-data" class="d-flex gap-2">
                                    @csrf
                                    <input type="file" name="file" class="form-control form-control-sm" style="max-width: 200px;" required>
                                    <button type="submit" class="btn btn-sm btn-primary">{{ $document->status === 'rejected' ? 'Replace' : 'Upload' }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No documents required yet for this application.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-5">
            {{-- Financial Information — this application ONLY, never mixed with others --}}
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Financial Information</h3>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Application Fee</span>
                    <span class="fw-semibold">{{ $application->currency }} {{ number_format($financials['application_fee'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Tuition</span>
                    <span class="fw-semibold">{{ $application->currency }} {{ number_format($financials['tuition_fee'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Altura Service Fee</span>
                    <span class="fw-semibold">{{ $application->currency }} {{ number_format($financials['service_fee'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Amount Paid</span>
                    <span class="fw-semibold text-success">{{ $application->currency }} {{ number_format($financials['amount_paid'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="fw-semibold">Balance</span>
                    <span class="fw-semibold {{ $financials['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $application->currency }} {{ number_format($financials['balance'], 2) }}
                    </span>
                </div>
            </div>

            {{-- Admission --}}
            @if($application->admission)
                <div class="card stat-card p-4 mb-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Admission</h3>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Decision</span>
                        <span class="fw-semibold text-capitalize">{{ $application->admission->decision }}</span>
                    </div>
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

</x-student-layout>
