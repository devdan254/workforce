<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployerRequest;
use App\Http\Requests\Admin\UpdateEmployerRequest;
use App\Models\EmployerProfile;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Mirrors Admin\JobSeekerController's structure and reasoning almost
 * line-for-line — same list/create/edit/suspend shape, same
 * per-row-computed summaries, same temporary-password pattern for
 * admin-created accounts. Columns differ (Company/Industry/Active Jobs/
 * Applicants/Workers Hired instead of Professional Title/Applications)
 * because that's what the spec's Employers list actually asks for.
 */
class EmployerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAnyEmployers', User::class);

        $query = User::role('employer')->with(['employerProfile.assignedOfficer', 'jobPostings']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            // Wrapped in a single where() closure so this OR group stays
            // correctly scoped against the country/industry/officer filters
            // below — an unwrapped top-level orWhereHas() would otherwise
            // incorrectly widen the whole query, not just the search match.
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('employerProfile', fn ($sub) => $sub->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('country')) {
            $query->whereHas('employerProfile', fn ($q) => $q->where('country', $request->string('country')));
        }

        if ($request->filled('industry')) {
            $query->whereHas('employerProfile', fn ($q) => $q->where('industry', 'like', '%'.$request->string('industry').'%'));
        }

        if ($request->filled('assigned_officer_id')) {
            $query->whereHas('employerProfile', fn ($q) => $q->where('assigned_officer_id', $request->integer('assigned_officer_id')));
        }

        $employers = $query->latest()->paginate(15)->withQueryString();

        // Computed per-row rather than eager-loaded, same reasoning as
        // Student/Job Seeker's index — these are summaries, not stored data.
        $employers->getCollection()->transform(function (User $employer) {
            $jobPostingIds = $employer->jobPostings()->pluck('id');

            $employer->active_jobs_count = $employer->jobPostings()->where('status', 'open')->count();
            $employer->applicants_count = \App\Models\JobApplication::whereIn('job_posting_id', $jobPostingIds)->count();
            $employer->hired_count = \App\Models\JobApplication::whereIn('job_posting_id', $jobPostingIds)
                ->whereHas('currentStatus', fn ($q) => $q->where('slug', 'deployed'))
                ->count();
            $employer->financial_summary = Invoice::financialSummaryForPerson($employer->id);

            return $employer;
        });

        return view('admin.employers.index', [
            'employers' => $employers,
            'countries' => EmployerProfile::query()->distinct()->orderBy('country')->pluck('country'),
            'officers' => User::whereHas('roles', fn ($q) => $q->whereNotIn('name', ['student', 'job_seeker', 'employer']))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('createEmployer', User::class);

        return view('admin.employers.create');
    }

    public function store(StoreEmployerRequest $request): RedirectResponse
    {
        $temporaryPassword = Str::password(12);

        $employer = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'phone' => $request->string('phone'),
            'password' => Hash::make($temporaryPassword),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $employer->assignRole('employer');

        $employer->employerProfile()->create([
            'company_name' => $request->string('company_name'),
            'country' => $request->string('country'),
            'industry' => $request->input('industry'),
        ]);

        // Redirects straight into the workspace, not back to the list —
        // same reasoning as Job Seeker's own store(): Admin typically
        // continues the full onboarding (company profile, worker requests,
        // documents) right after creating the account.
        return redirect()
            ->route('admin.employers.show', $employer)
            ->with('success', "Employer created. Temporary password: {$temporaryPassword} (shown once — share it securely).");
    }

    public function edit(User $employer): View
    {
        $this->authorize('updateEmployer', $employer);

        return view('admin.employers.edit', ['employer' => $employer]);
    }

    public function update(UpdateEmployerRequest $request, User $employer): RedirectResponse
    {
        $employer->update($request->validated());

        return redirect()->route('admin.employers.index')->with('success', 'Employer updated.');
    }

    public function suspend(User $employer): RedirectResponse
    {
        $this->authorize('suspendEmployer', $employer);

        $employer->update(['is_active' => ! $employer->is_active]);

        $action = $employer->is_active ? 'reactivated' : 'suspended';

        return back()->with('success', "{$employer->name} has been {$action}.");
    }
}
