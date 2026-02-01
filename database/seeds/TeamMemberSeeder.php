<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('team_members')->truncate();

        $now = now();
        $data = [
            [
                'name' => 'Amanda Rizqyana',
                'role' => 'Founder',
                'email' => 'amandarizqyana@hokgstudio.com',
                'instagram' => 'https://www.instagram.com/amandarizqyana',
                'avatar' => 'assets/img/tentang/tentang-1.webp',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Arief Hadinata',
                'role' => 'Co-Founder',
                'email' => 'ariefhadinata@hokgstudio.com',
                'instagram' => 'https://www.instagram.com/ariefhadinata',
                'avatar' => 'assets/img/tentang/tentang-1.webp',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Bakhtiar Amrullah',
                'role' => 'Co-Founder',
                'email' => 'info@example.com',
                'instagram' => 'https://instagram.com/',
                'avatar' => 'assets/img/tentang/tentang-1.webp',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('team_members')->insert($data);
    }
}
