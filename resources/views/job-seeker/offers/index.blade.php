<x-job-seeker-layout title="Job Offers">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Job Offers</h2>

    <div class="card stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Job</th>
                        <th>Country</th>
                        <th>Salary</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr>
                            <td class="fw-semibold small">{{ $offer->jobApplication->jobPosting->title }}</td>
                            <td class="small">{{ $offer->jobApplication->jobPosting->country }}</td>
                            <td class="small">{{ $offer->currency }} {{ number_format($offer->salary, 0) }}</td>
                            <td class="small">{{ $offer->start_date?->format('d M Y') ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $offer->status === 'accepted' ? 'bg-success-subtle text-success-emphasis' : ($offer->status === 'declined' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis') }}">
                                    {{ ucfirst($offer->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('job-seeker.offers.show', $offer) }}" class="btn btn-sm btn-outline-primary">View Offer</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No job offers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $offers->links() }}</div>

</x-job-seeker-layout>
