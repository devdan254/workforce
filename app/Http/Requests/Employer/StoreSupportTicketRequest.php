<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

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
            'category' => ['required', 'in:Worker Requests,Candidates,Job Postings,Documents,Payments,Job Offers,Other'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }
}
