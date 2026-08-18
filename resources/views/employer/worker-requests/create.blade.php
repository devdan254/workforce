<x-employer-layout title="Request Workers">

    <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Request Workers</h2>
    <p class="text-secondary mb-4">Tell Altura what you need — our team will review this and create the official job posting.</p>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('employer.worker-requests.store') }}">
        @csrf

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;">Company Information</h3>
            <p class="text-secondary small mb-3">Pulled from your Company Profile. <a href="{{ route('employer.profile.edit') }}">Update it here</a> if anything's changed.</p>
            <div class="row g-2 small">
                <div class="col-md-4"><strong>Company:</strong> {{ auth()->user()->employerProfile?->company_name ?? '—' }}</div>
                <div class="col-md-4"><strong>Industry:</strong> {{ auth()->user()->employerProfile?->industry ?? '—' }}</div>
                <div class="col-md-4"><strong>Country:</strong> {{ auth()->user()->employerProfile?->country ?? '—' }}</div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Contact Person for This Request</h3>
            <p class="text-secondary small mb-3">Leave blank to use your account's own name, email, and phone.</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="contact_name" class="form-control" placeholder="{{ auth()->user()->name }}" value="{{ old('contact_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Job Title</label>
                    <input type="text" name="contact_job_title" class="form-control" value="{{ old('contact_job_title') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="contact_email" class="form-control" placeholder="{{ auth()->user()->email }}" value="{{ old('contact_email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="tel" name="contact_phone" class="form-control" placeholder="{{ auth()->user()->phone }}" value="{{ old('contact_phone') }}">
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Hiring Requirements</h3>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Job Title(s) *</label>
                    <input type="text" name="job_title" class="form-control" placeholder="e.g. Registered Nurse" value="{{ old('job_title') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Number Required *</label>
                    <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity', 1) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Experience Required</label>
                    <input type="text" name="preferred_experience" class="form-control" placeholder="e.g. 2+ years" value="{{ old('preferred_experience') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employment Type *</label>
                    <select name="employment_type" class="form-select" required>
                        @foreach(['permanent' => 'Permanent', 'contract' => 'Contract', 'temporary' => 'Temporary', 'seasonal' => 'Seasonal', 'part_time' => 'Part Time', 'full_time' => 'Full Time'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('employment_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Age Range</label>
                    <input type="text" name="age_range" class="form-control" placeholder="e.g. 21-40 (if applicable)" value="{{ old('age_range') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Preferred Start Date</label>
                    <input type="date" name="preferred_start_date" class="form-control" value="{{ old('preferred_start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Work Location</label>
                    <input type="text" name="work_location" class="form-control" placeholder="e.g. Berlin, Germany" value="{{ old('work_location') }}">
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Compensation</h3>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Salary Min</label>
                    <input type="number" step="0.01" name="salary_min" class="form-control" value="{{ old('salary_min') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Salary Max</label>
                    <input type="number" step="0.01" name="salary_max" class="form-control" value="{{ old('salary_max') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Currency</label>
                    <input type="text" name="currency" class="form-control" maxlength="3" placeholder="USD" value="{{ old('currency') }}">
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Role Details</h3>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Responsibilities</label>
                    <textarea name="responsibilities" class="form-control" rows="3" placeholder="What will this role actually do day-to-day?">{{ old('responsibilities') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Skills</label>
                    <textarea name="skills" class="form-control" rows="2" placeholder="e.g. Patient care, IV administration, CPR certified">{{ old('skills') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Benefits</label>
                    <textarea name="benefits" class="form-control" rows="2" placeholder="e.g. Accommodation, meals, transport, visa sponsorship, air ticket">{{ old('benefits') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Requirements</label>
                    <textarea name="requirements" class="form-control" rows="3" placeholder="Education, certifications, languages, and any other hard requirements">{{ old('requirements') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Additional Information</label>
                    <textarea name="additional_requirements" class="form-control" rows="2" placeholder="Anything else Altura should know">{{ old('additional_requirements') }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit Request</button>
        <a href="{{ route('employer.worker-requests.index') }}" class="btn btn-light">Cancel</a>
    </form>

</x-employer-layout>
