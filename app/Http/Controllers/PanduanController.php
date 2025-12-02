<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanduanController extends Controller
{
    // 1. Panduan Pembelian Produk (Sudah ada)
    public function pembelian()
    {
        return view('web.panduan.pembelian');
    }

    // 2. Panduan Peserta Lelang (Baru)
    public function lelangPeserta()
    {
        // Pastikan file view: resources/views/web/panduan/lelang_peserta.blade.php ada
        return view('web.panduan.lelang_peserta');
    }

    // 3. Panduan Penjualan Karya Lelang (Baru)
    public function penjualanKarya()
    {
        // Pastikan file view: resources/views/web/panduan/penjualan_karya.blade.php ada
        return view('web.panduan.penjualan_karya');
    }

    // 4. Panduan Penjualan Produk (Baru)
    public function penjualanProduk()
    {
        // Pastikan file view: resources/views/web/panduan/penjualan_produk.blade.php ada
        return view('web.panduan.penjualan_produk');
    }
}