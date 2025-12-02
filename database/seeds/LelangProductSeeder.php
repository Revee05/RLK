<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LelangProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];
        for ($i = 1; $i <= 21; $i++) {
            $data[] = [
                'user_id'      => 1,
                'kategori_id'  => 1, // pastikan id kategori 1 ada
                'karya_id'     => 5, // pastikan id karya 5 ada
                'title'        => 'Lelang Product ' . $i,
                'slug'         => Str::slug('Lelang Product ' . $i . '-' . uniqid()),
                'description'  => 'Deskripsi produk lelang ke-' . $i,
                'price'        => rand(100000, 1000000),
                'diskon'       => rand(0, 30),
                'stock'        => rand(1, 10),
                'sku'          => 'SKU-LELANG-' . $i . '-' . uniqid(),
                'weight'       => 1,
                'asuransi'     => 1,
                'long'         => 10,
                'width'        => 10,
                'height'       => 10,
                'status'       => 1,
                'kondisi'      => 1,
                'kelipatan'    => 10000,
                'end_date'     => now()->addDays($i),
                'type'         => $i % 8 == 1 ? 'featured' : 'normal', // featured di index 1,9,17 (0,8,16)
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
        DB::table('products')->insert($data);
    }
}