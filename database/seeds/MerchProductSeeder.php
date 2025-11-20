<?php

use Illuminate\Database\Seeder;
use App\Models\MerchProduct;

class MerchProductSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 100; $i++) {
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