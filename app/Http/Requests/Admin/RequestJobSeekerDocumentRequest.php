<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestJobSeekerDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('request', \App\Models\Document::class);
    }

    public function rules(): array
    {
        return [
            'document_category_id' => ['required', 'exists:document_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            // Optional — leave blank for a general vault-level request,
            // set it to tie the request to one specific job application.
            'job_application_id' => ['nullable', 'exists:job_applications,id'],
        ];
    }
}
