<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployerAppointmentRequest extends FormRequest
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
            // Optional — leave blank for a direct employer appointment
            // (Recruitment Consultation, Onboarding); set it to tie the
            // appointment to a specific candidate's interview instead.
            // Matches the two ownership shapes AppointmentPolicy::view()
            // already checks for Employer (direct student_id, or
            // transitive via job_application -> job_posting -> employer_id).
            'job_application_id' => ['nullable', 'exists:job_applications,id'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
