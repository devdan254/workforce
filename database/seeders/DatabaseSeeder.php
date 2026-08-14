<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Foundation (must run in this order) ----
        $this->call([
            RolesAndPermissionsSeeder::class,
            StatusesAndTransitionsSeeder::class,
            DocumentCategoriesSeeder::class,
        ]);

        // ---- 1 Super Admin, so you can log in immediately ----
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@alturaworkforce.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Staff + sample students + universities/courses/applications land in
        // a dedicated DemoDataSeeder next — kept separate so you can re-run
        // the foundation seeders in production without ever touching demo data.
    }
}
