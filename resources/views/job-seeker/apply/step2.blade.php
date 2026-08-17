<x-job-seeker-layout title="Apply — Address">

    <div style="max-width: 720px;" class="mx-auto">
        <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 1]) }}" class="text-decoration-none small mb-3 d-inline-block">← Back</a>

        @include('job-seeker.apply._stepper')

        @if($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card stat-card p-4">
            <h2 class="h5 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Address</h2>

            <form method="POST" action="{{ route('job-seeker.jobs.apply.save', [$jobPosting, 2]) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Country *</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $data['address']['country'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">County *</label>
                        <input type="text" name="county" class="form-control" value="{{ old('county', $data['address']['county'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $data['address']['city'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Town</label>
                        <input type="text" name="town" class="form-control" value="{{ old('town', $data['address']['town'] ?? '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Address</label>
                        <input type="text" name="address_line" class="form-control" value="{{ old('address_line', $data['address']['address_line'] ?? '') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 1]) }}" class="btn btn-light">← Back</a>
                    <button type="submit" class="btn btn-primary">Next: Education →</button>
                </div>
            </form>
        </div>
    </div>

</x-job-seeker-layout>
