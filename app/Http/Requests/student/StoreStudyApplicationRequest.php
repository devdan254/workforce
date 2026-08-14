<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudyApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\StudyApplication::class);
    }

    public function rules(): array
    {
        return [
            // Profile fields, upserted onto student_profiles/users — NOT stored
            // on study_applications itself, so they stay a single source of truth
            // across every application this student ever submits.
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'highest_qualification' => ['required', 'string', 'max:100'],

            // The application itself.
            'university_id' => ['required', 'exists:universities,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'intake' => ['nullable', 'string', 'max:100'],
        ];
    }
}
