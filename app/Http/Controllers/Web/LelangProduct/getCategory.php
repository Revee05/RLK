<?php

namespace App\Http\Controllers\Web\LelangProduct;

use App\Http\Controllers\Controller;
use App\Kategori;
use Illuminate\Http\Request;

class getCategory extends Controller
{
    public function LelangCategory()
    {
        // Ambil semua kategori produk tanpa filter produk aktif
        $categories = Kategori::where('cat_type', 'product')
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }
}