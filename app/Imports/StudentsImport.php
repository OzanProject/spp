<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Cek apakah data mandatory ada
            if (empty($row['nis']) || empty($row['nama']) || empty($row['email'])) {
                continue;
            }

            // Buat atau Update akun User
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['nama'],
                    'password' => Hash::make('password123'), // Default password
                    'role' => 'student'
                ]
            );

            // Jika user sudah ada, update namanya
            if (!$user->wasRecentlyCreated) {
                $user->update(['name' => $row['nama']]);
            }

            // Buat atau Update profil Student
            Student::updateOrCreate(
                ['nis' => $row['nis']],
                [
                    'user_id' => $user->id,
                    'class_room_id' => $row['id_kelas'] ?? null,
                    'parent_phone' => $row['no_hp_ortu'] ?? null,
                ]
            );
        }
    }
}
