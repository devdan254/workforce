<x-employer-layout title="Job Offer Details">

    <a href="{{ route('employer.offers.index') }}" class="text-decoration-none small mb-3 d-inline-block">← Back to Job Offers</a>

    <div class="card stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
                    {{ $offer->jobApplication->jobSeeker->name }}
                </h2>
                <p class="text-secondary mb-0">{{ $offer->jobApplication->jobPosting->title }} · {{ $offer->jobApplication->jobPosting->country }}</p>
            </div>
            <span class="badge fs-6 {{ $offer->status === 'accepted' ? 'bg-success-subtle text-success-emphasis' : ($offer->status === 'declined' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis') }}">
                {{ ucfirst($offer->status) }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card stat-card p-4 mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Offer Details</h3>
                <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Salary</dt><dd class="col-7">{{ $offer->currency }} {{ number_format($offer->salary, 0) }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Start Date</dt><dd class="col-7">{{ $offer->start_date?->format('d F Y') ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Contract Duration</dt><dd class="col-7">{{ $offer->contract_duration ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Working Hours</dt><dd class="col-7">{{ $offer->working_hours ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Location</dt><dd class="col-7">{{ $offer->location ?? '—' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Accommodation</dt><dd class="col-7">{{ $offer->accommodation_provided ? 'Provided' : 'Not provided' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Meals</dt><dd class="col-7">{{ $offer->meals_provided ? 'Provided' : 'Not provided' }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Air Ticket</dt><dd class="col-7">{{ $offer->air_ticket_provided ? 'Provided' : 'Not provided' }}</dd>
                </dl>
                @if($offer->benefits)
                    <hr>
                    <strong class="small">Benefits</strong>
                    <p class="small mb-0 mt-1">{{ $offer->benefits }}</p>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Candidate</h3>
                <dl class="row small mb-3">
                    <dt class="col-5 text-secondary fw-normal">Name</dt><dd class="col-7">{{ $offer->jobApplication->jobSeeker->name }}</dd>
                    <dt class="col-5 text-secondary fw-normal">Reference</dt><dd class="col-7">{{ $offer->jobApplication->reference_number }}</dd>
                </dl>
                <a href="{{ route('employer.candidates.show', $offer->jobApplication) }}" class="btn btn-sm btn-outline-primary w-100 mb-2">View Candidate Profile</a>
                @if($offer->offerLetterDocument)
                    <div class="alert alert-secondary small mb-0">
                        <i class="fa-solid fa-lock"></i> Offer letter access is currently restricted. Contact Altura for access.
                    </div>
                @else
                    <p class="text-secondary small mb-0 mt-2">No offer letter uploaded yet.</p>
                @endif
            </div>
        </div>
    </div>

</x-employer-layout>
