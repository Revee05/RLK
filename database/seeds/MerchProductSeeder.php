<?php

namespace Database\Seeds;

use Illuminate\Database\Seeds;
use App\models\MerchProduct;

class MerchProductSeeder extends Seeder
{
    public function run()
    {
        // $this->call(\Database\Seeders\MerchProductSeeder::class);

        for ($i = 1; $i <= 21; $i++) {
            MerchProduct::create([
                'name'        => "Sample Product $i",
                'slug'        => "sample-product-$i",
                'description' => "Deskripsi produk sample ke-$i",
                'price'       => rand(10000, 50000),
                'stock'       => rand(1, 100),
                'status'      => 'active',
                'discount'    => rand(0, 30),
            ]);
        }
    }
}