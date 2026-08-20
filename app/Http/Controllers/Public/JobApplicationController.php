<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Deliberately NOT a rebuild of the full 7-step application wizard — that
 * already exists and works (JobSeeker\ApplicationWizardController, session-
 * based multi-step, CV parsing, per-slot document uploads). Duplicating it
 * here would mean maintaining the exact same logic in two places forever.
 *
 * This controller does exactly one thing: get a guest authenticated (either
 * by quick account creation, or by sending them through login if they
 * already have an account), then hand off to that existing wizard. The
 * "first-time applicant creates an account / returning applicant doesn't"
 * requirement is satisfied by this handoff, not by re-implementing the
 * wizard's own profile-creation logic a second time.
 */
class JobApplicationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $jobId = $request->integer('job') ?: null;
        $job = $jobId ? JobPosting::find($jobId) : null;

        if (Auth::check()) {
            $user = $request->user();

            if ($user->isJobSeeker()) {
                return $job
                    ? redirect()->route('job-seeker.jobs.apply', $job)
                    : redirect()->route('job-seeker.dashboard');
            }

            // Signed in as Student/Employer/Staff — a job application isn't
            // the right action for this account type. Explained plainly
            // rather than a confusing redirect or a silent failure.
            return view('public.job-application.wrong-account-type', ['accountType' => match (true) {
                $user->isStudent() => 'Student',
                $user->isEmployer() => 'Employer',
                default => 'Staff',
            }]);
        }

        return view('public.job-application.create', ['job' => $job]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $jobId = $request->integer('job') ?: null;

        // An account with this email already exists — never silently attach
        // a new application to someone else's account just because they
        // typed a matching email; that would let anyone apply "as" another
        // person. Send them to log in instead, and preserve exactly where
        // they were headed so redirect()->intended() lands them right back
        // in the wizard once they're authenticated.
        if (User::where('email', $request->input('email'))->exists()) {
            if ($jobId) {
                session(['url.intended' => route('job-seeker.jobs.apply', $jobId)]);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'An account already exists with this email. Please log in to continue your application.',
            ])->withInput($request->only('email'));
        }

        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->string('password')),
            'is_active' => true,
        ]);
        $user->assignRole('job_seeker');

        event(new Registered($user));

        Auth::login($user);

        return $jobId
            ? redirect()->route('job-seeker.jobs.apply', $jobId)
            : redirect()->route('public.job-application-form.talent-pool');
    }

    /**
     * The gap this fixes: "Join Talent Pool" used to create a bare account
     * (name/email/phone only) and drop the person straight on an empty
     * dashboard — nothing for Admin to follow up on. Same JobSeekerProfile
     * fields the full wizard eventually collects, same updateOrCreate
     * pattern ApplicationWizardController::submit() already uses — just
     * without the job-specific pieces (CV, passport, per-job documents)
     * that only make sense once someone's applying to something specific.
     */
    public function talentPoolProfile(Request $request): View|RedirectResponse
    {
        if (! Auth::check() || ! $request->user()->isJobSeeker()) {
            return redirect()->route('public.job-application-form');
        }

        return view('public.job-application.talent-pool-profile', [
            'profile' => $request->user()->jobSeekerProfile,
        ]);
    }

    public function storeTalentPoolProfile(Request $request): RedirectResponse
    {
        abort_unless(Auth::check() && $request->user()->isJobSeeker(), 403);

        $request->validate([
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'professional_title' => ['nullable', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:100'],
            'preferred_countries' => ['nullable', 'string'],
            'preferred_industries' => ['nullable', 'string'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'expected_salary_currency' => ['nullable', 'string', 'size:3'],
            'earliest_availability' => ['nullable', 'date'],
            'worked_abroad_before' => ['nullable', 'boolean'],
            'willing_to_relocate' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        // Comma-separated input split into the array these fields are cast
        // to — same "free text in, list out" shape used elsewhere in this
        // app (StudyPosting::coursesList(), JobPosting's responsibilities/
        // requirements/skills/benefits) rather than a multi-select widget.
        $splitList = fn (?string $value) => $value
            ? collect(explode(',', $value))->map(fn ($v) => trim($v))->filter()->values()->all()
            : null;

        $profile = $user->jobSeekerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'date_of_birth' => $request->input('date_of_birth'),
                'gender' => $request->input('gender'),
                'nationality' => $request->input('nationality'),
                'country' => $request->input('country'),
                'city' => $request->input('city'),
                'professional_title' => $request->input('professional_title'),
                'industry' => $request->input('industry'),
                'preferred_countries' => $splitList($request->input('preferred_countries')),
                'preferred_industries' => $splitList($request->input('preferred_industries')),
                'expected_salary' => $request->input('expected_salary'),
                'expected_salary_currency' => $request->input('expected_salary_currency'),
                'earliest_availability' => $request->input('earliest_availability'),
                'worked_abroad_before' => $request->boolean('worked_abroad_before'),
                'willing_to_relocate' => $request->boolean('willing_to_relocate'),
            ]
        );
        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return redirect()->route('job-seeker.dashboard')
            ->with('success', 'Thanks! Your profile is saved — we\'ll reach out when a matching opportunity comes up.');
    }
}
