<?php

namespace App\Http\Requests\JobSeeker;

use Illuminate\Foundation\Http\FormRequest;

class StoreWizardStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // wizard steps are session-scoped to the logged-in job seeker only
    }

    public function rules(): array
    {
        return match ((int) $this->route('step')) {
            1 => [
                'full_name' => ['required', 'string', 'max:150'],
                'national_id' => ['required', 'string', 'max:50'],
                'date_of_birth' => ['required', 'date', 'before:today'],
                'gender' => ['required', 'in:Male,Female'],
                'nationality' => ['required', 'string', 'max:100'],
                'phone' => ['required', 'string', 'max:30'],
                'email' => ['required', 'email', 'max:150'],
            ],
            2 => [
                'country' => ['required', 'string', 'max:100'],
                'county' => ['required', 'string', 'max:100'],
                'city' => ['nullable', 'string', 'max:100'],
                'town' => ['nullable', 'string', 'max:100'],
                'address_line' => ['nullable', 'string', 'max:255'],
            ],
            3 => [
                'level' => ['required', 'in:primary,secondary,diploma,bachelor,master,not_applicable'],
                'institution' => ['nullable', 'string', 'max:150'],
                'course' => ['nullable', 'string', 'max:150'],
                'graduation_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 10)],
            ],
            4 => [
                'occupation' => ['nullable', 'array'],
                'occupation.*' => ['nullable', 'string', 'max:150'],
                'employer' => ['nullable', 'array'],
                'employer.*' => ['nullable', 'string', 'max:150'],
                'years_of_experience' => ['nullable', 'array'],
                'years_of_experience.*' => ['nullable', 'numeric', 'min:0', 'max:60'],
            ],
            6 => [
                'worked_abroad_before' => ['required', 'boolean'],
                'preferred_country' => ['nullable', 'string', 'max:100'],
                'preferred_industry' => ['nullable', 'string', 'max:100'],
                'expected_salary' => ['nullable', 'numeric', 'min:0'],
                'earliest_availability' => ['nullable', 'date'],
            ],
            default => [],
        };
    }
}
