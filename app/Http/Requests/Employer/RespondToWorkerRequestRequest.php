<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class RespondToWorkerRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('respond', $this->route('workerRequest'));
    }

    public function rules(): array
    {
        return [
            'employer_response' => ['required', 'string', 'max:2000'],
        ];
    }
}
