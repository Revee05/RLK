<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;
use Illuminate\Http\Request;
use App\models\MerchCategory;

class GetMerchProduct extends Controller
{
    protected function getCategories()
    {
        return MerchCategory::select('id', 'name', 'slug')->orderBy('name')->get();
    }

    public function __invoke(Request $request)
    {
        $batch = max(1, (int) $request->query('batch', 1));
        $perPage = 21;

        $search = $request->query('search');
        $category = $request->query('category');
        $sort = $request->query('sort');

        $featuredQuery = MerchProduct::with('images')
            ->select(['id', 'name', 'slug', 'price', 'stock', 'status', 'discount', 'type'])
            ->where('status', 'active')
            ->where('type', 'featured');

        if ($search) {
            $featuredQuery->where('name', 'like', '%' . $search . '%');
        }
        if ($category) {
            $featuredQuery->whereHas('categories', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }
        // Sorting
        if ($sort == 'newest') $featuredQuery->orderByDesc('created_at');
        elseif ($sort == 'oldest') $featuredQuery->orderBy('created_at');
        elseif ($sort == 'cheapest') $featuredQuery->orderBy('price');
        elseif ($sort == 'priciest') $featuredQuery->orderByDesc('price');
        else $featuredQuery->orderByDesc('created_at');

        $featured = $featuredQuery
            ->skip(($batch - 1) * 3)
            ->take(3)
            ->get()
            ->values();

        $normalQuery = MerchProduct::with('images')
            ->select(['id', 'name', 'slug', 'price', 'stock', 'status', 'discount', 'type'])
            ->where('status', 'active')
            ->where('type', 'normal');

        if ($search) {
            $normalQuery->where('name', 'like', '%' . $search . '%');
        }
        if ($category) {
            $normalQuery->whereHas('categories', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }
        // Sorting
        if ($sort == 'newest') $normalQuery->orderByDesc('created_at');
        elseif ($sort == 'oldest') $normalQuery->orderBy('created_at');
        elseif ($sort == 'cheapest') $normalQuery->orderBy('price');
        elseif ($sort == 'priciest') $normalQuery->orderByDesc('price');
        else $normalQuery->orderByDesc('created_at');

        $normal = $normalQuery
            ->skip(($batch - 1) * 18)
            ->take(18)
            ->get()
            ->values();

        // Susun urutan produk: featured hanya di span2, normal hanya di cell biasa
        $result = [];
        $featuredIdx = 0;
        $normalIdx = 0;
        $span2Idx = [0, 8, 16];

        for ($i = 0; $i < $perPage; $i++) {
            if (in_array($i, $span2Idx)) {
                // Hanya featured di cell span-2
                $result[] = isset($featured[$featuredIdx]) ? $featured[$featuredIdx++] : null;
            } else {
                // Hanya normal di cell biasa
                $result[] = isset($normal[$normalIdx]) ? $normal[$normalIdx++] : null;
            }
        }

        // Log hasil fetching hanya jika env local atau development
        if (app()->environment(['local', 'development', 'dev'])) {
            \Log::info('Fetch merch products batch', [
                'batch' => $batch,
                'count' => count(array_filter($result)),
                'product_ids' => collect($result)->filter()->pluck('id')->toArray(),
            ]);
        }

        // Jika ingin mengirim kategori ke response (misal untuk dropdown filter)
        $categories = $this->getCategories();

        return response()->json([
            'batch' => $batch,
            'count' => count(array_filter($result)),
            'products' => array_values($result),
            'categories' => $categories, // <-- tambahkan ini jika perlu
        ]);
    }
}