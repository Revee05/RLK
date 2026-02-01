<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MerchtrySeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada setidaknya 1 kategori (ID 1) untuk relasi
        // Kita insert ignore saja biar tidak error kalau sudah ada
        DB::table('merch_categories')->insertOrIgnore([
            'id' => 1,
            'name' => 'General Merchandise',
            'slug' => 'general-merch',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $products = [
            ['name' => 'Kaos Basic Hitam Polos', 'type' => 'normal', 'price' => 85000, 'image' => 'merch/kaos_hitam.jpg'],
            ['name' => 'Hoodie Oversize Senja', 'type' => 'featured', 'price' => 250000, 'image' => 'merch/hoodie_senja.jpg'],
            ['name' => 'Totebag Kanvas Art', 'type' => 'normal', 'price' => 45000, 'image' => 'merch/totebag_art.jpg'],
            ['name' => 'Topi Baseball Vintage', 'type' => 'normal', 'price' => 65000, 'image' => 'merch/topi_vintage.jpg'],
            ['name' => 'Kaos Putih Grafis Abstrak', 'type' => 'normal', 'price' => 120000, 'image' => 'merch/kaos_putih.jpg'],
            ['name' => 'Jaket Denim Lukis', 'type' => 'featured', 'price' => 450000, 'image' => 'merch/jaket_denim.jpg'],
            ['name' => 'Mug Keramik Eksklusif', 'type' => 'normal', 'price' => 35000, 'image' => 'merch/mug_keramik.jpg'],
            ['name' => 'Sweater Crewneck Navy', 'type' => 'normal', 'price' => 175000, 'image' => 'merch/sweater_navy.jpg'],
            ['name' => 'Tumbler Stainless Custom', 'type' => 'featured', 'price' => 95000, 'image' => 'merch/tumbler.jpg'],
            ['name' => 'Kemeja Flannel Kotak', 'type' => 'normal', 'price' => 185000, 'image' => 'merch/kemeja_flannel.jpg'],
        ];

        foreach ($products as $index => $item) {
            $now = Carbon::now();

            // ---------------------------------------------------------
            // 1. Insert ke tabel `merch_products`
            // ---------------------------------------------------------
            $productId = DB::table('merch_products')->insertGetId([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . Str::random(5),
                'description' => '<p>Deskripsi lengkap untuk produk <strong>' . $item['name'] . '</strong>. Dibuat dengan bahan berkualitas tinggi dan desain eksklusif.</p>',
                'status' => 'active',
                'type' => $item['type'],
                'price' => $item['price'],
                'stock' => 100, // Stok global
                'discount' => ($index % 3 == 0) ? 10.00 : 0.00,
                'category_id' => 1, // Default ke kategori ID 1
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // ---------------------------------------------------------
            // 2. Insert ke tabel Pivot `merch_category_product`
            // ---------------------------------------------------------
            DB::table('merch_category_product')->insert([
                'merch_product_id' => $productId,
                'merch_category_id' => 1 // Asumsi kategori ID 1
            ]);

            // ---------------------------------------------------------
            // 3. Insert Variant Default ke `merch_product_variants`
            // ---------------------------------------------------------
            $variantId = DB::table('merch_product_variants')->insertGetId([
                'merch_product_id' => $productId,
                'name' => 'Default Color',
                'code' => 'SKU-' . strtoupper(Str::random(6)), // Kolom 'code' (bukan sku)
                'is_default' => 1,
                'stock' => 50,
                'price' => $item['price'],
                'discount' => ($index % 3 == 0) ? 10.00 : 0.00,
                'weight' => 200, // Kolom 'weight' (sesuai screenshot)
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // ---------------------------------------------------------
            // 4. Insert Gambar ke `merch_product_variant_images`
            // ---------------------------------------------------------
            DB::table('merch_product_variant_images')->insert([
                'merch_product_variant_id' => $variantId,
                'image_path' => $item['image'],
                'label' => 'Front View', // Kolom 'label' (bukan is_primary)
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // ---------------------------------------------------------
            // 5. Insert Sizes ke `merch_product_variant_sizes`
            // ---------------------------------------------------------
            $sizes = ['S', 'M', 'L', 'XL'];
            foreach ($sizes as $size) {
                DB::table('merch_product_variant_sizes')->insert([
                    'merch_product_variant_id' => $variantId,
                    'size' => $size,
                    'stock' => 15, // Stok per size
                    'price' => $item['price'],
                    'discount' => ($index % 3 == 0) ? 10.00 : 0.00,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}