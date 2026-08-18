<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // always operates on the current user's own profile
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:30'],
            'company_name' => ['required', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'company_size' => ['nullable', Rule::in(['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'])],
            'city' => ['nullable', 'string', 'max:100'],
            'company_description' => ['nullable', 'string', 'max:2000'],
            'contact_job_title' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
