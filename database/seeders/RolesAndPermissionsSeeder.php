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

            'documents.view', 'documents.upload', 'documents.verify', 'documents.reject',

            'payments.view', 'payments.create', 'payments.confirm', 'payments.refund',

            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.send',

            'visa.view', 'visa.update',

            'appointments.view', 'appointments.create', 'appointments.update',

            'tickets.view', 'tickets.respond', 'tickets.close',

            'tasks.view', 'tasks.create', 'tasks.update',

            'resources.manage',

            'activity.view',
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
            'documents.view', 'documents.verify', 'documents.reject',
            'invoices.view', 'invoices.create',
            'visa.view', 'visa.update',
            'appointments.view', 'appointments.create', 'appointments.update',
            'tickets.view', 'tickets.respond', 'tickets.close',
            'tasks.view', 'tasks.create', 'tasks.update',
            'resources.manage', 'activity.view',
        ]);

        $educationOfficer = Role::firstOrCreate(['name' => 'education_officer', 'guard_name' => 'web']);
        $educationOfficer->syncPermissions([
            'students.view',
            'applications.view', 'applications.update', 'applications.change_status',
            'documents.view', 'documents.verify', 'documents.reject',
            'appointments.view', 'appointments.create', 'appointments.update',
            'tasks.view', 'tasks.update',
        ]);

        // Finance Officer: deliberately does NOT get applications.change_status —
        // matches the spec's explicit example of a permission boundary.
        $financeOfficer = Role::firstOrCreate(['name' => 'finance_officer', 'guard_name' => 'web']);
        $financeOfficer->syncPermissions([
            'students.view',
            'payments.view', 'payments.create', 'payments.confirm', 'payments.refund',
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.send',
            'tasks.view', 'tasks.update',
        ]);

        $visaOfficer = Role::firstOrCreate(['name' => 'visa_officer', 'guard_name' => 'web']);
        $visaOfficer->syncPermissions([
            'students.view',
            'visa.view', 'visa.update',
            'documents.view', 'documents.verify', 'documents.reject',
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

        // Student: no spatie permissions at all — every student capability is
        // ownership-based ("is this my record?"), enforced entirely in the Policies.
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
    }
}
