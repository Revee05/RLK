<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminBaruSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin Baru',
                'email' => 'adminbaru2@gmail.com',
                'username' => 'adminbaru',
                'password' => Hash::make('passwordbaru'),
                'access' => 'admin',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'User Kedua',
                'email' => 'user2@gmail.com',
                'username' => 'userkedua',
                'password' => Hash::make('password'),
                'access' => 'member',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        ]);

    }
}
