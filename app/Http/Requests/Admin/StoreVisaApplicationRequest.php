<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * "Applicant Source" branches the whole form: an existing Student, an
 * existing Job Seeker, or a brand-new standalone applicant with no account
 * yet. Personal/passport fields are only required for the standalone path —
 * a Student or Job Seeker's identity already lives on their own profile,
 * so re-collecting it here would create a second, driftable copy.
 */
class StoreVisaApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('visa.create');
    }

    public function rules(): array
    {
        $source = $this->input('applicant_source');

        $rules = [
            'applicant_source' => ['required', Rule::in(['student', 'job_seeker', 'standalone'])],
            'destination_country' => ['required', 'string', 'max:100'],
            'visa_type' => ['nullable', 'string', 'max:100'],
            'purpose_of_travel' => ['nullable', 'string', 'max:100'],
        ];

        if ($source === 'student') {
            $rules['user_id'] = ['required', 'exists:users,id'];
        } elseif ($source === 'job_seeker') {
            $rules['user_id'] = ['required', 'exists:users,id'];
        } else {
            $rules['first_name'] = ['required', 'string', 'max:100'];
            $rules['last_name'] = ['required', 'string', 'max:100'];
            $rules['email'] = ['required', 'email', 'max:255', 'unique:users,email'];
            $rules['phone'] = ['nullable', 'string', 'max:30'];
            $rules['date_of_birth'] = ['nullable', 'date'];
            $rules['nationality'] = ['nullable', 'string', 'max:100'];
            $rules['passport_number'] = ['nullable', 'string', 'max:50'];
        }

        return $rules;
    }
}
