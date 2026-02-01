<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class NewLelangSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Daftar kata sifat untuk judul agar terlihat beda
        $adjectives = ['Misterius', 'Abstrak', 'Klasik', 'Modern', 'Antik', 'Emas', 'Langka'];
        $nouns      = ['Lukisan', 'Patung', 'Vas', 'Keris', 'Batu Akik', 'Guci', 'Kaligrafi'];

        for ($i = 1; $i <= 21; $i++) {
            
            // Membuat Judul Acak
            $randomTitle = $faker->randomElement($nouns) . ' ' . $faker->randomElement($adjectives) . ' ' . $faker->firstName;
            $title       = $randomTitle . ' (Batch ' . $i . ')';
            $slug        = Str::slug($title . '-' . uniqid());

            // Tentukan status featured (Index 1, 9, 17...)
            $type = ($i % 8 == 1) ? 'featured' : 'lelang';

            // 1. INPUT PRODUK BARU
            $productId = DB::table('products')->insertGetId([
                'user_id'       => 1, 
                'kategori_id'   => $faker->numberBetween(1, 5),
                'karya_id'      => $faker->numberBetween(1, 10),
                'title'         => $title,
                'slug'          => $slug,
                'description'   => "Karya seni eksklusif: " . $faker->paragraph() . "\n\nSpesifikasi khusus lelang.",
                'price'         => $faker->numberBetween(150000, 25000000),
                'diskon'        => 0,
                'stock'         => 1,
                'sku'           => 'BID-' . strtoupper(Str::random(5)) . '-' . $i,
                'weight'        => $faker->numberBetween(500, 5000),
                'asuransi'      => 1,
                'long'          => $faker->numberBetween(20, 80),
                'width'         => $faker->numberBetween(20, 80),
                'height'        => $faker->numberBetween(10, 100),
                'status'        => 1,
                'kondisi'       => 'Baru',
                'kelipatan'     => 25000,
                'end_date'      => now()->addDays(rand(3, 10)),
                'type'          => $type,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 2. INPUT GAMBAR OTOMATIS
            $images = [
                [
                    'products_id' => $productId,
                    'name'        => $slug . '-utama.jpg',
                    'path'        => 'uploads/products/default.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'products_id' => $productId,
                    'name'        => $slug . '-samping.jpg',
                    'path'        => 'uploads/products/default.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            ];

            DB::table('product_images')->insert($images);
        }
    }
}