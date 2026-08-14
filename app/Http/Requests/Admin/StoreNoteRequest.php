<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // No dedicated NotePolicy for Stage 1 — any authenticated staff member
        // (never a student) can leave an internal note. Lightweight on purpose;
        // notes are low-stakes commentary, not a workflow-critical action.
        return ! $this->user()->isStudent();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:2000'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }
}
