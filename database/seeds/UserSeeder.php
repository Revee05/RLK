<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //users seeder
        DB::table('users')->truncate();

            $data = [
                [
                    'name' => 'Admin',
                    'email' => 'admin@gmail.com',
                    'username' => 'admin',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password,
                    'access' => 'admin',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ],
                [
                    'name' => 'Member',
                    'email' => 'member@gmail.com',
                    'username' => 'member',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password,
                    'access' => 'member',
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ],
                
            ];
            DB::table('users')->insert($data);
    }
}
