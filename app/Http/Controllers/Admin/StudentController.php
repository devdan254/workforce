<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Invoice;
use App\Models\Status;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Table: Student | Country | Application | Status | Assigned Officer |
     * Documents | Balance | Last Activity | Actions — matches the spec's
     * example exactly. A student CAN have multiple applications; this list
     * shows their most recently active one as a summary, same pattern as
     * the Student Dashboard's "primary application."
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::role('student')->with([
            'studentProfile',
            'studyApplications' => fn ($q) => $q->with(['university', 'currentStatus', 'assignedOfficer'])->latest('submitted_at'),
        ]);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        if ($request->filled('country')) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('country', $request->string('country')));
        }

        if ($request->filled('status_id')) {
            $query->whereHas('studyApplications', fn ($q) => $q->where('status_id', $request->integer('status_id')));
        }

        if ($request->filled('assigned_officer_id')) {
            $query->whereHas('studyApplications', fn ($q) => $q->where('assigned_officer_id', $request->integer('assigned_officer_id')));
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        // Documents + balance are computed per-row rather than eager-loaded relations,
        // same reasoning as the Student Dashboard: these are summaries, not stored data.
        $students->getCollection()->transform(function (User $student) {
            $student->document_totals = [
                'completed' => $student->documents()->whereIn('status', ['uploaded', 'under_review', 'verified'])->count(),
                'total' => $student->documents()->count(),
            ];
            $student->financial_summary = Invoice::financialSummaryForStudent($student->id);

            return $student;
        });

        return view('admin.students.index', [
            'students' => $students,
            'countries' => StudentProfile::query()->whereNotNull('country')->distinct()->orderBy('country')->pluck('country'),
            'applicationStatuses' => Status::where('type', 'application')->orderBy('sort_order')->get(),
            'officers' => User::whereNot('id', $request->user()->id)
                ->whereHas('roles', fn ($q) => $q->whereNotIn('name', ['student']))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.students.create');
    }

    /**
     * Admin enrolling a student manually (e.g. from an offline/walk-in application) —
     * generates a random password and, in a real deployment, this is where you'd
     * dispatch a "set your password" notification. Stage 1 has no mailer configured
     * yet, so the temporary password is shown once on the confirmation screen instead.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $temporaryPassword = Str::password(12);

        $student = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'phone' => $request->string('phone'),
            'password' => Hash::make($temporaryPassword),
            'is_active' => true,
            'email_verified_at' => now(), // admin-created accounts are pre-verified
        ]);
        $student->assignRole('student');

        if ($request->filled('country')) {
            $student->studentProfile()->create(['country' => $request->string('country')]);
        }

        // Redirects straight into the workspace, not back to the list — Admin
        // routinely does the full onboarding right after creating the account
        // (personal info, education, experience, documents), not just the
        // account itself. See the Onboarding entry point for the guided flow.
        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', "Student created. Temporary password: {$temporaryPassword} (shown once — share it securely).");
    }

    public function edit(User $student): View
    {
        $this->authorize('update', $student);

        return view('admin.students.edit', ['student' => $student]);
    }

    public function update(UpdateStudentRequest $request, User $student): RedirectResponse
    {
        $student->update($request->validated());

        return redirect()->route('admin.students.index')->with('success', 'Student updated.');
    }

    /**
     * Toggles is_active — EnsureUserIsActive middleware force-logs-out the
     * student on their very next request, per the spec's requirement that
     * suspension isn't just a UI state but actually locks the account.
     */
    public function suspend(User $student): RedirectResponse
    {
        $this->authorize('suspend', $student);

        $student->update(['is_active' => ! $student->is_active]);

        $action = $student->is_active ? 'reactivated' : 'suspended';

        return back()->with('success', "{$student->name} has been {$action}.");
    }
}
