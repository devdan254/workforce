@php
    $p = $posting; // shorthand — null on create
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">University / College Details</h3>

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">University / College Name *</label>
                    <input type="text" name="university_name" class="form-control" value="{{ old('university_name', $p?->university_name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Country *</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $p?->country) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Scholarship *</label>
                    <select name="scholarship_type" class="form-select" required>
                        <option value="full" @selected(old('scholarship_type', $p?->scholarship_type) === 'full')>Full</option>
                        <option value="partial" @selected(old('scholarship_type', $p?->scholarship_type) === 'partial')>Partial</option>
                        <option value="none" @selected(old('scholarship_type', $p?->scholarship_type ?? 'none') === 'none')>None</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Age Requirement</label>
                    <input type="text" name="age_requirement" class="form-control" placeholder="e.g. 18–30, or No limit" value="{{ old('age_requirement', $p?->age_requirement) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Intake</label>
                    <input type="text" name="intake" class="form-control" placeholder="e.g. September 2026, January 2027" value="{{ old('intake', $p?->intake) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Courses Offered *</label>
                    <textarea name="courses_offered" class="form-control" rows="3" placeholder="One course per line, or comma-separated — e.g. Computer Science, Nursing, Business Administration" required>{{ old('courses_offered', $p?->courses_offered) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">School Fees</h3>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Currency *</label>
                    <input type="text" name="fees_currency" class="form-control" maxlength="3" value="{{ old('fees_currency', $p?->fees_currency ?? 'USD') }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">School Fees per Semester</label>
                    <input type="number" step="0.01" name="school_fees_per_semester" class="form-control" value="{{ old('school_fees_per_semester', $p?->school_fees_per_semester) }}">
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Description &amp; Requirements</h3>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $p?->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Requirements</label>
                <textarea name="requirements" class="form-control" rows="3">{{ old('requirements', $p?->requirements) }}</textarea>
            </div>
            <div>
                <label class="form-label fw-semibold">Application Eligibility</label>
                <textarea name="application_eligibility" class="form-control" rows="3">{{ old('application_eligibility', $p?->application_eligibility) }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Banner Image</h3>
            @if($p?->image_url)
                <img src="{{ $p->image_url }}" alt="{{ $p->university_name }}" class="w-100 rounded mb-2" style="max-height:150px;object-fit:cover;">
            @else
                <p class="text-secondary small">No banner uploaded yet.</p>
            @endif
            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
        </div>

        <div class="card stat-card p-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Status</h3>
            <select name="status" class="form-select mb-3" required>
                @foreach(['draft' => 'Draft', 'open' => 'Open (Published)', 'closed' => 'Closed', 'archived' => 'Archived'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $p?->status ?? 'draft') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-text mb-3">Once created, use Publish/Unpublish/Close/Archive from the list page for quicker one-click state changes — this dropdown is here for direct control when needed.</div>
            <button type="submit" class="btn btn-primary w-100">{{ $p ? 'Save Changes' : 'Create Study Posting' }}</button>
            <a href="{{ route('admin.study-postings.index') }}" class="btn btn-light w-100 mt-2">Cancel</a>
        </div>
    </div>
</div>
