<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class KaryaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('karya')->truncate();

            $data = [
                [
                    'name' => 'Sandi Tumiwa',
                    'slug' => Str::slug('Sandi Tumiwa', '-'),
                ]
                 
            ];
            DB::table('karya')->insert($data);
    }
}
