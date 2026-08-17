<x-job-seeker-layout title="Apply — Additional Information">

    <div style="max-width: 720px;" class="mx-auto">
        <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 5]) }}" class="text-decoration-none small mb-3 d-inline-block">← Back</a>

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
            <h2 class="h5 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Additional Information</h2>

            <form method="POST" action="{{ route('job-seeker.jobs.apply.save', [$jobPosting, 6]) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small d-block">Have you worked abroad before? *</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="worked_abroad_before" value="1" class="form-check-input" id="workedYes" @checked(old('worked_abroad_before', $data['additional']['worked_abroad_before'] ?? null) == 1) required>
                        <label class="form-check-label" for="workedYes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="worked_abroad_before" value="0" class="form-check-input" id="workedNo" @checked(old('worked_abroad_before', $data['additional']['worked_abroad_before'] ?? null) === '0' || (isset($data['additional']['worked_abroad_before']) && ! $data['additional']['worked_abroad_before'])) required>
                        <label class="form-check-label" for="workedNo">No</label>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Preferred Country</label>
                        <input type="text" name="preferred_country" class="form-control" value="{{ old('preferred_country', $data['additional']['preferred_country'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Preferred Industry</label>
                        <input type="text" name="preferred_industry" class="form-control" value="{{ old('preferred_industry', $data['additional']['preferred_industry'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Expected Salary</label>
                        <input type="number" step="0.01" name="expected_salary" class="form-control" value="{{ old('expected_salary', $data['additional']['expected_salary'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Earliest Availability</label>
                        <input type="date" name="earliest_availability" class="form-control" value="{{ old('earliest_availability', $data['additional']['earliest_availability'] ?? '') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 5]) }}" class="btn btn-light">← Back</a>
                    <button type="submit" class="btn btn-primary">Next: Declaration →</button>
                </div>
            </form>
        </div>
    </div>

</x-job-seeker-layout>
