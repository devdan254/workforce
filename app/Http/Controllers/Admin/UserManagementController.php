<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * The one unified, cross-role view of every account in the system —
 * Student, Job Seeker, Employer, and Staff/Admin alike. Deliberately NOT
 * a replacement for the existing per-role Workspace pages (Student
 * Workspace, Job Seeker Workspace, Employer Workspace) — those still own
 * the rich, role-specific detail (documents, applications, payments).
 * This page is the simpler, cross-cutting counterpart, same relationship
 * Payments Management has to the three portal-scoped payment pages: one
 * place to see and manage every account's basic status, without
 * duplicating what already exists for the deep, per-role work.
 *
 * Authorization is genuinely per-target-role, not one blanket permission —
 * a Student row reuses students.update/students.delete exactly as the
 * Student Workspace already does; only a Staff row uses the new
 * users.manage permission, since nothing previously covered one staff
 * member managing another's account.
 */
class UserManagementController extends Controller
{
    /**
     * Deliberately excludes super_admin itself — even the id===1 account
     * shouldn't be able to mint additional super_admins through a simple
     * checkbox form. Creating a second true super_admin is significant
     * enough that it belongs at the database/seeder level, not something
     * this UI casually offers as one option among nine.
     */
    private const ASSIGNABLE_STAFF_ROLES = [
        'admin_officer', 'education_officer', 'finance_officer', 'visa_officer',
        'support_officer', 'sales_officer', 'recruitment_officer', 'hr_outsourcing_officer',
    ];

    public function index(Request $request): View
    {
        $this->authorize('viewAnyUsers', User::class);

        $query = User::with(['roles', 'studentProfile', 'employerProfile']);

        if ($request->filled('role')) {
            if ($request->string('role') === 'staff') {
                $query->whereHas('roles', fn ($q) => $q->whereNotIn('name', ['student', 'job_seeker', 'employer', 'visa_applicant']));
            } else {
                $query->role($request->string('role'));
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status') === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function edit(User $user): View
    {
        $this->authorize($this->editAbilityFor($user), $user);

        return view('admin.users.edit', [
            'targetUser' => $user,
            'assignableRoles' => self::ASSIGNABLE_STAFF_ROLES,
            'canManageRoles' => auth()->id() === 1 && $user->isStaff() && ! $user->hasRole('super_admin'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize($this->editAbilityFor($user), $user);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        // Role reassignment is a genuinely separate concern from the basic
        // account fields above — gated independently here (not just by
        // editAbilityFor()'s updateStaff check) so a stray 'roles' field
        // in a request can never reassign anyone's access unless the
        // actor is actually id === 1, regardless of what else is true.
        if (auth()->id() === 1 && $user->isStaff() && ! $user->hasRole('super_admin')) {
            $selectedRoles = array_intersect($request->input('roles', []), self::ASSIGNABLE_STAFF_ROLES);
            $user->syncRoles($selectedRoles);
        }

        return redirect()->route('admin.users.index')->with('success', "{$user->name}'s account has been updated.");
    }

    public function createStaff(): View
    {
        $this->authorize('manageStaffAccounts', User::class);

        return view('admin.users.create-staff', ['assignableRoles' => self::ASSIGNABLE_STAFF_ROLES]);
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $this->authorize('manageStaffAccounts', User::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'in:'.implode(',', self::ASSIGNABLE_STAFF_ROLES)],
        ]);

        $staff = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
            'is_active' => true,
        ]);

        $staff->assignRole($request->input('roles'));

        return redirect()->route('admin.users.index')->with('success', "{$staff->name}'s staff account has been created.");
    }

    public function toggleActive(User $user): RedirectResponse
    {
        $this->authorize($this->suspendAbilityFor($user), $user);

        $user->update(['is_active' => ! $user->is_active]);

        $action = $user->is_active ? 'reactivated' : 'suspended';

        return back()->with('success', "{$user->name} has been {$action}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('deleteAny', $user);

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "{$name}'s account has been permanently deleted.");
    }

    /**
     * update()/updateJobSeeker()/updateEmployer() specifically — not
     * editProfile()/editJobSeekerProfile()/editEmployerProfile(). Per
     * UserPolicy's own documented distinction, the profile-level ones
     * govern academic/passport/company details on the rich Workspace
     * pages; update() governs account-level changes (name/email/suspend)
     * — exactly what this page's edit form actually changes.
     */
    private function editAbilityFor(User $target): string
    {
        return match (true) {
            $target->isStudent() => 'update',
            $target->isJobSeeker() => 'updateJobSeeker',
            $target->isEmployer() => 'updateEmployer',
            default => 'updateStaff',
        };
    }

    private function suspendAbilityFor(User $target): string
    {
        return match (true) {
            $target->isStudent() => 'suspend',
            $target->isJobSeeker() => 'suspendJobSeeker',
            $target->isEmployer() => 'suspendEmployer',
            default => 'suspendStaff',
        };
    }
}
