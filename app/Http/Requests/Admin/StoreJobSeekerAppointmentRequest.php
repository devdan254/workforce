<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobSeekerAppointmentRequest extends FormRequest
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
            'job_application_id' => ['nullable', 'exists:job_applications,id'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
