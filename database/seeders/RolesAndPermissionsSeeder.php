<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'students.view', 'students.create', 'students.update', 'students.delete',

            'applications.view', 'applications.create', 'applications.update', 'applications.change_status',

            'documents.view', 'documents.upload', 'documents.verify', 'documents.reject', 'documents.delete',

            'payments.view', 'payments.create', 'payments.update', 'payments.confirm', 'payments.refund',

            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.send',

            'visa.view', 'visa.create', 'visa.update', 'visa.convert',

            'appointments.view', 'appointments.create', 'appointments.update',

            'tickets.view', 'tickets.respond', 'tickets.close',

            'tasks.view', 'tasks.create', 'tasks.update',

            'resources.manage',

            'activity.view',

            // ---- Stage 2: Job Seeker ----
            'job_seekers.view', 'job_seekers.create', 'job_seekers.update', 'job_seekers.delete',
            'job_applications.view', 'job_applications.create', 'job_applications.update', 'job_applications.change_status',
            'job_postings.view', 'job_postings.create', 'job_postings.update', 'job_postings.publish',
            // documents.*, payments.*, invoices.*, appointments.*, tasks.* are ALREADY reused
            // unmodified for Job Seekers — no job_seekers-scoped variants of these exist,
            // matching the spec's explicit "do not create a second document/payment/etc system."

            // ---- Stage 3: Employer ----
            'employers.view', 'employers.create', 'employers.update', 'employers.delete',
            'worker_requests.view', 'worker_requests.create', 'worker_requests.update', 'worker_requests.review',
            // documents.*/payments.*/invoices.*/appointments.*/tickets.*/tasks.* reused unmodified
            // again — same reasoning as Job Seeker.

            // These two are DELIBERATELY not assigned to the 'employer' role below —
            // per the spec, an employer has full baseline portal access automatically;
            // Admin grants these two INDIVIDUALLY, per employer account, via Spatie's
            // direct permission assignment (EmployerWorkspaceController::updatePermissions).
            // Two different employers can have different grants — that's the whole point.
            'employer_interviews.conduct',
            'employer_documents.view_candidate',

            // ---- Study Postings (Student-side equivalent of Job Postings) ----
            'study_postings.view', 'study_postings.create', 'study_postings.update', 'study_postings.publish',

            // ---- All Users (Admin Dashboard) — managing STAFF accounts
            // specifically. Student/Job Seeker/Employer rows on that same
            // page reuse their existing students.*/job_seekers.*/
            // employers.* permissions above, unchanged — this one exists
            // only because nothing previously covered one staff member
            // editing/suspending/deleting ANOTHER staff account. Master
            // list only (super_admin gets it automatically via
            // Permission::all() below); deliberately not granted to
            // admin_officer or any other role — managing colleagues'
            // accounts is a higher-privilege action than managing
            // Students/Job Seekers/Employers.
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ---- Roles ----
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all()); // Super Admin: everything, always.

        $adminOfficer = Role::firstOrCreate(['name' => 'admin_officer', 'guard_name' => 'web']);
        $adminOfficer->syncPermissions([
            'students.view', 'students.create', 'students.update',
            'applications.view', 'applications.create', 'applications.update', 'applications.change_status',
            'documents.view', 'documents.upload', 'documents.verify', 'documents.reject', 'documents.delete',
            'invoices.view', 'invoices.create',
            'visa.view', 'visa.create', 'visa.update', 'visa.convert',
            'appointments.view', 'appointments.create', 'appointments.update',
            'tickets.view', 'tickets.respond', 'tickets.close',
            'tasks.view', 'tasks.create', 'tasks.update',
            'resources.manage', 'activity.view',
            // Stage 2 — Admin Officer manages Job Seekers with the same breadth as Students.
            'job_seekers.view', 'job_seekers.create', 'job_seekers.update',
            'job_applications.view', 'job_applications.create', 'job_applications.update', 'job_applications.change_status',
            'job_postings.view', 'job_postings.create', 'job_postings.update', 'job_postings.publish',
            // Stage 3 — Admin Officer manages Employers with the same breadth.
            'employers.view', 'employers.create', 'employers.update',
            'worker_requests.view', 'worker_requests.create', 'worker_requests.update', 'worker_requests.review',
            'study_postings.view', 'study_postings.create', 'study_postings.update', 'study_postings.publish',
        ]);

        $educationOfficer = Role::firstOrCreate(['name' => 'education_officer', 'guard_name' => 'web']);
        $educationOfficer->syncPermissions([
            'students.view',
            'applications.view', 'applications.update', 'applications.change_status',
            'documents.view', 'documents.upload', 'documents.verify', 'documents.reject',
            'appointments.view', 'appointments.create', 'appointments.update',
            'tasks.view', 'tasks.update',
            'study_postings.view', 'study_postings.create', 'study_postings.update', 'study_postings.publish',
        ]);

        // Finance Officer: deliberately does NOT get applications.change_status —
        // matches the spec's explicit example of a permission boundary.
        $financeOfficer = Role::firstOrCreate(['name' => 'finance_officer', 'guard_name' => 'web']);
        $financeOfficer->syncPermissions([
            'students.view',
            'payments.view', 'payments.create', 'payments.update', 'payments.confirm', 'payments.refund',
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.send',
            'tasks.view', 'tasks.update',
        ]);

        $visaOfficer = Role::firstOrCreate(['name' => 'visa_officer', 'guard_name' => 'web']);
        $visaOfficer->syncPermissions([
            'students.view',
            'visa.view', 'visa.create', 'visa.update', 'visa.convert',
            'documents.view', 'documents.upload', 'documents.verify', 'documents.reject',
            'appointments.view', 'appointments.create', 'appointments.update',
            'tasks.view', 'tasks.update',
        ]);

        $supportOfficer = Role::firstOrCreate(['name' => 'support_officer', 'guard_name' => 'web']);
        $supportOfficer->syncPermissions([
            'students.view',
            'tickets.view', 'tickets.respond', 'tickets.close',
            'tasks.view', 'tasks.update',
        ]);

        // Sales Officer: deliberately does NOT get payments/invoices —
        // matches the spec's explicit example of a permission boundary.
        $salesOfficer = Role::firstOrCreate(['name' => 'sales_officer', 'guard_name' => 'web']);
        $salesOfficer->syncPermissions([
            'students.view', 'students.create',
            'applications.view', 'applications.create',
            'appointments.view', 'appointments.create',
            'tasks.view',
        ]);

        // ---- Stage 2 roles ----

        // Recruitment Officer: the Job Seeker equivalent of Education Officer —
        // manages the candidate pipeline (applications, documents, interviews)
        // but not finance. Reuses the SAME documents.*/appointments.*/tasks.*
        // permissions Education Officer already uses — no job-seeker-scoped
        // duplicates, per the spec's explicit reuse mandate.
        $recruitmentOfficer = Role::firstOrCreate(['name' => 'recruitment_officer', 'guard_name' => 'web']);
        $recruitmentOfficer->syncPermissions([
            'job_seekers.view',
            'job_applications.view', 'job_applications.update', 'job_applications.change_status',
            'job_postings.view', 'job_postings.create', 'job_postings.update', 'job_postings.publish',
            'documents.view', 'documents.upload', 'documents.verify', 'documents.reject',
            'appointments.view', 'appointments.create', 'appointments.update',
            'tickets.view', 'tickets.respond',
            'tasks.view', 'tasks.update',
        ]);

        // HR/Outsourcing Officer: broader Job Seeker oversight, including
        // finance — the Job Seeker equivalent of Admin Officer's breadth,
        // scoped to job seekers rather than students.
        $hrOutsourcingOfficer = Role::firstOrCreate(['name' => 'hr_outsourcing_officer', 'guard_name' => 'web']);
        $hrOutsourcingOfficer->syncPermissions([
            'job_seekers.view', 'job_seekers.create', 'job_seekers.update',
            'job_applications.view', 'job_applications.create', 'job_applications.update', 'job_applications.change_status',
            'job_postings.view', 'job_postings.create', 'job_postings.update', 'job_postings.publish',
            'documents.view', 'documents.upload', 'documents.verify', 'documents.reject',
            'payments.view', 'payments.create', 'payments.confirm',
            'invoices.view', 'invoices.create', 'invoices.send',
            'appointments.view', 'appointments.create', 'appointments.update',
            'tickets.view', 'tickets.respond', 'tickets.close',
            'tasks.view', 'tasks.create', 'tasks.update',
        ]);

        // Student: no spatie permissions at all — every student capability is
        // ownership-based ("is this my record?"), enforced entirely in the Policies.
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Job Seeker: same pattern as Student — zero spatie permissions,
        // every capability is ownership-based via student_id === $user->id
        // (see the Policy updates: DocumentPolicy, PaymentPolicy, InvoicePolicy,
        // AppointmentPolicy, SupportTicketPolicy all check isJobSeeker() now).
        Role::firstOrCreate(['name' => 'job_seeker', 'guard_name' => 'web']);

        // Employer: same pattern as Student/Job Seeker — zero role-level spatie
        // permissions. Ownership-based access to their own documents/payments/
        // invoices/appointments/tickets works identically (Policies already
        // check isEmployer() alongside isStudent()/isJobSeeker() — see the
        // Policy updates in this same delivery). Per-employer permission
        // VARIATION (some employers can approve candidates, others can't) is
        // granted directly to individual employer User accounts via Spatie's
        // direct permission assignment, NOT via this role — that's a later
        // phase once Candidates/Documents exist to gate.
        Role::firstOrCreate(['name' => 'employer', 'guard_name' => 'web']);

        // Stage 6 — Visa Management. Same zero-role-permissions pattern as
        // student/job_seeker/employer: a guest visa-only applicant (no
        // existing Student/Job Seeker account) still needs a REAL User row
        // for Documents/Payments/Invoices to attach to via their existing
        // student_id-style FKs — this role marks that row as "not yet a
        // Student or Job Seeker," nothing more. No portal exists for it;
        // it's purely an anchor point Admin manages until "Convert
        // Applicant" assigns a real category.
        Role::firstOrCreate(['name' => 'visa_applicant', 'guard_name' => 'web']);
    }
}
