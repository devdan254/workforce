<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployerDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Document::class);
    }

    public function rules(): array
    {
        return [
            'document_category_id' => ['required', 'exists:document_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'job_posting_id' => ['nullable', 'exists:job_postings,id'],
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ];
    }
}
