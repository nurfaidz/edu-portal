<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (User::where('name', $row['nim'])->exists()) {
                continue;
            }

            \DB::transaction(function () use ($row) {
                $userStudent = User::create([
                    'name' => $row['nim'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['nim']),
                ]);

                $student = $userStudent->studentProfile()->create([
                    'name' => $row['nama'],
                    'nim' => $row['nim'],
                    'class' => $row['kelas'],
                ]);

                $role = Role::where('name', \App\Enums\Roles\Role::Student->value)->first();
                $userStudent->assignRole($role);
            });
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
