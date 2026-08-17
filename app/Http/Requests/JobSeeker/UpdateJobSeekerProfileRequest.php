<?php

namespace App\Http\Requests\JobSeeker;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobSeekerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // always operates on the current user's own profile
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],

            'professional_title' => ['nullable', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:100'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'languages' => ['nullable', 'string', 'max:500'],
            'certifications' => ['nullable', 'string', 'max:1000'],

            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_issue_date' => ['nullable', 'date'],
            'passport_expiry_date' => ['nullable', 'date', 'after:passport_issue_date'],
            'passport_country' => ['nullable', 'string', 'max:100'],

            'preferred_countries_text' => ['nullable', 'string', 'max:255'],
            'preferred_industries_text' => ['nullable', 'string', 'max:255'],
            'preferred_positions_text' => ['nullable', 'string', 'max:255'],
            'preferred_employment_type' => ['nullable', 'string', 'max:50'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'expected_salary_currency' => ['nullable', 'string', 'max:3'],
            'earliest_availability' => ['nullable', 'date'],
            'willing_to_relocate' => ['nullable', 'boolean'],
            'worked_abroad_before' => ['nullable', 'boolean'],
        ];
    }
}
