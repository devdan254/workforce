<x-admin-layout title="Add Visa Application">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Add Visa Application</h2>

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
        <form method="POST" action="{{ route('admin.visa-management.store') }}">
            @csrf

            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Applicant Source</h3>
            <div class="d-flex gap-3 mb-4" id="sourceToggle">
                <div class="form-check">
                    <input type="radio" name="applicant_source" value="standalone" class="form-check-input" id="srcStandalone" checked>
                    <label class="form-check-label" for="srcStandalone">New Applicant (visa only)</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="applicant_source" value="student" class="form-check-input" id="srcStudent">
                    <label class="form-check-label" for="srcStudent">Existing Student</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="applicant_source" value="job_seeker" class="form-check-input" id="srcJobSeeker">
                    <label class="form-check-label" for="srcJobSeeker">Existing Job Seeker</label>
                </div>
            </div>

            <div id="panelStudent" style="display:none;" class="mb-4">
                <label class="form-label fw-semibold">Select Student</label>
                <select name="user_id" class="form-select">
                    <option value="">Select a student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} — {{ $student->email }}</option>
                    @endforeach
                </select>
                <div class="form-text">Attaches to that student's most recent study application. They must not already have a visa application.</div>
            </div>

            <div id="panelJobSeeker" style="display:none;" class="mb-4">
                <label class="form-label fw-semibold">Select Job Seeker</label>
                <select name="user_id" class="form-select">
                    <option value="">Select a job seeker</option>
                    @foreach($jobSeekers as $jobSeeker)
                        <option value="{{ $jobSeeker->id }}">{{ $jobSeeker->name }} — {{ $jobSeeker->email }}</option>
                    @endforeach
                </select>
                <div class="form-text">Attaches to that job seeker's most recent job application. They must not already have a visa application.</div>
            </div>

            <div id="panelStandalone" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nationality</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Passport Number</label>
                        <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number') }}">
                    </div>
                </div>
                <div class="form-text mt-1">Creates a new account for this person (no portal access) so documents, payments and invoices can be attached to them once those stages exist.</div>
            </div>

            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Visa Details</h3>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Destination Country</label>
                    <input type="text" name="destination_country" class="form-control" value="{{ old('destination_country') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Visa Type</label>
                    <select name="visa_type" class="form-select">
                        <option value="">Select visa type</option>
                        <option>Work Visa</option>
                        <option>Student Visa</option>
                        <option>Tourist Visa</option>
                        <option>Business Visa</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Purpose of Travel</label>
                    <input type="text" name="purpose_of_travel" class="form-control" value="{{ old('purpose_of_travel') }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Create Visa Application</button>
            <a href="{{ route('admin.visa-management.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

    <script>
        (function () {
            var radios = document.querySelectorAll('#sourceToggle input[name="applicant_source"]');
            var panels = { standalone: document.getElementById('panelStandalone'), student: document.getElementById('panelStudent'), job_seeker: document.getElementById('panelJobSeeker') };
            function refresh() {
                var selected = document.querySelector('#sourceToggle input[name="applicant_source"]:checked').value;
                Object.keys(panels).forEach(function (key) {
                    panels[key].style.display = (key === selected) ? '' : 'none';
                });
            }
            radios.forEach(function (r) { r.addEventListener('change', refresh); });
            refresh();
        })();
    </script>

</x-admin-layout>
