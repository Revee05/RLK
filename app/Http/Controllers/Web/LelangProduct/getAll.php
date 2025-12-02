<?php

namespace App\Http\Controllers\Web\LelangProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Products;

class getAll extends Controller
{
    private const PER_PAGE = 21;
    private const FEATURED_PAGE_SIZE = 3;
    private const NORMAL_PAGE_SIZE = 18;
    private const SPAN2_INDEXES = [0, 8, 16];

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

        // Query untuk produk featured (type = 'featured')
        $featuredQuery = $this->buildQuery('featured', $search, $category, $sort);
        $featured = $featuredQuery->simplePaginate(self::FEATURED_PAGE_SIZE, ['*'], 'featured_page', $batch);

        // Query untuk produk normal (type = 'normal')
        $normalQuery = $this->buildQuery('normal', $search, $category, $sort);
        $normal = $normalQuery->simplePaginate(self::NORMAL_PAGE_SIZE, ['*'], 'normal_page', $batch);

        // Gabungkan dengan pola grid
        $result = $this->composeGrid($featured, $normal);

        // Transform data
        $products = array_map(function ($p) {
            if (!$p) return null;
            return [
                'title' => $p->title,
                'slug' => $p->slug,
                'image' => $p->imageUtama->path ?? 'assets/img/default.jpg',
                'category' => optional($p->kategori)->name ?? '',
                'category_slug' => optional($p->kategori)->slug ?? '',
                'price' => $p->price,
                'price_str' => $p->price_str,
                'diskon' => $p->diskon,
            ];
        }, $result);

        $response = [
            'batch' => $batch,
            'count' => count(array_filter($products)),
            'products' => array_values($products),
            'has_more_featured' => $featured->hasMorePages(),
            'has_more_normal' => $normal->hasMorePages(),
        ];

        $this->responseLog($response); // gunakan fungsi log khusus

        return response()->json($response);
    }

    private function buildQuery(string $type, ?string $search, ?string $category, string $sort)
    {
        $query = Products::with(['imageUtama', 'kategori'])
            ->where('status', 1)
            ->where('type', $type);

        if ($search !== '') {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($category !== '') {
            $query->whereHas('kategori', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        $this->applySort($query, $sort);
        return $query;
    }

    private function applySort($query, string $sort): void
    {
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
    }

    private function composeGrid($featured, $normal): array
    {
        $grid = [];
        $fIdx = 0;
        $nIdx = 0;
        for ($i = 0; $i < self::PER_PAGE; $i++) {
            if (in_array($i, self::SPAN2_INDEXES, true)) {
                $grid[] = isset($featured[$fIdx]) ? $featured[$fIdx++] : null;
            } else {
                $grid[] = isset($normal[$nIdx]) ? $normal[$nIdx++] : null;
            }
        }
        return $grid;
    }

    /**
     * Log response JSON hanya di environment local/testing
     */
    private function responseLog(array $response): void
    {
        if (app()->environment(['local', 'testing'])) {
            \Log::info('LelangProduct JSON Response:', $response);
        }
    }
}