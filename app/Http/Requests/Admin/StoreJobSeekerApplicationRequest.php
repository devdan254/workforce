<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobSeekerApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\JobApplication::class);
    }

    public function rules(): array
    {
        $jobSeeker = $this->route('jobSeeker');

        return [
            // Dropdown, not free-text — unlike University/Course for Student
            // applications, job_postings are a controlled Altura catalog per
            // the spec ("Jobs are controlled by Altura"), not something Admin
            // invents ad-hoc per application.
            'job_posting_id' => [
                'required',
                'exists:job_postings,id',
                // job_applications has a DB-level unique constraint on
                // (job_seeker_id, job_posting_id) — this catches the same
                // rule as a friendly validation message instead of a raw
                // SQL constraint-violation crash.
                Rule::unique('job_applications')->where('job_seeker_id', $jobSeeker->id),
            ],
        ];
    }

    public function messages(): array
    {
        return ['job_posting_id.unique' => 'This candidate has already applied to that job posting.'];
    }
}
