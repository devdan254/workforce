<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

/**
 * A guided entry point, not a duplicate wizard. Per the spec's real-world
 * usage pattern — Admin is who actually onboards most Students and Job
 * Seekers (in person, over WhatsApp, on a call), not the person filling in
 * the web form themselves — this page just routes Admin to the right
 * existing Create form, and account creation now redirects straight into
 * that person's Workspace (see StudentController::store() /
 * JobSeekerController::store()) instead of back to a list. Personal Info,
 * Education, Experience, and Documents are all already editable there;
 * building a second copy of that logic here would be exactly the kind of
 * duplication the whole app has deliberately avoided.
 */
class OnboardingController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.onboarding.index');
    }
}
