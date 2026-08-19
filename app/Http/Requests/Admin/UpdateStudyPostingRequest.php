<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudyPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('studyPosting'));
    }

    public function rules(): array
    {
        return [
            'university_name' => ['required', 'string', 'max:150'],
            'country' => ['required', 'string', 'max:100'],
            'scholarship_type' => ['required', Rule::in(['full', 'partial', 'none'])],
            'courses_offered' => ['required', 'string'],
            'school_fees_per_semester' => ['nullable', 'numeric', 'min:0'],
            'fees_currency' => ['required', 'string', 'size:3'],
            'age_requirement' => ['nullable', 'string', 'max:100'],
            'requirements' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'intake' => ['nullable', 'string', 'max:150'],
            'application_eligibility' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'open', 'closed', 'archived'])],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
