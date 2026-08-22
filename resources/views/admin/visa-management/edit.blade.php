<x-admin-layout title="Edit Visa Application">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Edit Visa Application — {{ $visaApplication->displayName() }}</h2>

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
        <form method="POST" action="{{ route('admin.visa-management.update', $visaApplication) }}">
            @csrf
            @method('PATCH')

            @if($visaApplication->applicantType() === 'guest')
                <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Applicant Details</h3>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $visaApplication->first_name) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $visaApplication->middle_name) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $visaApplication->last_name) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $visaApplication->date_of_birth?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gender</label>
                        <input type="text" name="gender" class="form-control" value="{{ old('gender', $visaApplication->gender) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nationality</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $visaApplication->nationality) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Country of Residence</label>
                        <input type="text" name="country_of_residence" class="form-control" value="{{ old('country_of_residence', $visaApplication->country_of_residence) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Passport Number</label>
                        <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number', $visaApplication->passport_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Passport Expiry</label>
                        <input type="date" name="passport_expiry" class="form-control" value="{{ old('passport_expiry', $visaApplication->passport_expiry?->format('Y-m-d')) }}">
                    </div>
                </div>
            @else
                <div class="alert alert-secondary small">
                    This applicant's personal details are managed on their own profile, not here — edit their name, contact info, etc. from their Workspace directly.
                </div>
            @endif

            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Visa Details</h3>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Destination Country</label>
                    <input type="text" name="destination_country" class="form-control" value="{{ old('destination_country', $visaApplication->destination_country) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Visa Type</label>
                    <select name="visa_type" class="form-select">
                        <option value="">Select visa type</option>
                        @foreach(['Work Visa', 'Student Visa', 'Tourist Visa', 'Business Visa'] as $type)
                            <option value="{{ $type }}" @selected(old('visa_type', $visaApplication->visa_type) === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Purpose of Travel</label>
                    <input type="text" name="purpose_of_travel" class="form-control" value="{{ old('purpose_of_travel', $visaApplication->purpose_of_travel) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Expected Travel Date</label>
                    <input type="date" name="expected_travel_date" class="form-control" value="{{ old('expected_travel_date', $visaApplication->expected_travel_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Duration of Stay</label>
                    <input type="text" name="duration_of_stay" class="form-control" value="{{ old('duration_of_stay', $visaApplication->duration_of_stay) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Embassy Appointment</label>
                    <input type="datetime-local" name="embassy_appointment_at" class="form-control" value="{{ old('embassy_appointment_at', $visaApplication->embassy_appointment_at?->format('Y-m-d\TH:i')) }}">
                </div>
            </div>

            <h3 class="h6 fw-semibold mb-3" style="font-family:'Poppins',sans-serif;">Travel History</h3>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Previously Applied for This Visa</label>
                    <select name="previously_applied" class="form-select"><option value="">Select</option><option @selected(old('previously_applied', $visaApplication->previously_applied) === 'Yes')>Yes</option><option @selected(old('previously_applied', $visaApplication->previously_applied) === 'No')>No</option></select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Previously Refused a Visa</label>
                    <select name="previously_refused" class="form-select"><option value="">Select</option><option @selected(old('previously_refused', $visaApplication->previously_refused) === 'Yes')>Yes</option><option @selected(old('previously_refused', $visaApplication->previously_refused) === 'No')>No</option></select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Refusal Explanation</label>
                    <textarea name="refusal_explanation" class="form-control" rows="2">{{ old('refusal_explanation', $visaApplication->refusal_explanation) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Travelled Internationally Before</label>
                    <select name="travelled_internationally" class="form-select"><option value="">Select</option><option @selected(old('travelled_internationally', $visaApplication->travelled_internationally) === 'Yes')>Yes</option><option @selected(old('travelled_internationally', $visaApplication->travelled_internationally) === 'No')>No</option></select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Countries Visited</label>
                    <input type="text" name="countries_visited" class="form-control" value="{{ old('countries_visited', $visaApplication->countries_visited) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Additional Info</label>
                    <textarea name="additional_info" class="form-control" rows="3">{{ old('additional_info', $visaApplication->additional_info) }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.visa-management.show', $visaApplication) }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-admin-layout>
