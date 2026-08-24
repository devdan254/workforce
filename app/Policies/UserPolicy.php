<?php

namespace App\Policies;

use App\Models\User;

/**
 * Governs Admin's ability to manage STUDENT records specifically (User rows
 * with the student role). Stage 1 has no staff-managing-staff UI, so this
 * policy's scope is deliberately narrow — every method maps to the
 * `students.*` permissions seeded in RolesAndPermissionsSeeder.
 *
 * "Suspend" is authorized via students.update, not a separate permission —
 * it's just toggling is_active, not a destructive action. students.delete
 * exists in the permission set but nothing in Stage 1's UI calls it yet;
 * the spec is explicit about not building casual destructive-delete flows.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('students.view');
    }

    public function view(User $user, User $student): bool
    {
        return $user->can('students.view');
    }

    public function create(User $user): bool
    {
        return $user->can('students.create');
    }

    public function update(User $user, User $student): bool
    {
        return $user->can('students.update');
    }

    /**
     * Editing Personal/Academic/Passport info — deliberately broader than
     * update() above. The user's own framing for this feature: every
     * appointed role counts as "admin" for day-to-day student management,
     * not just Admin Officer/Super Admin. update() stays restricted because
     * it covers account-level changes (name/email/suspend); editing a
     * student's profile details is lower-stakes and something any staff
     * member working with that student should be able to do — students.view
     * is the right bar, since every role already has it.
     */
    public function editProfile(User $user, User $student): bool
    {
        return $user->can('students.view') && ! $user->isStudent();
    }

    public function suspend(User $user, User $student): bool
    {
        return $user->can('students.update');
    }

    /* ---------- Job Seeker equivalents ----------
     * Same User model, different permission prefix — can't reuse
     * viewAny/create/update above since those are hardcoded to students.*.
     * Laravel Policies support arbitrary ability names beyond the CRUD
     * conventions, so these are just as legitimate as the Student ones.
     */

    public function viewAnyJobSeekers(User $user): bool
    {
        return $user->can('job_seekers.view');
    }

    public function viewJobSeeker(User $user, User $jobSeeker): bool
    {
        return $user->can('job_seekers.view');
    }

    public function createJobSeeker(User $user): bool
    {
        return $user->can('job_seekers.create');
    }

    public function updateJobSeeker(User $user, User $jobSeeker): bool
    {
        return $user->can('job_seekers.update');
    }

    /**
     * Same reasoning as editProfile() above — any staff member working
     * with a job seeker should be able to edit their profile details,
     * not just Admin Officer/HR-Outsourcing Officer.
     */
    public function editJobSeekerProfile(User $user, User $jobSeeker): bool
    {
        return $user->can('job_seekers.view') && $user->isStaff();
    }

    public function suspendJobSeeker(User $user, User $jobSeeker): bool
    {
        return $user->can('job_seekers.update');
    }

    /* ---------- Employer equivalents ---------- */

    public function viewAnyEmployers(User $user): bool
    {
        return $user->can('employers.view');
    }

    public function viewEmployer(User $user, User $employer): bool
    {
        return $user->can('employers.view');
    }

    public function createEmployer(User $user): bool
    {
        return $user->can('employers.create');
    }

    public function updateEmployer(User $user, User $employer): bool
    {
        return $user->can('employers.update');
    }

    /**
     * Same reasoning as editProfile()/editJobSeekerProfile() above — any
     * staff member working with an employer should be able to edit their
     * company profile details, not just Admin Officer.
     */
    public function editEmployerProfile(User $user, User $employer): bool
    {
        return $user->can('employers.view') && $user->isStaff();
    }

    public function suspendEmployer(User $user, User $employer): bool
    {
        return $user->can('employers.update');
    }

    /* ---------- All Users (Admin Dashboard) ----------
     * The unified cross-role page reuses every ability above for Student/
     * Job Seeker/Employer rows unchanged — same authorization boundary as
     * their existing Workspace pages, nothing new granted there. These
     * exist only for the one genuinely new case: a staff/admin account
     * managing ANOTHER staff/admin account, which nothing previously
     * covered. deleteAny() additionally hard-blocks super_admin
     * specifically, regardless of who's asking — including another
     * super_admin — so the last one can never be removed by mistake.
     */

    public function viewAnyUsers(User $user): bool
    {
        return $user->can('students.view') || $user->can('job_seekers.view')
            || $user->can('employers.view') || $user->can('users.manage');
    }

    public function updateStaff(User $user, User $staff): bool
    {
        return $user->can('users.manage');
    }

    public function suspendStaff(User $user, User $staff): bool
    {
        return $user->can('users.manage');
    }

    public function deleteAny(User $user, User $target): bool
    {
        if ($target->hasRole('super_admin')) {
            return false;
        }

        return match (true) {
            $target->isStudent() => $user->can('students.delete'),
            $target->isJobSeeker() => $user->can('job_seekers.delete'),
            $target->isEmployer() => $user->can('employers.delete'),
            default => $user->can('users.manage'),
        };
    }
}
