<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestDocumentRequest extends FormRequest
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
            // set it to tie the request to one specific application.
            'study_application_id' => ['nullable', 'exists:study_applications,id'],
        ];
    }
}
