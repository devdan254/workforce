<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\AdminNotificationMail;
use App\Models\User;
use App\Models\WorkerRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Same shape as Public\StudyApplicationController: combines account
 * creation with the actual request into ONE submission for guests (the
 * original hire.html form has no separate account step either), while an
 * already-authenticated Employer just submits the hiring requirements
 * directly. WorkerRequest::create() here mirrors Employer\
 * WorkerRequestController::store() field-for-field — that one requires an
 * authenticated Employer (StoreWorkerRequestRequest::authorize() checks
 * $this->user()), so it can't be reused directly for a guest's first
 * submission; the creation logic is intentionally duplicated here rather
 * than awkwardly forcing a guest through an authenticated-only Form Request.
 */
class HireController extends Controller
{
    public function create(Request $request): View
    {
        if (Auth::check() && ! $request->user()->isEmployer()) {
            return view('public.hire.wrong-account-type', ['accountType' => match (true) {
                $request->user()->isStudent() => 'Student',
                $request->user()->isJobSeeker() => 'Job Seeker',
                default => 'Staff',
            }]);
        }

        return view('public.hire.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $isGuest = ! Auth::check();

        $rules = [
            'company_name' => ['required', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'company_website' => ['nullable', 'string', 'max:255'],
            'company_size' => ['nullable', 'string', 'max:50'],

            'contact_name' => ['required', 'string', 'max:150'],
            'contact_job_title' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:30'],

            'job_title' => ['required', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1'],
            'preferred_experience' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['nullable', Rule::in(['permanent', 'contract', 'temporary', 'seasonal', 'part_time', 'full_time'])],
            'preferred_start_date' => ['nullable', 'date'],
            'work_location' => ['nullable', 'string', 'max:150'],
            'additional_requirements' => ['nullable', 'string', 'max:2000'],
            'declaration' => ['accepted'],
        ];

        if ($isGuest) {
            $rules['contact_email'] = ['required', 'string', 'lowercase', 'email', 'max:255'];
            $rules['password'] = ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()];
        }

        $request->validate($rules);

        if ($isGuest) {
            if (User::where('email', $request->input('contact_email'))->exists()) {
                session(['url.intended' => route('employer.worker-requests.create')]);

                return redirect()->route('login')->withErrors([
                    'contact_email' => 'An account already exists with this email. Please log in, then submit your request from your dashboard.',
                ])->withInput($request->except('password', 'password_confirmation'));
            }

            $employer = User::create([
                'name' => $request->string('contact_name'),
                'email' => $request->string('contact_email'),
                'phone' => $request->string('contact_phone'),
                'password' => Hash::make($request->string('password')),
                'is_active' => true,
            ]);
            $employer->assignRole('employer');
            event(new Registered($employer));
            Auth::login($employer);

            $employer->employerProfile()->create([
                'company_name' => $request->string('company_name'),
                'industry' => $request->input('industry'),
                'country' => $request->string('country'),
                'company_website' => $request->input('company_website'),
                'company_size' => $request->input('company_size'),
                'contact_job_title' => $request->input('contact_job_title'),
            ]);
        } else {
            $employer = $request->user();
            $employer->update(['phone' => $request->string('contact_phone')]);
            $employer->employerProfile()->updateOrCreate(
                ['user_id' => $employer->id],
                [
                    'company_name' => $request->string('company_name'),
                    'industry' => $request->input('industry'),
                    'country' => $request->string('country'),
                    'company_website' => $request->input('company_website'),
                    'company_size' => $request->input('company_size'),
                    'contact_job_title' => $request->input('contact_job_title'),
                ]
            );
        }

        $workerRequest = WorkerRequest::create([
            'employer_id' => $employer->id,
            'contact_name' => $request->string('contact_name'),
            'contact_job_title' => $request->input('contact_job_title'),
            'contact_email' => $employer->email,
            'contact_phone' => $request->string('contact_phone'),
            'job_title' => $request->string('job_title'),
            'quantity' => $request->integer('quantity'),
            'preferred_experience' => $request->input('preferred_experience'),
            'employment_type' => $request->input('employment_type') ?: 'permanent',
            'preferred_start_date' => $request->input('preferred_start_date'),
            'work_location' => $request->input('work_location'),
            'additional_requirements' => $request->input('additional_requirements'),
            'status' => 'submitted',
        ]);

        // "Notify admin of its application, not necessarily the details" —
        // deliberately light, unlike Job/Study Application's full field
        // dump. WorkerRequest is a real, persisted record Admin already
        // reviews in the Worker Requests queue; the email is just the
        // heads-up + a direct link, not a duplicate of the data itself.
        Mail::to(config('notifications.admin_email'))->send(new AdminNotificationMail(
            heading: 'New Worker Request Submitted',
            lines: [
                'Company' => $employer->employerProfile?->company_name ?? $request->string('company_name'),
                'Submitted By' => $employer->name,
            ],
            actionLabel: 'Review Worker Request',
            actionUrl: route('admin.worker-requests.show', $workerRequest),
        ));

        return redirect()->route('employer.worker-requests.show', $workerRequest)
            ->with('success', 'Your request has been submitted. A recruitment specialist will contact you within 1–2 business days.');
    }
}
