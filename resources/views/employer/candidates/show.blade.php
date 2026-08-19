<x-employer-layout title="Candidate Profile">

    <a href="{{ route('employer.candidates.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Candidates</a>

    @php
        $profile = $application->jobSeeker->jobSeekerProfile;
    @endphp

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">{{ $application->jobSeeker->name }}</h2>
                <p class="text-secondary mb-0">{{ $profile?->professional_title ?? 'Professional title not provided' }}</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis fs-6">{{ $application->currentStatus->label }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Personal Information</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Name</dt><dd class="col-7">{{ $application->jobSeeker->name }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Nationality</dt><dd class="col-7">{{ $profile?->nationality ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Location</dt><dd class="col-7">{{ $profile?->city }}{{ $profile?->city && $profile?->country ? ', ' : '' }}{{ $profile?->country ?? '—' }}</dd>
                </dl>
                <p class="text-secondary small mt-3 mb-0">Contact information is managed by Altura and shared once a candidate is progressed further in the process.</p>
            </div>

            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Professional Information</h3>
                <dl class="row small mb-3">
                    <dt class="col-5 text-secondary fw-normal">Title</dt><dd class="col-7">{{ $profile?->professional_title ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Industry</dt><dd class="col-7">{{ $profile?->industry ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Skills</dt><dd class="col-7">{{ $profile?->skills ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Languages</dt><dd class="col-7">{{ $profile?->languages ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Certifications</dt><dd class="col-7">{{ $profile?->certifications ?? '—' }}</dd>
                </dl>

                @if($profile?->experiences->isNotEmpty())
                    <hr>
                    <strong class="small">Employment History</strong>
                    @foreach($profile->experiences as $experience)
                        <div class="small py-1">
                            {{ $experience->occupation }} — {{ $experience->employer ?? '—' }}
                            @if($experience->years_of_experience) ({{ $experience->years_of_experience }} yrs) @endif
                        </div>
                    @endforeach
                @endif

                @if($profile?->educations->isNotEmpty())
                    <hr>
                    <strong class="small">Education</strong>
                    @foreach($profile->educations as $education)
                        <div class="small py-1">
                            {{ ucfirst(str_replace('_', ' ', $education->level)) }} — {{ $education->institution ?? '—' }}
                            @if($education->graduation_year) ({{ $education->graduation_year }}) @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Documents</h3>
                @if($canViewDocuments)
                    @forelse($documents as $document)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <span class="fw-semibold small">{{ $document->name }}</span>
                                <span class="text-secondary small">· {{ $document->category->name ?? '' }}</span>
                            </div>
                            @if($document->status === 'verified' && $document->file_path)
                                <a href="{{ route('employer.candidate-documents.download', $document) }}" class="btn btn-sm btn-outline-primary">Download</a>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">Not yet verified</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary small mb-0">No documents uploaded yet.</p>
                    @endforelse
                @else
                    <div class="alert alert-secondary small mb-0">
                        <i class="fa-solid fa-lock"></i> Candidate documents are currently restricted. Contact Altura for access.
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Application</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Job Applied For</dt><dd class="col-7">{{ $application->jobPosting->title }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Reference</dt><dd class="col-7">{{ $application->reference_number }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Applied</dt><dd class="col-7">{{ $application->applied_at?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Status</dt><dd class="col-7">{{ $application->currentStatus->label }}</dd>
                </dl>
            </div>

            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Application Timeline</h3>
                <div class="altura-timeline">
                    @foreach($application->statusHistories->sortBy('created_at') as $history)
                        <div class="altura-timeline-step {{ $history->to_status_id === $application->status_id ? 'is-current' : 'is-done' }}">
                            <div class="altura-timeline-dot">
                                <i class="fa-solid {{ $history->to_status_id === $application->status_id ? 'fa-hourglass-half' : 'fa-check' }}"></i>
                            </div>
                            <div class="fw-semibold small">{{ $history->toStatus->label }}</div>
                            <div class="text-secondary" style="font-size:.75rem;">{{ $history->created_at->format('d M Y') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</x-employer-layout>
