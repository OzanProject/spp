<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun Admin
        \App\Models\User::create([
            'name' => 'Admin Sekolah',
            'email' => 'ardiansyahdzan@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Akun Siswa (Contoh)
        $studentUser = \App\Models\User::create([
            'name' => 'Siswa Satu',
            'email' => 'siswa@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        // Buat Kelas Dummy
        $kelas = \App\Models\ClassRoom::create([
            'name' => 'X RPL 1'
        ]);

        // Masukkan data siswa ke tabel students
        \App\Models\Student::create([
            'user_id' => $studentUser->id,
            'class_room_id' => $kelas->id,
            'nis' => '1001',
            'parent_phone' => '081234567890'
        ]);
        // Default Settings
        $defaults = [
            'school_name'    => 'SMA Negeri 1 Contoh',
            'school_address' => 'Jl. Pendidikan No. 1, Kota Contoh',
            'school_phone'   => '0211234567',
            'school_email'   => 'info@sekolah.sch.id',
            'spp_amount'     => '350000',
            'academic_year'  => '2025/2026',
        ];

        foreach ($defaults as $key => $value) {
            \App\Models\Setting::set($key, $value);
        }
    }
}
