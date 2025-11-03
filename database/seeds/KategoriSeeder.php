<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kategori')->truncate();

            $data = [
                [
                    'name' => 'Kreatif',
                    'slug' => Str::slug('kreatif'),
                    'cat_type' => 'product',
                ],
                [
                    'name' => 'Seni',
                    'slug' => Str::slug('seni'),
                    'cat_type' => 'blog',
                ]
                
                 
            ];
            DB::table('kategori')->insert($data);
    }
}
