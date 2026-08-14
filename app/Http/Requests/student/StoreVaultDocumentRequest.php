<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreVaultDocumentRequest extends FormRequest
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
            // No cap on how many documents a student can have in total — this only
            // limits a single submission, so one add-action doesn't overwhelm the request.
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'], // 10MB each
        ];
    }

    public function messages(): array
    {
        return [
            'files.max' => 'You can add up to 20 files in one go — add more in a second batch if you need to.',
        ];
    }
}
