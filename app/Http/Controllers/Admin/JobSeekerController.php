<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobSeekerRequest;
use App\Http\Requests\Admin\UpdateJobSeekerRequest;
use App\Models\Invoice;
use App\Models\JobSeekerProfile;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Deliberately mirrors Admin\StudentController's structure and reasoning
 * almost line-for-line — same list/create/edit/suspend shape, same
 * per-row-computed document/financial summaries, same temporary-password
 * pattern for admin-created accounts. The columns differ (Professional
 * Title instead of a university, Applications instead of a single
 * application) because that's what the spec's Job Seekers list actually
 * asks for, not because the underlying approach needed to change.
 */
class JobSeekerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAnyJobSeekers', User::class);

        $query = User::role('job_seeker')->with([
            'jobSeekerProfile',
            'jobApplications' => fn ($q) => $q->with(['jobPosting', 'currentStatus', 'assignedOfficer'])->latest('applied_at'),
        ]);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        if ($request->filled('country')) {
            $query->whereHas('jobSeekerProfile', fn ($q) => $q->where('country', $request->string('country')));
        }

        if ($request->filled('industry')) {
            $query->whereHas('jobSeekerProfile', fn ($q) => $q->where('industry', 'like', '%'.$request->string('industry').'%'));
        }

        if ($request->filled('status_id')) {
            $query->whereHas('jobApplications', fn ($q) => $q->where('status_id', $request->integer('status_id')));
        }

        if ($request->filled('assigned_officer_id')) {
            $query->whereHas('jobApplications', fn ($q) => $q->where('assigned_officer_id', $request->integer('assigned_officer_id')));
        }

        $jobSeekers = $query->latest()->paginate(15)->withQueryString();

        // Same reasoning as Student's index: computed per-row rather than
        // eager-loaded, since these are summaries, not stored data.
        $jobSeekers->getCollection()->transform(function (User $jobSeeker) {
            $jobSeeker->application_totals = [
                'active' => $jobSeeker->jobApplications()->whereHas('currentStatus', fn ($q) => $q->where('is_terminal', false))->count(),
                'total' => $jobSeeker->jobApplications()->count(),
            ];
            $jobSeeker->document_totals = [
                'completed' => $jobSeeker->documents()->whereIn('status', ['uploaded', 'under_review', 'verified'])->count(),
                'total' => $jobSeeker->documents()->count(),
            ];
            $jobSeeker->financial_summary = Invoice::financialSummaryForPerson($jobSeeker->id);

            return $jobSeeker;
        });

        return view('admin.job-seekers.index', [
            'jobSeekers' => $jobSeekers,
            'countries' => JobSeekerProfile::query()->whereNotNull('country')->distinct()->orderBy('country')->pluck('country'),
            'applicationStatuses' => Status::where('type', 'job_application')->orderBy('sort_order')->get(),
            'officers' => User::whereNot('id', $request->user()->id)
                ->whereHas('roles', fn ($q) => $q->whereNotIn('name', ['student', 'job_seeker']))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('createJobSeeker', User::class);

        return view('admin.job-seekers.create');
    }

    public function store(StoreJobSeekerRequest $request): RedirectResponse
    {
        $temporaryPassword = Str::password(12);

        $jobSeeker = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'phone' => $request->string('phone'),
            'password' => Hash::make($temporaryPassword),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $jobSeeker->assignRole('job_seeker');

        if ($request->filled('country') || $request->filled('professional_title')) {
            $jobSeeker->jobSeekerProfile()->create([
                'country' => $request->input('country'),
                'professional_title' => $request->input('professional_title'),
            ]);
        }

        // Redirects straight into the workspace, not back to the list — same
        // reasoning as Student's own store(): Admin typically continues the
        // full onboarding (profile, education, experience, documents) right
        // after creating the account.
        return redirect()
            ->route('admin.job-seekers.show', $jobSeeker)
            ->with('success', "Job Seeker created. Temporary password: {$temporaryPassword} (shown once — share it securely).");
    }

    public function edit(User $jobSeeker): View
    {
        $this->authorize('updateJobSeeker', $jobSeeker);

        return view('admin.job-seekers.edit', ['jobSeeker' => $jobSeeker]);
    }

    public function update(UpdateJobSeekerRequest $request, User $jobSeeker): RedirectResponse
    {
        $jobSeeker->update($request->validated());

        return redirect()->route('admin.job-seekers.index')->with('success', 'Job Seeker updated.');
    }

    public function suspend(User $jobSeeker): RedirectResponse
    {
        $this->authorize('suspendJobSeeker', $jobSeeker);

        $jobSeeker->update(['is_active' => ! $jobSeeker->is_active]);

        $action = $jobSeeker->is_active ? 'reactivated' : 'suspended';

        return back()->with('success', "{$jobSeeker->name} has been {$action}.");
    }
}
