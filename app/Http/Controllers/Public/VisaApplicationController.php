<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\AdminNotificationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Genuinely different from Job/Study Application: VisaApplication (the
 * model) only ever exists as a downstream step of an EXISTING Job or Study
 * Application already in progress — created by Admin, never standalone.
 * There's no backend entity for "a brand-new visitor's general visa
 * request" to attach to. Rather than invent new database infrastructure
 * for this (a real VisaRequest table + Admin management UI) or silently
 * throw away everything the wizard collects, this preserves the full
 * 5-step wizard experience but sends everything — including uploaded
 * documents as real email attachments — to info@alturaworkforce.com.
 * Nothing is persisted; the email IS the record, same principle as
 * Contact/Consultation, just carrying far more detail and real files.
 */
class VisaApplicationController extends Controller
{
    public function create(): View
    {
        return view('public.visa.application');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'string', 'max:20'],
            'nationality' => ['required', 'string', 'max:100'],
            'country_of_residence' => ['required', 'string', 'max:100'],
            'passport_number' => ['required', 'string', 'max:50'],
            'passport_expiry' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],

            'destination_country' => ['required', 'string', 'max:100'],
            'purpose_of_travel' => ['required', 'string', 'max:100'],
            'expected_travel_date' => ['nullable', 'date'],
            'duration_of_stay' => ['nullable', 'string', 'max:100'],
            'has_admission_letter' => ['nullable', 'string', 'max:10'],
            'has_employment_contract' => ['nullable', 'string', 'max:10'],
            'has_invitation_letter' => ['nullable', 'string', 'max:10'],

            'visa_type' => ['required', 'string', 'max:100'],
            'previously_applied' => ['nullable', 'string', 'max:10'],
            'previously_refused' => ['nullable', 'string', 'max:10'],
            'refusal_explanation' => ['nullable', 'string', 'max:1000'],
            'travelled_internationally' => ['nullable', 'string', 'max:10'],
            'countries_visited' => ['nullable', 'string', 'max:255'],

            'passport' => ['required', 'file', 'max:10240'],
            'passport_photo' => ['required', 'file', 'max:10240'],
            'admission_letter' => ['nullable', 'file', 'max:10240'],
            'employment_contract' => ['nullable', 'file', 'max:10240'],
            'invitation_letter' => ['nullable', 'file', 'max:10240'],
            'bank_statement' => ['nullable', 'file', 'max:10240'],
            'academic_certificates' => ['nullable', 'file', 'max:10240'],
            'additional_documents' => ['nullable', 'file', 'max:10240'],

            'additional_info' => ['nullable', 'string', 'max:2000'],
            'declaration_accurate' => ['accepted'],
            'declaration_consent' => ['accepted'],
        ]);

        // Read every uploaded file's bytes into memory NOW, while the
        // request (and therefore the temp upload) is still alive — see
        // AdminNotificationMail's own docblock for why this can't just
        // pass along getRealPath() instead.
        $fileFields = [
            'passport', 'passport_photo', 'admission_letter', 'employment_contract',
            'invitation_letter', 'bank_statement', 'academic_certificates', 'additional_documents',
        ];

        $attachments = collect($fileFields)
            ->filter(fn ($field) => $request->hasFile($field))
            ->map(function ($field) use ($request) {
                $file = $request->file($field);

                return [
                    'content' => $file->get(),
                    'name' => str($field)->headline().' - '.$file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                ];
            })
            ->values()
            ->all();

        Mail::to(config('notifications.info_email'))->send(new AdminNotificationMail(
            heading: 'New Visa Application Inquiry',
            lines: array_filter([
                'Name' => trim("{$data['first_name']} {$data['middle_name']} {$data['last_name']}"),
                'Date of Birth' => $data['date_of_birth'],
                'Gender' => $data['gender'],
                'Nationality' => $data['nationality'],
                'Country of Residence' => $data['country_of_residence'],
                'Passport Number' => $data['passport_number'],
                'Passport Expiry' => $data['passport_expiry'],
                'Phone' => $data['phone'],
                'Email' => $data['email'],
                'Destination Country' => $data['destination_country'],
                'Purpose of Travel' => $data['purpose_of_travel'],
                'Visa Type' => $data['visa_type'],
                'Expected Travel Date' => $data['expected_travel_date'] ?? null,
                'Duration of Stay' => $data['duration_of_stay'] ?? null,
                'Previously Applied for This Visa' => $data['previously_applied'] ?? null,
                'Previously Refused a Visa' => $data['previously_refused'] ?? null,
                'Refusal Explanation' => $data['refusal_explanation'] ?? null,
                'Travelled Internationally Before' => $data['travelled_internationally'] ?? null,
                'Countries Visited' => $data['countries_visited'] ?? null,
                'Additional Info' => $data['additional_info'] ?? null,
                'Documents Attached' => count($attachments).' file(s) — see attachments',
            ]),
            fileAttachments: $attachments,
        ));

        return redirect()->route('public.visa-application-form')->with('success', true);
    }
}
