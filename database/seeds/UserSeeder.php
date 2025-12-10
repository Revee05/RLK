<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvPath = database_path('seeds/data/users.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: {$csvPath}");
            return;
        }

        // Set true hanya jika ingin hapus semua user dulu (HATI-HATI)
        $truncate = false;
        if ($truncate) {
            DB::table('users')->truncate();
            $this->command->info('Truncated users table.');
        }

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            $this->command->error('CSV is empty');
            return;
        }

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $name  = trim($data['name'] ?? 'User');
            $email = trim($data['email'] ?? '');
            if ($email === '') {
                $this->command->error("Skipping row without email (name: {$name})");
                continue;
            }

            $username = trim($data['username'] ?? '');
            if ($username === '') {
                // create username from email if kosong
                $username = preg_replace('/@.*$/', '', $email);
            }

            $rawPassword = trim($data['password'] ?? '');
            if ($rawPassword === '') {
                // generate random password if tidak disediakan
                $rawPassword = Str::random(12);
                $this->command->info("Generated password for {$email}: {$rawPassword}");
            }

            $passwordHash = Hash::make($rawPassword);

            $access = trim($data['access'] ?? 'member');
            // set email verification time always to now()
            $emailVerifiedAt = now();
            $createdAt = !empty($data['created_at']) ? $data['created_at'] : now();
            $updatedAt = !empty($data['updated_at']) ? $data['updated_at'] : $createdAt;
            
            // idempotent: updateOrInsert by email
            DB::table('users')->updateOrInsert(
                ['email' => $email],
                [
                    'name' => $name,
                    'username' => $username,
                    'password' => $passwordHash,
                    'access' => $access,
                    'email_verified_at' => $emailVerifiedAt,
                    'remember_token' => Str::random(10),
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );

            $this->command->info("Upserted user: {$email}");
        }

        fclose($handle);
    }
}
