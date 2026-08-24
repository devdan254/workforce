<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\StatusTransition;
use App\Models\User;
use App\Models\VisaApplication;
use App\Notifications\AdminAlertNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Stage 3 of the incremental Visa Management build — the wizard now
 * creates a REAL VisaApplication (standalone/guest path), not just an
 * email. Reuses the exact same guest-User-creation shape and initial-
 * status lookup Admin\VisaManagementController::store() already uses, so
 * a visa application looks identical in Visa Management regardless of
 * whether Admin or a public visitor created it — one system, not two.
 *
 * Documents stay email-attachment-only for now — real Document
 * persistence is explicitly Stage 5 of this build, not this one. Nothing
 * here pretends otherwise; the files are still delivered (as attachments),
 * just not yet wired into the Document system Admin will eventually
 * review them through.
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

        // Same "never silently attach to someone else's identity" rule as
        // Jobs/Study/Hire — but visa_applicant has no portal to redirect
        // to and back from (no login-then-resume flow exists yet, since
        // Workspace integration is a later stage), so this stays a plain
        // validation failure with a clear next step, not a login redirect.
        if (User::where('email', $data['email'])->exists()) {
            return back()->withErrors([
                'email' => 'An account already exists with this email. Please contact us directly so we can attach this application to your existing record.',
            ])->withInput($request->except(array_keys($request->allFiles())));
        }

        // Same guest-User shape as Admin\VisaManagementController::store()'s
        // standalone branch — a random password, since visa_applicant has
        // no portal to log into with one.
        $guest = User::create([
            'name' => trim("{$data['first_name']} {$data['last_name']}"),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make(Str::random(32)),
            'is_active' => true,
        ]);
        $guest->assignRole('visa_applicant');

        $initialStatusId = StatusTransition::where('status_type', 'visa')->whereNull('from_status_id')->value('to_status_id');

        $visaApplication = VisaApplication::create([
            'user_id' => $guest->id,
            'status_id' => $initialStatusId,

            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'nationality' => $data['nationality'],
            'country_of_residence' => $data['country_of_residence'],
            'passport_number' => $data['passport_number'],
            'passport_expiry' => $data['passport_expiry'],

            'destination_country' => $data['destination_country'],
            'purpose_of_travel' => $data['purpose_of_travel'],
            'expected_travel_date' => $data['expected_travel_date'] ?? null,
            'duration_of_stay' => $data['duration_of_stay'] ?? null,
            'has_admission_letter' => $data['has_admission_letter'] ?? null,
            'has_employment_contract' => $data['has_employment_contract'] ?? null,
            'has_invitation_letter' => $data['has_invitation_letter'] ?? null,

            'visa_type' => $data['visa_type'],
            'previously_applied' => $data['previously_applied'] ?? null,
            'previously_refused' => $data['previously_refused'] ?? null,
            'refusal_explanation' => $data['refusal_explanation'] ?? null,
            'travelled_internationally' => $data['travelled_internationally'] ?? null,
            'countries_visited' => $data['countries_visited'] ?? null,
            'additional_info' => $data['additional_info'] ?? null,
        ]);

        // Stage 5 — every uploaded file becomes a REAL Document now, not
        // just an email attachment. Vault-level (student_id = guest's own
        // account, no application scope — matches VisaApplication::
        // documentsQuery()'s "guest" branch exactly), status 'under_review'
        // since it's genuinely already uploaded, not merely requested.
        $visaCategoryId = DocumentCategory::where('name', 'Visa Documents')->value('id');

        $fileFields = [
            'passport' => 'Passport',
            'passport_photo' => 'Passport Photo',
            'admission_letter' => 'Admission Letter',
            'employment_contract' => 'Employment Contract',
            'invitation_letter' => 'Invitation Letter',
            'bank_statement' => 'Bank Statement',
            'academic_certificates' => 'Academic Certificates',
            'additional_documents' => 'Additional Documents',
        ];

        foreach ($fileFields as $field => $label) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $path = $file->store("documents/{$guest->id}", 'public');

            $document = Document::create([
                'student_id' => $guest->id,
                'document_category_id' => $visaCategoryId,
                'name' => $label,
                'status' => 'required',
            ]);

            app(\App\Services\DocumentVerificationService::class)
                ->markUploaded($document, $path, $file->getClientMimeType(), $file->getSize());
        }

        // Light notification — a real, persisted record now exists for
        // Admin to review properly in Visa Management (documents included,
        // via the link below), so there's no reason to duplicate every
        // field — or the files themselves — into the email body too.
        AdminAlertNotification::sendToAdmins(
            heading: 'New Visa Application Submitted',
            lines: [
                'Applicant' => $guest->name,
                'Destination' => $data['destination_country'],
                'Visa Type' => $data['visa_type'],
            ],
            actionLabel: 'Review Visa Application',
            actionUrl: route('admin.visa-management.show', $visaApplication),
        );

        return redirect()->route('public.visa-application-form')->with('success', true);
    }
}
