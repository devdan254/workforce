<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('appointment'));
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:100'],
            'mode' => ['required', Rule::in(['video', 'physical', 'phone'])],
            'scheduled_at' => ['required', 'date'],
            'staff_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
