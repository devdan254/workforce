<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkerRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\WorkerRequest::class);
    }

    public function rules(): array
    {
        return [
            'contact_name' => ['nullable', 'string', 'max:150'],
            'contact_job_title' => ['nullable', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],

            'job_title' => ['required', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1'],
            'preferred_experience' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['required', Rule::in(['permanent', 'contract', 'temporary', 'seasonal', 'part_time', 'full_time'])],
            'age_range' => ['nullable', 'string', 'max:50'],
            'preferred_start_date' => ['nullable', 'date'],
            'work_location' => ['nullable', 'string', 'max:150'],

            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'currency' => ['nullable', 'string', 'size:3'],
            'responsibilities' => ['nullable', 'string', 'max:2000'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'benefits' => ['nullable', 'string', 'max:1000'],
            'requirements' => ['nullable', 'string', 'max:2000'],
            'additional_requirements' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
