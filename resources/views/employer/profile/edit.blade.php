<x-employer-layout title="Company Profile">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">Company Profile</h2>
        <div class="text-end">
            <div class="small text-secondary">Profile Completion</div>
            <div class="fw-semibold">{{ $profile->profile_completion_percent ?? 0 }}%</div>
        </div>
    </div>

    <form method="POST" action="{{ route('employer.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Company Information</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Company Name *</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $profile?->company_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Industry</label>
                    <input type="text" name="industry" class="form-control" value="{{ old('industry', $profile?->industry) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country *</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $profile?->country) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $profile?->city) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Company Website</label>
                    <input type="url" name="company_website" class="form-control" placeholder="https://..." value="{{ old('company_website', $profile?->company_website) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Company Size</label>
                    <select name="company_size" class="form-select">
                        <option value="">Select</option>
                        @foreach(['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'] as $size)
                            <option value="{{ $size }}" @selected(old('company_size', $profile?->company_size) === $size)>{{ $size }} employees</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Company Description</label>
                    <textarea name="company_description" class="form-control" rows="3">{{ old('company_description', $profile?->company_description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Contact Person</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" class="form-control" value="{{ $employer->name }}" disabled>
                    <div class="form-text">Change your name from <a href="{{ route('profile.edit') }}">Account Settings</a>.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Job Title</label>
                    <input type="text" name="contact_job_title" class="form-control" placeholder="e.g. HR Manager" value="{{ old('contact_job_title', $profile?->contact_job_title) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" class="form-control" value="{{ $employer->email }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $employer->phone) }}">
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Company Logo</h3>
            @if($profile?->logo_path)
                <img src="{{ $profile->logo_url }}" alt="Logo" class="mb-3" style="max-height:100px;border-radius:8px;">
            @endif
            <input type="file" name="logo" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Save Company Profile</button>
    </form>

</x-employer-layout>
