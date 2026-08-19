<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestEmployerDocumentRequest extends FormRequest
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
            // Optional — leave blank for a general company-level request,
            // set it to tie the request to a specific job posting.
            'job_posting_id' => ['nullable', 'exists:job_postings,id'],
        ];
    }
}
