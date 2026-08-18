<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConvertWorkerRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('review', $this->route('workerRequest'));
    }

    public function rules(): array
    {
        return [
            // The one genuinely missing field a Worker Request doesn't collect —
            // everything else (title, vacancies, employment_type, employer) maps
            // straight across. Salary/description/requirements/benefits/deadline
            // get filled in on the resulting posting's own Edit page afterward,
            // reusing JobPostingController entirely rather than duplicating it here.
            'job_category_id' => ['required', 'exists:job_categories,id'],
            'country' => ['required', 'string', 'max:100'],
        ];
    }
}
