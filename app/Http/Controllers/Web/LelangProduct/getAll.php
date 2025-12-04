<?php

namespace App\Http\Controllers\Web\LelangProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Products;
use App\Bid; 
use Illuminate\Support\Facades\Log;

class getAll extends Controller
{
    private const PER_PAGE = 21;
    private const FEATURED_PAGE_SIZE = 3;
    private const NORMAL_PAGE_SIZE = 18;
    private const SPAN2_INDEXES = [0, 8, 16];

    public function json(Request $request)
    {
        try {
            $batch = max(1, (int) $request->query('batch', 1));
            $search = trim((string) $request->query('search', ''));
            $category = trim((string) $request->query('category', ''));
            $sort = (string) $request->query('sort', '');

            // current authenticated user (nullable)
            $currentUserId = auth()->id();

            // Query Featured
            $featuredQuery = $this->buildQuery('featured', $search, $category, $sort);
            $featured = $featuredQuery->simplePaginate(self::FEATURED_PAGE_SIZE, ['*'], 'featured_page', $batch);

            // Query Normal
            $normalQuery = $this->buildQuery('normal', $search, $category, $sort);
            $normal = $normalQuery->simplePaginate(self::NORMAL_PAGE_SIZE, ['*'], 'normal_page', $batch);

            $result = $this->composeGrid($featured, $normal);

            $products = array_map(function ($p) use ($currentUserId) {
                if (!$p) return null;

                // DATA HARGA TERTINGGI
                // Kita ambil dari kolom virtual 'highest_bid_amount' yang dibuat di buildQuery
                // Jika null (belum ada bid), kita set ke 0
                $highestBid = $p->highest_bid_amount ?? 0;

                return [
                    'title' => $p->title,
                    'slug'  => $p->slug,
                    'image' => $p->imageUtama->path ?? 'assets/img/default.jpg',
                    'category' => optional($p->kategori)->name ?? '',
                    'category_slug' => optional($p->kategori)->slug ?? '',
                    'price' => $p->price,         // Harga asli (angka)
                    'price_str' => $p->price_str, // Harga asli (format Rp string dari Model)
                    'diskon' => $p->diskon,
                    'highest_bid' => $highestBid, // Harga bid tertinggi (angka)
                    'end_date_iso' => $p->end_date ? $p->end_date->toIso8601String() : null,
                    // tambahan untuk frontend: status dan id pemenang (dari products)
                    'status' => $p->status,
                    'winner_id' => $p->winner_id ?? null,
                    'is_winner' => ($p->status == 2 && $p->winner_id && $currentUserId && ($p->winner_id == $currentUserId)),
                ];
            }, $result);

            $response = [
                'batch' => $batch,
                'count' => count(array_filter($products)),
                'products' => array_values($products),
                'has_more_featured' => $featured->hasMorePages(),
                'has_more_normal' => $normal->hasMorePages(),
            ];

            $this->responseLog($response);

            return response()->json($response);

        } catch (\Throwable $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 200);
        }
    }

    private function buildQuery(string $type, ?string $search, ?string $category, string $sort)
    {
        // --- OPTIMASI SUBQUERY (SOLUSI AGAR TIDAK BOROS) ---
        // Kita siapkan perintah untuk mencari MAX price dari tabel bid
        // Pastikan nama tabel 'bid' sesuai dengan model App\Bid
        $highestBidQuery = \App\Bid::selectRaw('MAX(price)')
            ->whereColumn('product_id', 'products.id');

        // Masukkan subquery ke dalam query utama
        $query = Products::with(['imageUtama', 'kategori'])
            ->select('products.*') // Ambil semua kolom produk
            ->selectSub($highestBidQuery, 'highest_bid_amount') // Tambah kolom virtual
            ->whereIn('status', [1, 2])
            ->where('type', $type);

        if ($search !== '') {
            $searchNoSpace = preg_replace('/\s+/', '', strtolower($search));
            $query->whereRaw("REPLACE(LOWER(title), ' ', '') LIKE ?", ['%' . $searchNoSpace . '%']);
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

    private function responseLog(array $response): void
    {
        if (app()->environment(['local', 'testing'])) {
            Log::info('LelangProduct JSON Response:', $response);
        }
    }
}