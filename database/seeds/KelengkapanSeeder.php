<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class KelengkapanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kelengkapans')->truncate();

        $data = [
            [
                'name' => 'kotak pengemasan',
                'slug' => Str::slug('kotak pengemasan', '-'),
            ],
            [
                'name' => 'sertifikat keaslian karya',
                'slug' => Str::slug('sertifikat keaslian karya', '-'),
            ],
            [
                'name' => 'sarung tangan',
                'slug' => Str::slug('sarung tangan', '-'),
            ],
            [
                'name' => 'poster',
                'slug' => Str::slug('poster', '-'),
            ],
            [
                'name' => 'stiker',
                'slug' => Str::slug('stiker', '-'),
            ],
            [
                'name' => 'ucapan terima kasih dari seniman',
                'slug' => Str::slug('ucapan terima kasih dari seniman', '-'),
            ],
            [
                'name' => 'bonus dari seniman',
                'slug' => Str::slug('bonus dari seniman', '-'),
            ],
            [
                'name' => 'figura',
                'slug' => Str::slug('figura', '-'),
            ],
            [
                'name' => 'cetak digital',
                'slug' => Str::slug('cetak digital', '-'),
            ],
          
        ];
        DB::table('kelengkapans')->insert($data);
    }
}
