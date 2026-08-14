<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('application'));
    }

    public function rules(): array
    {
        return [
            'university_name' => ['required', 'string', 'max:150'],
            'university_country' => ['required', 'string', 'max:100'],
            'course_name' => ['required', 'string', 'max:150'],
            'study_level' => ['required', 'in:certificate,diploma,bachelor,master,phd'],
            'intake' => ['nullable', 'string', 'max:100'],
            'application_deadline' => ['nullable', 'date'],
            'application_fee' => ['nullable', 'numeric', 'min:0'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'service_fee' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
