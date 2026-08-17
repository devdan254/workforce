<x-job-seeker-layout title="My Profile">

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
        <div class="text-end">
            <div class="small text-secondary">Profile Completion</div>
            <div class="fw-semibold">{{ $profile->profile_completion_percent ?? 0 }}%</div>
        </div>
    </div>

    <form method="POST" action="{{ route('job-seeker.profile.update') }}">
        @csrf
        @method('PATCH')

        {{-- Personal --}}
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Personal</h3>
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
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">Select</option>
                        <option value="Male" @selected(old('gender', $profile?->gender) === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender', $profile?->gender) === 'Female')>Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nationality</label>
                    <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $profile?->nationality) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $profile?->country) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $profile?->city) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" name="address_line" class="form-control" value="{{ old('address_line', $profile?->address_line) }}">
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('profile.edit') }}" class="small">Change your password or delete your account →</a>
            </div>
        </div>

        {{-- Professional --}}
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Professional</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Professional Title</label>
                    <input type="text" name="professional_title" class="form-control" placeholder="e.g. Registered Nurse" value="{{ old('professional_title', $profile?->professional_title) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Industry</label>
                    <input type="text" name="industry" class="form-control" placeholder="e.g. Healthcare" value="{{ old('industry', $profile?->industry) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Skills</label>
                    <input type="text" name="skills" class="form-control" placeholder="e.g. Patient care, IV administration, CPR certified" value="{{ old('skills', $profile?->skills) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Languages</label>
                    <input type="text" name="languages" class="form-control" placeholder="e.g. English, Swahili" value="{{ old('languages', $profile?->languages) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Certifications</label>
                    <input type="text" name="certifications" class="form-control" placeholder="e.g. BLS, ACLS" value="{{ old('certifications', $profile?->certifications) }}">
                </div>
            </div>
        </div>

        {{-- Passport --}}
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Passport</h3>
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

        {{-- Job Preferences --}}
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;">Job Preferences</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Countries</label>
                    <input type="text" name="preferred_countries_text" class="form-control" placeholder="e.g. Germany, UAE" value="{{ old('preferred_countries_text', implode(', ', $profile?->preferred_countries ?? [])) }}">
                    <div class="form-text">Comma-separated.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Industries</label>
                    <input type="text" name="preferred_industries_text" class="form-control" placeholder="e.g. Healthcare, Hospitality" value="{{ old('preferred_industries_text', implode(', ', $profile?->preferred_industries ?? [])) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Positions</label>
                    <input type="text" name="preferred_positions_text" class="form-control" value="{{ old('preferred_positions_text', implode(', ', $profile?->preferred_positions ?? [])) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Employment Type</label>
                    <select name="preferred_employment_type" class="form-select">
                        <option value="">No preference</option>
                        @foreach(['permanent' => 'Permanent', 'contract' => 'Contract', 'temporary' => 'Temporary', 'seasonal' => 'Seasonal'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('preferred_employment_type', $profile?->preferred_employment_type) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Expected Salary</label>
                    <input type="number" step="0.01" name="expected_salary" class="form-control" value="{{ old('expected_salary', $profile?->expected_salary) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Currency</label>
                    <input type="text" name="expected_salary_currency" class="form-control" maxlength="3" placeholder="USD" value="{{ old('expected_salary_currency', $profile?->expected_salary_currency) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Earliest Availability</label>
                    <input type="date" name="earliest_availability" class="form-control" value="{{ old('earliest_availability', $profile?->earliest_availability?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-4">
                    <div class="form-check">
                        <input type="checkbox" name="willing_to_relocate" value="1" class="form-check-input" id="relocate" @checked(old('willing_to_relocate', $profile?->willing_to_relocate ?? true))>
                        <label class="form-check-label" for="relocate">Willing to relocate</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="worked_abroad_before" value="1" class="form-check-input" id="workedAbroad" @checked(old('worked_abroad_before', $profile?->worked_abroad_before))>
                        <label class="form-check-label" for="workedAbroad">Worked abroad before</label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mb-4">Save Profile</button>
    </form>

    <div class="row g-4">
        {{-- Employment History --}}
        <div class="col-lg-6">
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Employment History</h3>
                @forelse($experiences as $experience)
                    <div class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold small">{{ $experience->occupation }}</div>
                            <div class="text-secondary small">{{ $experience->employer ?? '—' }} @if($experience->years_of_experience) · {{ $experience->years_of_experience }} yrs @endif</div>
                        </div>
                        <form method="POST" action="{{ route('job-seeker.profile.experience.destroy', $experience) }}" onsubmit="return confirm('Remove this experience?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">Remove</button>
                        </form>
                    </div>
                @empty
                    <p class="text-secondary small mb-3">No work experience added yet.</p>
                @endforelse

                <form method="POST" action="{{ route('job-seeker.profile.experience.store') }}" class="row g-2 mt-3 pt-3 border-top">
                    @csrf
                    <div class="col-12"><input type="text" name="occupation" class="form-control form-control-sm" placeholder="Occupation" required></div>
                    <div class="col-7"><input type="text" name="employer" class="form-control form-control-sm" placeholder="Employer"></div>
                    <div class="col-5"><input type="number" step="0.5" name="years_of_experience" class="form-control form-control-sm" placeholder="Years"></div>
                    <div class="col-12"><button type="submit" class="btn btn-sm btn-outline-primary w-100">+ Add Experience</button></div>
                </form>
            </div>
        </div>

        {{-- Education --}}
        <div class="col-lg-6">
            <div class="card stat-card p-4">
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Education</h3>
                @forelse($educations as $education)
                    <div class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold small">{{ ucfirst(str_replace('_', ' ', $education->level)) }}</div>
                            <div class="text-secondary small">{{ $education->institution ?? '—' }} @if($education->course) · {{ $education->course }} @endif @if($education->graduation_year) ({{ $education->graduation_year }}) @endif</div>
                        </div>
                        <form method="POST" action="{{ route('job-seeker.profile.education.destroy', $education) }}" onsubmit="return confirm('Remove this education record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">Remove</button>
                        </form>
                    </div>
                @empty
                    <p class="text-secondary small mb-3">No education records added yet.</p>
                @endforelse

                <form method="POST" action="{{ route('job-seeker.profile.education.store') }}" class="row g-2 mt-3 pt-3 border-top">
                    @csrf
                    <div class="col-12">
                        <select name="level" class="form-select form-select-sm" required>
                            <option value="">Select level</option>
                            @foreach(['primary' => 'Primary', 'secondary' => 'Secondary', 'diploma' => 'Diploma', 'bachelor' => "Bachelor's Degree", 'master' => "Master's Degree", 'not_applicable' => 'Not Applicable'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-7"><input type="text" name="institution" class="form-control form-control-sm" placeholder="Institution"></div>
                    <div class="col-5"><input type="number" name="graduation_year" class="form-control form-control-sm" placeholder="Year"></div>
                    <div class="col-12"><input type="text" name="course" class="form-control form-control-sm" placeholder="Course"></div>
                    <div class="col-12"><button type="submit" class="btn btn-sm btn-outline-primary w-100">+ Add Education</button></div>
                </form>
            </div>
        </div>
    </div>

</x-job-seeker-layout>
