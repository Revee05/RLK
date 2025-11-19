<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;
use Illuminate\Http\Request;
use App\models\MerchCategory;

class GetMerchProduct extends Controller
{

    public function __invoke(Request $request)
    {
        // Validasi batch
        $batch = (int) $request->query('batch', 1);
        if ($batch < 1) $batch = 1;

        // Validasi sort
        $allowedSort = ['', 'newest', 'oldest', 'cheapest', 'priciest'];
        $sort = $request->query('sort', '');
        if (!in_array($sort, $allowedSort)) {
            $sort = '';
        }

        // Validasi category (hanya slug yang ada di database)
        $category = $request->query('category');
        if ($category) {
            $exists = \App\models\MerchCategory::where('slug', $category)->exists();
            if (!$exists) $category = null;
        }

        $search = $request->query('search');
        if ($search && strlen($search) > 50) {
            $search = substr($search, 0, 50);
        }
        $perPage = 21;

        // Featured products (ambil hanya defaultVariant)
        $featuredQuery = MerchProduct::with([
            'defaultVariant.images' => function($q) {
                $q->select('id', 'merch_product_variant_id', 'image_path', 'label')->limit(1);
            },
            'defaultVariant.sizes' => function($q) {
                $q->orderBy('id'); // atau tambahkan where('is_default', 1) jika ada flag default
            }
        ])
            ->select(['id', 'name', 'slug', 'type', 'status'])
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
        elseif ($sort == 'cheapest') $featuredQuery->orderBy('id'); // Sorting by id as price is now in variant
        elseif ($sort == 'priciest') $featuredQuery->orderByDesc('id');
        else $featuredQuery->orderByDesc('created_at');

        $featured = $featuredQuery->simplePaginate(3, ['*'], 'featured_page', $batch);

        // Normal products (ambil hanya defaultVariant)
        $normalQuery = MerchProduct::with([
            'defaultVariant.images' => function($q) {
                $q->select('id', 'merch_product_variant_id', 'image_path', 'label')->limit(1);
            },
            'defaultVariant.sizes' => function($q) {
                $q->orderBy('id'); // atau tambahkan where('is_default', 1) jika ada flag default
            }
        ])
            ->select(['id', 'name', 'slug', 'type', 'status'])
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
        elseif ($sort == 'cheapest') $normalQuery->orderBy('id');
        elseif ($sort == 'priciest') $normalQuery->orderByDesc('id');
        else $normalQuery->orderByDesc('created_at');

        $normal = $normalQuery->simplePaginate(18, ['*'], 'normal_page', $batch);

        // Susun urutan produk: featured hanya di span2, normal hanya di cell biasa
        $result = [];
        $featuredIdx = 0;
        $normalIdx = 0;
        $span2Idx = [0, 8, 16];

        for ($i = 0; $i < $perPage; $i++) {
            if (in_array($i, $span2Idx)) {
                $result[] = isset($featured[$featuredIdx]) ? $featured[$featuredIdx++] : null;
            } else {
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

        $response = [
            'batch' => $batch,
            'count' => count(array_filter($result)),
            'products' => array_values($result),
            'has_more_featured' => $featured->hasMorePages(),
            'has_more_normal' => $normal->hasMorePages(),
        ];

        // Log seluruh response jika env local/development
        if (app()->environment(['local', 'development', 'dev'])) {
            \Log::info('Fetch merch products API response', $response);
        }

        return response()->json($response);
    }
}