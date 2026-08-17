@php
    $p = $posting; // shorthand — null on create
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Job Details</h3>

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Job Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $p?->title) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Category *</label>
                    <select name="job_category_id" class="form-select" required>
                        <option value="">Select</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('job_category_id', $p?->job_category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country *</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $p?->country) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $p?->city) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Number of Positions *</label>
                    <input type="number" name="vacancies" class="form-control" value="{{ old('vacancies', $p?->vacancies ?? 1) }}" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Employment Type *</label>
                    <select name="employment_type" class="form-select" required>
                        @foreach(['permanent' => 'Permanent', 'contract' => 'Contract', 'temporary' => 'Temporary', 'seasonal' => 'Seasonal'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('employment_type', $p?->employment_type) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Application Deadline</label>
                    <input type="date" name="application_deadline" class="form-control" value="{{ old('application_deadline', $p?->application_deadline?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Experience Required</label>
                    <input type="text" name="experience_required" class="form-control" placeholder="e.g. 2+ years" value="{{ old('experience_required', $p?->experience_required) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Education Requirement</label>
                    <input type="text" name="education_requirement" class="form-control" value="{{ old('education_requirement', $p?->education_requirement) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Working Hours</label>
                    <input type="text" name="working_hours" class="form-control" placeholder="e.g. 48 hrs/week" value="{{ old('working_hours', $p?->working_hours) }}">
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Compensation</h3>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Currency *</label>
                    <input type="text" name="currency" class="form-control" maxlength="3" value="{{ old('currency', $p?->currency ?? 'USD') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Salary Min</label>
                    <input type="number" step="0.01" name="salary_min" class="form-control" value="{{ old('salary_min', $p?->salary_min) }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Salary Max</label>
                    <input type="number" step="0.01" name="salary_max" class="form-control" value="{{ old('salary_max', $p?->salary_max) }}">
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Description</h3>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $p?->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Responsibilities</label>
                <textarea name="responsibilities" class="form-control" rows="3">{{ old('responsibilities', $p?->responsibilities) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Requirements</label>
                <textarea name="requirements" class="form-control" rows="3">{{ old('requirements', $p?->requirements) }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Skills</label>
                    <textarea name="skills" class="form-control" rows="2">{{ old('skills', $p?->skills) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Languages</label>
                    <textarea name="languages" class="form-control" rows="2">{{ old('languages', $p?->languages) }}</textarea>
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label fw-semibold">Benefits</label>
                <textarea name="benefits" class="form-control" rows="3">{{ old('benefits', $p?->benefits) }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">What's Included</h3>
            @foreach(['accommodation_provided' => 'Accommodation', 'meals_provided' => 'Meals', 'visa_support_provided' => 'Visa Support', 'air_ticket_provided' => 'Air Ticket'] as $field => $label)
                <div class="form-check mb-2">
                    <input type="checkbox" name="{{ $field }}" value="1" class="form-check-input" id="{{ $field }}" @checked(old($field, $p?->{$field}))>
                    <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Photo</h3>
            @if($p?->image_url)
                <img src="{{ $p->image_url }}" alt="{{ $p->title }}" class="w-100 rounded mb-2" style="max-height:150px;object-fit:cover;">
            @else
                <p class="text-secondary small">No photo yet — a category icon shows instead until one's uploaded.</p>
            @endif
            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
        </div>

        <div class="card stat-card p-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Status</h3>
            <select name="status" class="form-select mb-3" required>
                @foreach(['draft' => 'Draft', 'open' => 'Open (Published)', 'closed' => 'Closed', 'filled' => 'Filled', 'archived' => 'Archived'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $p?->status ?? 'draft') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-text mb-3">Once created, use Publish/Unpublish/Close/Archive from the list page for quicker one-click state changes — this dropdown is here for direct control when needed.</div>
            <button type="submit" class="btn btn-primary w-100">{{ $p ? 'Save Changes' : 'Create Job Posting' }}</button>
            <a href="{{ route('admin.job-postings.index') }}" class="btn btn-light w-100 mt-2">Cancel</a>
        </div>
    </div>
</div>
