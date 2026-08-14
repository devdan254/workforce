<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateStudentProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('student.profile.edit', [
            'user' => $request->user(),
            'profile' => $request->user()->studentProfile,
        ]);
    }

    public function update(UpdateStudentProfileRequest $request): RedirectResponse
    {
        $request->user()->update(['phone' => $request->string('phone')]);

        $request->user()->studentProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->safe()->except('phone')
        );

        // Profile completion recalculates every save — never trust a stale
        // cached percentage sitting on the row from before this update.
        $profile = $request->user()->studentProfile;
        $profile->update(['profile_completion_percent' => $profile->calculateCompletionPercent()]);

        return back()->with('success', 'Profile updated.');
    }
}
