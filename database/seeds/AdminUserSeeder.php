<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User; // Pastikan model User di-import
use Illuminate\Support\Facades\Hash; // Import Hash

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek dulu agar tidak duplikat
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // Cari berdasarkan email
            [
                'name' => 'Admin12',
                'username' => 'admin2',
                'password' => Hash::make('admin123123'), // Ganti ini!
                'access' => 'admin',
                'jenis_kelamin' => 'Laki-laki',
                'email_verified_at' => now()
            ]
        );
    }
}