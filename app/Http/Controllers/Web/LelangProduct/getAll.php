<?php

namespace App\Http\Controllers\Web\LelangProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Products;

class getAll extends Controller
{
    /**
     * Mengembalikan data produk lelang dalam format JSON (untuk AJAX)
     */
    public function json(Request $request)
    {
        // Validasi & normalisasi parameter
        $batch = max(1, (int) $request->query('batch', 1));
        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));
        $sort = (string) $request->query('sort', '');

        $query = Products::with(['imageUtama', 'kategori'])
            ->active();

        if ($search !== '') {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($category !== '') {
            $query->whereHas('kategori', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        switch ($sort) {
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            case 'cheapest':
                $query->orderBy('price', 'asc');
                break;
            case 'priciest':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // Gunakan simplePaginate agar ringan untuk API
        $perPage = 16;
        $paginator = $query->simplePaginate($perPage, ['*'], 'page', $batch);

        $items = collect($paginator->items())->map(function ($produk) {
            return [
                'title' => $produk->title,
                'slug' => $produk->slug,
                'image' => $produk->imageUtama->path ?? 'assets/img/default.jpg',
                'category' => optional($produk->kategori)->name ?? '',
                'category_slug' => optional($produk->kategori)->slug ?? '',
                'price' => $produk->price,
                'price_str' => $produk->price_str,
                'diskon' => $produk->diskon,
            ];
        })->values();

        return response()->json([
            'batch' => $batch,
            'count' => $items->count(),
            'products' => $items,
            'has_more' => $paginator->hasMorePages(),
        ]);
    }
}