<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\UpdateEmployerProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('employer.profile.edit', [
            'employer' => $request->user(),
            'profile' => $request->user()->employerProfile,
        ]);
    }

    public function update(UpdateEmployerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->filled('phone')) {
            $user->update(['phone' => $request->string('phone')]);
        }

        $data = [
            'company_name' => $request->input('company_name'),
            'industry' => $request->input('industry'),
            'country' => $request->input('country'),
            'company_website' => $request->input('company_website'),
            'company_size' => $request->input('company_size'),
            'city' => $request->input('city'),
            'company_description' => $request->input('company_description'),
            'contact_job_title' => $request->input('contact_job_title'),
        ];

        if ($request->hasFile('logo')) {
            $existing = $user->employerProfile?->logo_path;
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            $data['logo_path'] = $request->file('logo')->store('employer-logos', 'public');
        }

        $profile = $user->employerProfile()->updateOrCreate(['user_id' => $user->id], $data);

        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Company profile updated.');
    }
}
