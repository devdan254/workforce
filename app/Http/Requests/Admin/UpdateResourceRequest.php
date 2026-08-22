<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('resource'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:150'],
            'type' => ['required', Rule::in(['guide', 'faq', 'form', 'checklist'])],
            'audience' => ['nullable', 'array'],
            'audience.*' => [Rule::in(['student', 'job_seeker', 'employer'])],
            'body' => ['nullable', 'string', 'max:10000'],
            'file' => ['nullable', 'file', 'max:10240'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
