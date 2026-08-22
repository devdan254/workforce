<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Personal/passport fields are only actually saved for a standalone/guest
 * applicant (see VisaManagementController::update()) — for a Student or Job
 * Seeker, that data belongs to their own profile, not this record, so
 * editing it here would create a second copy that could silently drift
 * from the real one. The fields still validate the same way regardless
 * (harmless if present but unused) rather than needing two separate request
 * classes for what's otherwise one form.
 */
class UpdateVisaApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('visaApplication'));
    }

    public function rules(): array
    {
        return [
            'destination_country' => ['required', 'string', 'max:100'],
            'visa_type' => ['nullable', 'string', 'max:100'],
            'purpose_of_travel' => ['nullable', 'string', 'max:100'],
            'expected_travel_date' => ['nullable', 'date'],
            'duration_of_stay' => ['nullable', 'string', 'max:100'],
            'embassy_appointment_at' => ['nullable', 'date'],
            'previously_applied' => ['nullable', 'string', 'max:10'],
            'previously_refused' => ['nullable', 'string', 'max:10'],
            'refusal_explanation' => ['nullable', 'string', 'max:1000'],
            'travelled_internationally' => ['nullable', 'string', 'max:10'],
            'countries_visited' => ['nullable', 'string', 'max:255'],
            'additional_info' => ['nullable', 'string', 'max:2000'],

            'first_name' => ['nullable', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'country_of_residence' => ['nullable', 'string', 'max:100'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_expiry' => ['nullable', 'date'],
        ];
    }
}
