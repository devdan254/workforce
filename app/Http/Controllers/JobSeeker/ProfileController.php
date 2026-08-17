<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobSeeker\UpdateJobSeekerProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $profile = $user->jobSeekerProfile;

        return view('job-seeker.profile.edit', [
            'user' => $user,
            'profile' => $profile,
            'experiences' => $profile?->experiences ?? collect(),
            'educations' => $profile?->educations ?? collect(),
        ]);
    }

    public function update(UpdateJobSeekerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->filled('phone')) {
            $user->update(['phone' => $request->string('phone')]);
        }

        $toArray = fn (?string $text) => $text ? array_filter(array_map('trim', explode(',', $text))) : null;

        $profile = $user->jobSeekerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'date_of_birth' => $request->input('date_of_birth'),
                'gender' => $request->input('gender'),
                'nationality' => $request->input('nationality'),
                'address_line' => $request->input('address_line'),
                'city' => $request->input('city'),
                'country' => $request->input('country'),
                'professional_title' => $request->input('professional_title'),
                'industry' => $request->input('industry'),
                'skills' => $request->input('skills'),
                'languages' => $request->input('languages'),
                'certifications' => $request->input('certifications'),
                'passport_number' => $request->input('passport_number'),
                'passport_issue_date' => $request->input('passport_issue_date'),
                'passport_expiry_date' => $request->input('passport_expiry_date'),
                'passport_country' => $request->input('passport_country'),
                'preferred_countries' => $toArray($request->input('preferred_countries_text')),
                'preferred_industries' => $toArray($request->input('preferred_industries_text')),
                'preferred_positions' => $toArray($request->input('preferred_positions_text')),
                'preferred_employment_type' => $request->input('preferred_employment_type'),
                'expected_salary' => $request->input('expected_salary'),
                'expected_salary_currency' => $request->input('expected_salary_currency'),
                'earliest_availability' => $request->input('earliest_availability'),
                'willing_to_relocate' => $request->boolean('willing_to_relocate'),
                'worked_abroad_before' => $request->boolean('worked_abroad_before'),
            ]
        );

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Profile updated.');
    }

    public function storeExperience(Request $request): RedirectResponse
    {
        $request->validate([
            'occupation' => ['required', 'string', 'max:150'],
            'employer' => ['nullable', 'string', 'max:150'],
            'years_of_experience' => ['nullable', 'numeric', 'min:0', 'max:60'],
        ]);

        $profile = $request->user()->jobSeekerProfile ?? $request->user()->jobSeekerProfile()->create([]);

        $profile->experiences()->create([
            'occupation' => $request->string('occupation'),
            'employer' => $request->input('employer'),
            'years_of_experience' => $request->input('years_of_experience'),
            'sort_order' => $profile->experiences()->count(),
        ]);

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Experience added.');
    }

    public function destroyExperience(Request $request, \App\Models\JobExperience $experience): RedirectResponse
    {
        abort_unless($experience->profile->user_id === $request->user()->id, 404);

        $experience->delete();

        return back()->with('success', 'Experience removed.');
    }

    public function storeEducation(Request $request): RedirectResponse
    {
        $request->validate([
            'level' => ['required', 'in:primary,secondary,diploma,bachelor,master,not_applicable'],
            'institution' => ['nullable', 'string', 'max:150'],
            'course' => ['nullable', 'string', 'max:150'],
            'graduation_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 10)],
        ]);

        $profile = $request->user()->jobSeekerProfile ?? $request->user()->jobSeekerProfile()->create([]);

        $profile->educations()->create([
            'level' => $request->string('level'),
            'institution' => $request->input('institution'),
            'course' => $request->input('course'),
            'graduation_year' => $request->input('graduation_year'),
            'sort_order' => $profile->educations()->count(),
        ]);

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Education added.');
    }

    public function destroyEducation(Request $request, \App\Models\JobEducation $education): RedirectResponse
    {
        abort_unless($education->profile->user_id === $request->user()->id, 404);

        $education->delete();

        return back()->with('success', 'Education removed.');
    }
}
