<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => \App\Enums\Roles\Role::Superadmin->value,
            // 'guard_name' => 'superadmin',
            'guard_name' => 'web',
        ]);

        // Admin
        \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => \App\Enums\Roles\Role::Admin->value,
            // 'guard_name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Lecturer
        \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => \App\Enums\Roles\Role::Lecturer->value,
            // 'guard_name' => 'lecturer',
            'guard_name' => 'web',
        ]);

        // Student
        \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => \App\Enums\Roles\Role::Student->value,
            // 'guard_name' => 'student',
            'guard_name' => 'web',
        ]);
    }
}
