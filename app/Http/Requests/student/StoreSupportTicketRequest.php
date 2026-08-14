<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\SupportTicket::class);
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(['Applications', 'Documents', 'Payments', 'Visa', 'Travel', 'Other'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }
}
