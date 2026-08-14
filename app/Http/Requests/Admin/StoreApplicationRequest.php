<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\StudyApplication::class);
    }

    public function rules(): array
    {
        return [
            // Free-text, not a dropdown — Admin can name a university/course that
            // isn't in the catalog yet; the controller firstOrCreate()s it.
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
