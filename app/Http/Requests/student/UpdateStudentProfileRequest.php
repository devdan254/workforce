<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // always operates on the current user's own profile — no ID param, no ownership ambiguity
    }

    public function rules(): array
    {
        return [
            // Personal
            'phone' => ['required', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],

            // Academic
            'highest_qualification' => ['nullable', 'string', 'max:100'],
            'institution' => ['nullable', 'string', 'max:150'],
            'graduation_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 10)],
            'field_of_study' => ['nullable', 'string', 'max:150'],
            'grade' => ['nullable', 'string', 'max:50'],

            // Passport
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_issue_date' => ['nullable', 'date'],
            'passport_expiry_date' => ['nullable', 'date', 'after:passport_issue_date'],
            'passport_country' => ['nullable', 'string', 'max:100'],

            // Preferences
            'preferred_countries' => ['nullable', 'array'],
            'preferred_countries.*' => ['string', 'max:100'],
            'preferred_course' => ['nullable', 'string', 'max:150'],
            'preferred_study_level' => ['nullable', 'string', 'max:50'],
            'preferred_intake' => ['nullable', 'string', 'max:100'],
        ];
    }
}
