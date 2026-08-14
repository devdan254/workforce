<x-student-layout title="My Profile">

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
        <h2 class="h4 fw-semibold mb-0" style="font-family:'Poppins',sans-serif;color:#082159;">My Profile</h2>
        @if($profile)
            <div class="text-end">
                <div class="small text-secondary">Profile Completion</div>
                <div class="fw-semibold">{{ $profile->profile_completion_percent }}%</div>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('student.profile.update') }}">
        @csrf
        @method('PATCH')

        {{-- Personal Information --}}
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Personal Information</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">Select</option>
                        <option value="Male" @selected(old('gender', $profile?->gender) === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender', $profile?->gender) === 'Female')>Female</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nationality</label>
                    <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $profile?->nationality) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" name="address_line" class="form-control" value="{{ old('address_line', $profile?->address_line) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $profile?->city) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $profile?->country) }}">
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('profile.edit') }}" class="small">Change your password or delete your account →</a>
            </div>
        </div>

        {{-- Academic Information --}}
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Academic Information</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Highest Qualification</label>
                    <select name="highest_qualification" class="form-select">
                        <option value="">Select level</option>
                        @foreach(['High School', 'Diploma', "Bachelor's Degree", "Master's Degree"] as $level)
                            <option value="{{ $level }}" @selected(old('highest_qualification', $profile?->highest_qualification) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Institution</label>
                    <input type="text" name="institution" class="form-control" value="{{ old('institution', $profile?->institution) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Graduation Year</label>
                    <input type="number" name="graduation_year" class="form-control" value="{{ old('graduation_year', $profile?->graduation_year) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Field of Study</label>
                    <input type="text" name="field_of_study" class="form-control" value="{{ old('field_of_study', $profile?->field_of_study) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Grade</label>
                    <input type="text" name="grade" class="form-control" value="{{ old('grade', $profile?->grade) }}">
                </div>
            </div>
        </div>

        {{-- Passport Information --}}
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Passport Information</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Passport Number</label>
                    <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number', $profile?->passport_number) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country of Issue</label>
                    <input type="text" name="passport_country" class="form-control" value="{{ old('passport_country', $profile?->passport_country) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Issue Date</label>
                    <input type="date" name="passport_issue_date" class="form-control" value="{{ old('passport_issue_date', $profile?->passport_issue_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Expiry Date</label>
                    <input type="date" name="passport_expiry_date" class="form-control" value="{{ old('passport_expiry_date', $profile?->passport_expiry_date?->format('Y-m-d')) }}">
                </div>
            </div>
        </div>

        {{-- Preferences --}}
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Preferences</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Countries</label>
                    <input type="text" name="preferred_countries_text" class="form-control"
                           value="{{ old('preferred_countries_text', implode(', ', $profile?->preferred_countries ?? [])) }}"
                           placeholder="e.g. Germany, Australia" onchange="document.getElementById('preferred_countries_hidden').value = this.value">
                    <div class="form-text">Comma-separated.</div>
                    {{-- Converted to an array server-side isn't possible from a plain text input directly,
                         so we split it into individual hidden inputs on submit via a tiny inline script. --}}
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Course</label>
                    <input type="text" name="preferred_course" class="form-control" value="{{ old('preferred_course', $profile?->preferred_course) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Study Level</label>
                    <input type="text" name="preferred_study_level" class="form-control" value="{{ old('preferred_study_level', $profile?->preferred_study_level) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Intake</label>
                    <input type="text" name="preferred_intake" class="form-control" value="{{ old('preferred_intake', $profile?->preferred_intake) }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>

    <script>
        // Turn the comma-separated "Preferred Countries" text into preferred_countries[]
        // hidden inputs right before submit, since a single text field can't post an array.
        document.querySelector('form').addEventListener('submit', function () {
            const raw = this.querySelector('[name="preferred_countries_text"]').value;
            const values = raw.split(',').map(v => v.trim()).filter(Boolean);
            values.forEach(v => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'preferred_countries[]';
                input.value = v;
                this.appendChild(input);
            });
        });
    </script>

</x-student-layout>
