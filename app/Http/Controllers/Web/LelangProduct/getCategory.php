<?php

namespace App\Http\Controllers\Web\LelangProduct;

use App\Http\Controllers\Controller;
use App\Kategori;
use Illuminate\Http\Request;

class getCategory extends Controller
{
    public function LelangCategory()
    {
        try {
            // Ambil semua kategori produk tanpa filter produk aktif
            $categories = Kategori::where('cat_type', 'product')
                ->select('id', 'name', 'slug')
                ->orderBy('name')
                ->get();

            $response = [
                'categories' => $categories,
            ];
            \Log::info('LelangCategory JSON Response:', $response);

            return response()->json($response);
        } catch (\Throwable $e) {
            \Log::error('LelangCategory ERROR: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Gagal mengambil kategori'], 500);
        }
    }
}