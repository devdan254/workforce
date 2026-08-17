<x-job-seeker-layout title="Apply — Education">

    <div style="max-width: 720px;" class="mx-auto">
        <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 2]) }}" class="text-decoration-none small mb-3 d-inline-block">← Back</a>

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
            <h2 class="h5 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Education</h2>

            <form method="POST" action="{{ route('job-seeker.jobs.apply.save', [$jobPosting, 3]) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Highest Education Level *</label>
                        <select name="level" class="form-select" required>
                            <option value="">Select level</option>
                            @foreach(['primary' => 'Primary', 'secondary' => 'Secondary', 'diploma' => 'Diploma', 'bachelor' => "Bachelor's Degree", 'master' => "Master's Degree", 'not_applicable' => 'Not Applicable'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('level', $data['education']['level'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Graduation Year</label>
                        <input type="number" name="graduation_year" class="form-control" value="{{ old('graduation_year', $data['education']['graduation_year'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Institution</label>
                        <input type="text" name="institution" class="form-control" value="{{ old('institution', $data['education']['institution'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Course</label>
                        <input type="text" name="course" class="form-control" value="{{ old('course', $data['education']['course'] ?? '') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 2]) }}" class="btn btn-light">← Back</a>
                    <button type="submit" class="btn btn-primary">Next: Work Experience →</button>
                </div>
            </form>
        </div>
    </div>

</x-job-seeker-layout>
