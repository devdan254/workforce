<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('jobPosting'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'job_category_id' => ['required', 'exists:job_categories,id'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'vacancies' => ['required', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'employment_type' => ['required', Rule::in(['permanent', 'contract', 'temporary', 'seasonal'])],
            'experience_required' => ['nullable', 'string', 'max:150'],
            'education_requirement' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
            'languages' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'accommodation_provided' => ['nullable', 'boolean'],
            'meals_provided' => ['nullable', 'boolean'],
            'visa_support_provided' => ['nullable', 'boolean'],
            'air_ticket_provided' => ['nullable', 'boolean'],
            'working_hours' => ['nullable', 'string', 'max:100'],
            'application_deadline' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'open', 'closed', 'filled', 'archived'])],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
