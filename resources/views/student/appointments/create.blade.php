<x-student-layout title="Book Appointment">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Book an Appointment</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card stat-card p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('student.appointments.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Appointment Type</label>
                <select name="type" class="form-select" required>
                    <option value="">Select a type</option>
                    <option>Student Consultation</option>
                    <option>Document Review</option>
                    <option>Visa Interview Prep</option>
                    <option>Pre-Departure Briefing</option>
                    <option>General Question</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Related Application (optional)</label>
                <select name="study_application_id" class="form-select">
                    <option value="">Not related to a specific application</option>
                    @foreach($applications as $application)
                        <option value="{{ $application->id }}">{{ $application->university->name }} — {{ $application->course->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preferred Date &amp; Time</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mode</label>
                    <select name="mode" class="form-select" required>
                        <option value="video">Video Call</option>
                        <option value="physical">In Person</option>
                        <option value="phone">Phone Call</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Anything specific you'd like to discuss..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Request Appointment</button>
            <a href="{{ route('student.appointments.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-student-layout>
