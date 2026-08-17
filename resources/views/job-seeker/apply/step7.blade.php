<x-job-seeker-layout title="Apply — Declaration">

    <div style="max-width: 720px;" class="mx-auto">
        <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 6]) }}" class="text-decoration-none small mb-3 d-inline-block">← Back</a>

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
            <h2 class="h5 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Review &amp; Declaration</h2>

            <div class="mb-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Application Summary</h3>
                <div class="row small g-2">
                    <div class="col-6 text-secondary">Applying for</div><div class="col-6 fw-semibold">{{ $jobPosting->title }} — {{ $jobPosting->country }}</div>
                    <div class="col-6 text-secondary">Name</div><div class="col-6">{{ $data['personal']['full_name'] ?? '—' }}</div>
                    <div class="col-6 text-secondary">Email</div><div class="col-6">{{ $data['personal']['email'] ?? '—' }}</div>
                    <div class="col-6 text-secondary">Phone</div><div class="col-6">{{ $data['personal']['phone'] ?? '—' }}</div>
                    <div class="col-6 text-secondary">Documents Uploaded</div><div class="col-6">{{ count($data['documents'] ?? []) }} of 5</div>
                </div>
                <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 1]) }}" class="small d-inline-block mt-2">Edit any section →</a>
            </div>

            <form method="POST" action="{{ route('job-seeker.jobs.apply.submit', $jobPosting) }}">
                @csrf
                <div class="form-check mb-4">
                    <input type="checkbox" name="declaration" value="1" class="form-check-input" id="declaration" required>
                    <label class="form-check-label small" for="declaration">
                        I agree with the terms and conditions for Altura Workforce Solutions, and confirm that the information provided in this application is accurate to the best of my knowledge.
                    </label>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 6]) }}" class="btn btn-light">← Back</a>
                    <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                </div>
            </form>
        </div>
    </div>

</x-job-seeker-layout>
