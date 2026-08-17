<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Overrides Breeze's published default, which creates a User but assigns NO
 * role at all — meaning every self-registered account (Student or Job Seeker)
 * would pass neither isStudent() nor isJobSeeker(), fall through
 * AuthenticatedSessionController's redirect logic to the Admin dashboard,
 * and immediately be blocked by the admin role middleware. This was a real
 * gap that existed since Stage 1 but was never caught because every account
 * used so far was seeded (with a role already assigned), never actually
 * self-registered through this form.
 *
 * ONE route (/register, Breeze's own, untouched), made role-aware via a
 * query string — no new route needed. ?as=job_seeker registers a Job
 * Seeker; anything else (including no query string) registers a Student,
 * preserving the existing default behavior for any link that doesn't
 * specify otherwise.
 */
class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        $role = $request->query('as') === 'job_seeker' ? 'job_seeker' : 'student';

        return view('auth.register', ['role' => $role]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,job_seeker'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole($request->input('role'));

        event(new Registered($user));

        Auth::login($user);

        return redirect($user->isJobSeeker()
            ? route('job-seeker.dashboard', absolute: false)
            : route('student.dashboard', absolute: false)
        );
    }
}