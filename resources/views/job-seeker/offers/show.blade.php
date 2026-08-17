<x-job-seeker-layout title="Job Offer">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('job-seeker.offers.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Job Offers</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $offer->jobApplication->jobPosting->title }}
                </h2>
                <p class="text-secondary mb-2">{{ $offer->jobApplication->jobPosting->country }}@if($offer->location) · {{ $offer->location }}@endif</p>
                <span class="badge fs-6 {{ $offer->status === 'accepted' ? 'bg-success-subtle text-success-emphasis' : ($offer->status === 'declined' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis') }}">
                    {{ ucfirst($offer->status) }}
                </span>
            </div>
            <div class="text-end">
                <div class="fs-3 fw-semibold text-primary">{{ $offer->currency }} {{ number_format($offer->salary, 0) }}</div>
                <div class="text-secondary small">per month</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Offer Details</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Contract Duration</dt><dd class="col-7">{{ $offer->contract_duration ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Start Date</dt><dd class="col-7">{{ $offer->start_date?->format('d F Y') ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Working Hours</dt><dd class="col-7">{{ $offer->working_hours ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Accommodation</dt><dd class="col-7">{{ $offer->accommodation_provided ? 'Provided' : 'Not provided' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Meals</dt><dd class="col-7">{{ $offer->meals_provided ? 'Provided' : 'Not provided' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Air Ticket</dt><dd class="col-7">{{ $offer->air_ticket_provided ? 'Provided' : 'Not provided' }}</dd>
                </dl>
                @if($offer->benefits)
                    <hr>
                    <h4 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Benefits</h4>
                    <p class="small mb-0">{{ $offer->benefits }}</p>
                @endif
            </div>

            @if($offer->status === 'pending')
                <div class="card stat-card p-4">
                    <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Your Decision</h3>
                    <p class="text-secondary small">Review the offer carefully. Accepting will move your application forward to documentation and visa processing.</p>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('job-seeker.offers.accept', $offer) }}" onsubmit="return confirm('Accept this job offer? This will move your application to the next stage.');">
                            @csrf
                            <button type="submit" class="btn btn-success">Accept Offer</button>
                        </form>
                        <form method="POST" action="{{ route('job-seeker.offers.decline', $offer) }}" onsubmit="return confirm('Decline this job offer? This cannot be undone.');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">Decline Offer</button>
                        </form>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#clarificationModal">Request Clarification</button>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Application</h3>
                <dl class="row small mb-3">
                    <dt class="col-5 text-secondary fw-normal">Reference</dt><dd class="col-7">{{ $offer->jobApplication->reference_number }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Category</dt><dd class="col-7">{{ $offer->jobApplication->jobPosting->category->name }}</dd>
                </dl>
                <a href="{{ route('job-seeker.applications.show', $offer->jobApplication) }}" class="btn btn-sm btn-outline-primary w-100 mb-2">View Full Application</a>
                @if($offer->offerLetterDocument)
                    <a href="{{ route('job-seeker.offers.download_letter', $offer) }}" class="btn btn-sm btn-outline-secondary w-100">Download Offer Letter</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Request Clarification modal --}}
    <div class="modal fade" id="clarificationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('job-seeker.offers.clarify', $offer) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">Request Clarification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold small">Your Question</label>
                        <textarea name="message" class="form-control" rows="4" required placeholder="e.g. Is the accommodation shared or private?"></textarea>
                        <div class="form-text">This creates a support ticket — our team will respond as soon as possible.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-job-seeker-layout>
