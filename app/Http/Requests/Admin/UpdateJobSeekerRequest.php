<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobSeekerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateJobSeeker', $this->route('jobSeeker'));
    }

    public function rules(): array
    {
        $jobSeeker = $this->route('jobSeeker');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($jobSeeker->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
