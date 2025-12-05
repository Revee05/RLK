<?php

use Illuminate\Database\Seeder;
use App\Panduan;

class PanduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['title' => 'Panduan Peserta Lelang', 'slug' => 'peserta-lelang'],
            ['title' => 'Panduan Penjualan Karya Lelang', 'slug' => 'penjualan-karya-lelang'],
            ['title' => 'Panduan Pembelian Produk', 'slug' => 'pembelian-produk'],
            ['title' => 'Panduan Penjualan Produk', 'slug' => 'penjualan-produk'],
        ];

        foreach ($data as $item) {
            Panduan::create($item);
        }
    }
}
