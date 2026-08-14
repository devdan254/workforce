<x-student-layout title="Start New Application">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Start a New Study Application</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card stat-card p-4" style="max-width: 720px;">
        <form method="POST" action="{{ route('student.applications.store') }}">
            @csrf

            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Your Details</h3>
            <p class="text-secondary small mb-3">
                We already have some of this on file — update anything that's changed.
                This also fills in your <a href="{{ route('profile.edit') }}" target="_blank">Profile</a> for future applications.
            </p>

            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                    <div class="form-text">Edit this on your <a href="{{ route('profile.edit') }}">Profile</a> page.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country of Residence <span class="text-danger">*</span></label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', auth()->user()->studentProfile?->country) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Highest Education Level <span class="text-danger">*</span></label>
                    <select name="highest_qualification" class="form-select" required>
                        <option value="">Select level</option>
                        @foreach(['High School', 'Diploma', "Bachelor's Degree", "Master's Degree"] as $level)
                            <option value="{{ $level }}" @selected(old('highest_qualification', auth()->user()->studentProfile?->highest_qualification) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-4">

            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">This Application</h3>

            <div class="mb-3">
                <label class="form-label fw-semibold">University</label>
                <select name="university_id" id="university_id" class="form-select" required>
                    <option value="">Select a university</option>
                    @foreach($universities as $university)
                        <option value="{{ $university->id }}" @selected(old('university_id') == $university->id)>{{ $university->name }} — {{ $university->country }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Course</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="">Select a university first</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Preferred Intake</label>
                <input type="text" name="intake" class="form-control" value="{{ old('intake') }}" placeholder="e.g. September 2026">
            </div>

            <button type="submit" class="btn btn-primary">Start Application</button>
            <a href="{{ route('student.applications.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

    {{-- Minimal vanilla JS to filter the course dropdown by selected university —
         data is embedded server-side, no extra API endpoint needed for Stage 1. --}}
    <script>
        const universityCourses = @json($universities->mapWithKeys(fn($u) => [$u->id => $u->courses->map(fn($c) => ['id' => $c->id, 'name' => $c->name])]));
        const universitySelect = document.getElementById('university_id');
        const courseSelect = document.getElementById('course_id');

        universitySelect.addEventListener('change', function () {
            const courses = universityCourses[this.value] || [];
            courseSelect.innerHTML = courses.length
                ? courses.map(c => `<option value="${c.id}">${c.name}</option>`).join('')
                : '<option value="">No active courses for this university</option>';
        });

        // Re-populate courses on page reload after a validation error, so the
        // previously selected university's courses aren't just an empty dropdown.
        if (universitySelect.value) {
            universitySelect.dispatchEvent(new Event('change'));
        }
    </script>

</x-student-layout>
