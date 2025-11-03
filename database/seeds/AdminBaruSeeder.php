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
            'name' => 'Admin Baru',
            'email' => 'adminbaru@gmail.com',
            'username' => 'adminbaru',
            'password' => Hash::make('passwordbaru'),
            'access' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }
}
