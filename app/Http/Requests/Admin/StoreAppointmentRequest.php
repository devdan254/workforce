<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Appointment::class);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:100'],
            'mode' => ['required', Rule::in(['video', 'physical', 'phone'])],
            'scheduled_at' => ['required', 'date'],
            'study_application_id' => ['nullable', 'exists:study_applications,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
