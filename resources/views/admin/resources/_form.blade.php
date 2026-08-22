@php
    $r = $resource ?? null;
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Details</h3>

            <div class="mb-3">
                <label class="form-label fw-semibold">Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $r?->title) }}" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category</label>
                    <input type="text" name="category" class="form-control" placeholder="e.g. Study Abroad Guide, Country Guides" value="{{ old('category', $r?->category) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type *</label>
                    <select name="type" class="form-select" required>
                        @foreach(['guide' => 'Guide', 'faq' => 'FAQ', 'form' => 'Form', 'checklist' => 'Checklist'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $r?->type) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="body" class="form-control" rows="6" placeholder="The resource's own written content — used when it doesn't need a file, or alongside one as context.">{{ old('body', $r?->body) }}</textarea>
            </div>
        </div>

        <div class="card stat-card p-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">File (Optional)</h3>
            @if($r?->file_path)
                <p class="small text-secondary mb-2"><i class="fa-solid fa-paperclip"></i> A file is already attached. Uploading a new one replaces it.</p>
            @else
                <p class="small text-secondary mb-2">Not every resource needs a file — plain title + description is enough for a FAQ or short guide.</p>
            @endif
            <input type="file" name="file" class="form-control">
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card p-4 mb-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Audience</h3>
            <p class="small text-secondary mb-3">Who should see this. Leave everything unchecked to show it to everyone.</p>
            @php $selectedAudience = old('audience', $r?->audience ?? []); @endphp
            <div class="form-check mb-2">
                <input type="checkbox" name="audience[]" value="student" class="form-check-input" id="audience-student" @checked(in_array('student', $selectedAudience))>
                <label class="form-check-label" for="audience-student">Students</label>
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" name="audience[]" value="job_seeker" class="form-check-input" id="audience-job-seeker" @checked(in_array('job_seeker', $selectedAudience))>
                <label class="form-check-label" for="audience-job-seeker">Job Seekers</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="audience[]" value="employer" class="form-check-input" id="audience-employer" @checked(in_array('employer', $selectedAudience))>
                <label class="form-check-label" for="audience-employer">Employers</label>
            </div>
        </div>

        <div class="card stat-card p-4">
            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Status</h3>
            <div class="form-check mb-3">
                <input type="checkbox" name="is_published" value="1" class="form-check-input" id="is-published" @checked(old('is_published', $r?->is_published ?? false))>
                <label class="form-check-label" for="is-published">Published — visible in the portal now</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">{{ $r ? 'Save Changes' : 'Create Resource' }}</button>
            <a href="{{ route('admin.resources.index') }}" class="btn btn-light w-100 mt-2">Cancel</a>
        </div>
    </div>
</div>
