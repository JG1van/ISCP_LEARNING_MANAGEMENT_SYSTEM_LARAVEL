<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('admins')->insert([
            [
                'id' => 1,
                'name' => 'Admin A',
                'username' => 'QAZWSXEDC',
                'password' => Hash::make('admin1234'),
                'role' => 1,
                'date_in' => now()->format('Y-m-d'),
                'position' => 'Super-Admin',
                'phone' => '081111111111',
                'img' => null,
                'login_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Admin B',
                'username' => 'Admin',
                'password' => Hash::make('admin1234'),
                'role' => 2,
                'date_in' => now()->format('Y-m-d'),
                'position' => 'Admin',
                'phone' => '082222222222',
                'img' => null,
                'login_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Admin C',
                'username' => 'Operasional',
                'password' => Hash::make('admin1234'),
                'role' => 3,
                'date_in' => now()->format('Y-m-d'),
                'position' => 'Operasional',
                'phone' => '083333333333',
                'img' => null,
                'login_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Admin D',
                'username' => 'Konten-Pembelajaran',
                'password' => Hash::make('admin1234'),
                'role' => 4,
                'date_in' => now()->format('Y-m-d'),
                'position' => 'Konten-Pembelajaran',
                'phone' => '084444444444',
                'img' => null,
                'login_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Admin E',
                'username' => 'Layanan-Pengguna',
                'password' => Hash::make('admin1234'),
                'role' => 5,
                'date_in' => now()->format('Y-m-d'),
                'position' => 'Layanan-Pengguna',
                'phone' => '085555555555',
                'img' => null,
                'login_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('exercise_types')->insert([
            ['id' => 1, 'kode' => 'UH', 'name' => 'Ulangan Harian'],
            ['id' => 2, 'kode' => 'PTS', 'name' => 'Penilaian Tengah Semester'],
            ['id' => 3, 'kode' => 'PAS', 'name' => 'Penilaian Akhir Semester'],
            ['id' => 4, 'kode' => 'AKM', 'name' => 'Asesmen Kompetensi Minimum'],
            ['id' => 5, 'kode' => 'ASPD', 'name' => 'Asesmen Standardisasi Pendidikan Daerah'],
        ]);
        DB::table('exercise_models')->insert([
            ['id' => 1, 'name' => 'Pilihan Ganda'],
            ['id' => 2, 'name' => 'Pilihan Ganda Banyak'],
            ['id' => 3, 'name' => 'Pernyataan'],
            ['id' => 4, 'name' => 'Isian'],
            ['id' => 5, 'name' => 'Uraian'],
            ['id' => 6, 'name' => 'Iya Tidak'],
            ['id' => 7, 'name' => 'Argumen'],
        ]);
    }

}
