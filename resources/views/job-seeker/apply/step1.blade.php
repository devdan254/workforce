<x-job-seeker-layout title="Apply — Personal Information">

    <div style="max-width: 720px;" class="mx-auto">
        <a href="{{ route('job-seeker.jobs.show', $jobPosting) }}" class="text-decoration-none small mb-3 d-inline-block">← Back to {{ $jobPosting->title }}</a>

        @include('job-seeker.apply._stepper')

        @if(session('info'))
            <div class="alert alert-info small">{{ session('info') }}</div>
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

        {{-- Optional CV upload to auto-fill this step --}}
        <div class="card stat-card p-3 mb-4 border-primary-subtle">
            <form method="POST" action="{{ route('job-seeker.jobs.apply.parse_cv', $jobPosting) }}" enctype="multipart/form-data" class="d-flex gap-2 align-items-center flex-wrap">
                @csrf
                <i class="fa-solid fa-wand-magic-sparkles text-primary"></i>
                <span class="small text-secondary flex-grow-1">
                    @if(!empty($data['documents']['cv_document_id']))
                        <strong class="text-success">CV uploaded ✓</strong> — upload again to replace it.
                    @else
                        Upload your CV and we'll try to auto-fill this form for you (optional — you can always fill it in manually).
                    @endif
                </span>
                <input type="file" name="cv" class="form-control form-control-sm" style="max-width: 220px;" accept=".pdf,.doc,.docx" required>
                <button type="submit" class="btn btn-sm btn-outline-primary">Upload &amp; Auto-Fill</button>
            </form>
        </div>

        <div class="card stat-card p-4">
            <h2 class="h5 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Personal Information</h2>

            <form method="POST" action="{{ route('job-seeker.jobs.apply.save', [$jobPosting, 1]) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $data['personal']['full_name'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">National ID / Passport *</label>
                        <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $data['personal']['national_id'] ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $data['personal']['date_of_birth'] ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Gender *</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Male" @selected(old('gender', $data['personal']['gender'] ?? '') === 'Male')>Male</option>
                            <option value="Female" @selected(old('gender', $data['personal']['gender'] ?? '') === 'Female')>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Nationality *</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $data['personal']['nationality'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Phone Number *</label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone', $data['personal']['phone'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Email Address *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $data['personal']['email'] ?? '') }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">Next: Address →</button>
                </div>
            </form>
        </div>
    </div>

</x-job-seeker-layout>
