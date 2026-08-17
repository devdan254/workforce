<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobSeekerInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Invoice::class);
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
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
