<x-job-seeker-layout title="Apply — Work Experience">

    <div style="max-width: 720px;" class="mx-auto">
        <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 3]) }}" class="text-decoration-none small mb-3 d-inline-block">← Back</a>

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
            <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Work Experience</h2>
            <p class="text-secondary small mb-4">Optional — add as many roles as relevant, or skip if this is your first job.</p>

            <form method="POST" action="{{ route('job-seeker.jobs.apply.save', [$jobPosting, 4]) }}">
                @csrf

                <div id="experienceRows">
                    @php $existing = $data['experiences'] ?? [['occupation' => '', 'employer' => '', 'years_of_experience' => '']]; @endphp
                    @foreach($existing as $i => $exp)
                        <div class="row g-2 mb-3 experience-row">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Current Occupation</label>
                                <input type="text" name="occupation[]" class="form-control form-control-sm" value="{{ $exp['occupation'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Employer</label>
                                <input type="text" name="employer[]" class="form-control form-control-sm" value="{{ $exp['employer'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Years of Experience</label>
                                <input type="number" step="0.5" name="years_of_experience[]" class="form-control form-control-sm" value="{{ $exp['years_of_experience'] ?? '' }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="addExperienceBtn" class="btn btn-sm btn-outline-secondary mb-4">+ Add Another Experience</button>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 3]) }}" class="btn btn-light">← Back</a>
                    <button type="submit" class="btn btn-primary">Next: Documents →</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('addExperienceBtn').addEventListener('click', function () {
            const container = document.getElementById('experienceRows');
            const row = document.createElement('div');
            row.className = 'row g-2 mb-3 experience-row';
            row.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Current Occupation</label>
                    <input type="text" name="occupation[]" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Employer</label>
                    <input type="text" name="employer[]" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Years of Experience</label>
                    <input type="number" step="0.5" name="years_of_experience[]" class="form-control form-control-sm">
                </div>
            `;
            container.appendChild(row);
        });
    </script>

</x-job-seeker-layout>
