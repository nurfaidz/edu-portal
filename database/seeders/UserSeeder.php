<?php

namespace Database\Seeders;

use App\Enums\Roles\Role as RolesRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::factory()->create([
            'name' => 'Superadmin',
            'email' => 'superadmin@gmail.com',
        ]);

        $roleSuperadmin = Role::where('name', RolesRole::Superadmin->value)->first();
        $superadmin->assignRole($roleSuperadmin);

        // Admin
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'adminExample@gmail.com',
        ]);

        $roleAdmin = Role::where('name', RolesRole::Admin->value)->first();
        $admin->assignRole($roleAdmin);

        // Lecturers
        $lecturers = [
            ['name' => 'A01', 'lecturer_name' => 'Sofyan Lukmanfiandy, S.Kom., M.Kom.', 'email' => 'sofyan.lukmanfiandy@example.com'],
            ['name' => 'A02', 'lecturer_name' => 'Sri Rahayu, S.Kom. M.Eng.', 'email' => 'sri.rahayu@example.com'],
            ['name' => 'A03', 'lecturer_name' => 'Jemmy Edwin Bororing, S.Kom., M.Eng.', 'email' => 'jemmy.edwin@example.com'],
            ['name' => 'A04', 'lecturer_name' => 'Fatsyahrina Fitriastuti, S.Si., M.T.', 'email' => 'fatsyahrina.fitriastuti@example.com'],
            ['name' => 'A05', 'lecturer_name' => 'Eri Haryanto, S.Kom., M.Kom.', 'email' => 'eri.haryanto@example.com'],
            ['name' => 'A06', 'lecturer_name' => 'Agustin Setiyorini, S.Kom., M.Kom.', 'email' => 'agustin.setiyorini@example.com'],
            ['name' => 'A07', 'lecturer_name' => 'Jeffry Andhika Putra, S.T., M.M., M.Eng.', 'email' => 'jeffry.andhika@example.com'],
            ['name' => 'A08', 'lecturer_name' => 'Yumarlin MZ, S.Kom., M.Pd., M.Kom.', 'email' => 'yumarlin.mz@example.com'],
            ['name' => 'A09', 'lecturer_name' => 'Ryan Ari Setyawan S.Kom., M.Eng.', 'email' => 'ryan.ari@example.com'],
            ['name' => 'A010', 'lecturer_name' => 'Erry Maricha Oki NH, S.Kom., MTA', 'email' => 'erry.maricha@example.com'],
        ];

        $roleLecturer = Role::where('name', RolesRole::Lecturer->value)->first();

        foreach ($lecturers as $lecturerData) {
            $lecturerUser = User::factory()->create([
                'name' => $lecturerData['name'],
                'email' => $lecturerData['email'],
            ]);

            $lecturerUser->lecturerProfile()->create([
                'name' => $lecturerData['lecturer_name'],
                'npp' => $lecturerData['name'],
            ]);

            $lecturerUser->assignRole($roleLecturer);
        }

        // Student
        $students = [
            ['name' => 'ST01', 'student_name' => 'Student 01', 'email' => 'student01@example.com'],
            ['name' => 'ST02', 'student_name' => 'Student 02', 'email' => 'student02@example.com'],
            ['name' => 'ST03', 'student_name' => 'Student 03', 'email' => 'student03@example.com']
        ];

        $roleStudent = Role::where('name', RolesRole::Student->value)->first();

        foreach ($students as $studentData) {
            $studentUser = User::factory()->create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
            ]);

            $studentUser->studentProfile()->create([
                'name' => $studentData['student_name'],
                'nim' => $studentData['name'],
            ]);

            $studentUser->assignRole($roleStudent);
        }
    }
}
