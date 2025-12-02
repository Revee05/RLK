<?php

namespace App\Http\Controllers\Web\LelangProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Products;

class getAll extends Controller
{
    /**
     * Menampilkan daftar produk lelang (untuk halaman lelang)
     */
    public function index(Request $request)
    {
        // Ambil produk lelang, bisa tambahkan filter sesuai kebutuhan
        $products = Products::active()
            ->orderBy('id', 'desc')
            ->paginate(16);

        return view('web.lelang', compact('products'));
    }
}