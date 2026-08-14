<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Payment::class);
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::in(['mpesa', 'card', 'bank_transfer', 'cash'])],
            'reference' => ['nullable', 'string', 'max:100'],
            // Admin asserting the payment already happened (e.g. cash received in person) —
            // confirms it in the same action instead of leaving it pending for a second click.
            'confirm_immediately' => ['nullable', 'boolean'],
        ];
    }
}
