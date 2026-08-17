<?php

namespace App\Http\Requests\JobSeeker;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Invoice $invoice */
        $invoice = $this->route('invoice');

        return $this->user()->can('pay', $invoice);
    }

    public function rules(): array
    {
        /** @var Invoice $invoice */
        $invoice = $this->route('invoice');

        return [
            'amount' => ['required', 'numeric', 'min:1', Rule::when(true, ['max:'.max($invoice->balance, 0)])],
            'method' => ['required', Rule::in(['mpesa', 'card', 'bank_transfer', 'cash'])],
            'reference' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return ['amount.max' => 'You can\'t pay more than the outstanding balance on this invoice.'];
    }
}
