<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployerInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Invoice::class);
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            // Ties this invoice to a specific candidate's recruitment fee
            // (e.g. "Candidate Processing" for one placement) — same field
            // and same job_posting filter chain the Employer-facing
            // Invoices page already reads (invoice.jobApplication.jobPosting).
            'job_application_id' => ['nullable', 'exists:job_applications,id'],
            'due_date' => ['nullable', 'date'],
            'currency' => ['required', 'string', 'size:3'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
