<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\Models\MerchProduct;
use App\Models\MerchCategory;
use Illuminate\Http\Request;

class GetMerchProduct extends Controller
{
    private const PER_PAGE = 21;
    private const FEATURED_PAGE_SIZE = 3;
    private const NORMAL_PAGE_SIZE = 18;
    private const SPAN2_INDEXES = [0, 8, 16];
    private const ALLOWED_SORT = ['', 'newest', 'oldest', 'cheapest', 'priciest'];
    private const MAX_SEARCH_LEN = 50;

    public function __invoke(Request $request)
    {
        [$batch, $sort, $category, $search] = $this->validatedParams($request);

        $featured = $this->buildQuery('featured', $search, $category, $sort)
            ->simplePaginate(self::FEATURED_PAGE_SIZE, ['*'], 'featured_page', $batch);
        $normal = $this->buildQuery('normal', $search, $category, $sort)
            ->simplePaginate(self::NORMAL_PAGE_SIZE, ['*'], 'normal_page', $batch);

        $result = $this->composeGrid($featured, $normal);

        // Transform data: hanya ambil field penting
        $products = array_map(function ($p) {
            if (!$p) return null;

            $variant = $p->defaultVariant;

            // Jika tidak ada default variant sama sekali
            if (!$variant) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'image' => null,
                    'price' => null,
                    'discount' => null,
                ];
            }

            $image = $variant->images && $variant->images->count()
                ? $variant->images->first()->image_path
                : null;

            // Ambil harga & diskon dari sizes jika ada, jika tidak pakai kolom variant
            $price = null;
            $discount = null;
            if ($variant->sizes && $variant->sizes->count() > 0) {
                $price = $variant->sizes->min('price');      // harga minimum
                $discount = $variant->sizes->max('discount'); // diskon maksimum
            } else {
                $price = $variant->price;
                $discount = $variant->discount;
            }

            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'image' => $image,
                'price' => $price,
                'discount' => $discount,
            ];
        }, $result);

        $response = [
            'batch' => $batch,
            'count' => count(array_filter($products)),
            'products' => array_values($products),
            'has_more_featured' => $featured->hasMorePages(),
            'has_more_normal' => $normal->hasMorePages(),
        ];

        $this->logFetch('Fetch merch products batch', [
            'batch' => $batch,
            'count' => $response['count'],
            'product_ids' => collect($products)->filter()->pluck('id')->toArray(),
        ]);
        $this->logFetch('Fetch merch products API response', $response);

        return response()->json($response);
    }

    private function validatedParams(Request $request): array
    {
        $batch = max(1, (int)$request->query('batch', 1));
        $sort = $request->query('sort', '');
        if (!in_array($sort, self::ALLOWED_SORT, true)) $sort = '';
        $category = $request->query('category');
        if ($category && !MerchCategory::where('slug', $category)->exists()) $category = null;
        $search = $request->query('search');
        if ($search && strlen($search) > self::MAX_SEARCH_LEN) $search = substr($search, 0, self::MAX_SEARCH_LEN);
        return [$batch, $sort, $category, $search];
    }

    private function buildQuery(string $type, ?string $search, ?string $category, string $sort)
    {
        $query = MerchProduct::with([
            'defaultVariant.images' => fn($q) => $q->select('id', 'merch_product_variant_id', 'image_path')->orderBy('id'),
            'defaultVariant.sizes' => fn($q) => $q->select('id', 'merch_product_variant_id', 'price', 'discount')->orderBy('price'),
        ])
            ->select(['id', 'name', 'slug', 'type', 'status'])
            ->where('status', 'active')
            ->where('type', $type);

        if ($search) $query->where('name', 'like', '%'.$search.'%');
        if ($category) $query->whereHas('categories', fn($q) => $q->where('slug', $category));

        $this->applySort($query, $sort);
        return $query;
    }

    private function applySort($query, string $sort): void
    {
        if ($sort === 'newest') {
            $query->orderByDesc('created_at');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at');
        } elseif ($sort === 'cheapest') {
            $query->orderBy('id'); // Ganti dengan kolom harga jika ada
        } elseif ($sort === 'priciest') {
            $query->orderByDesc('id');
        } else {
            $query->orderByDesc('created_at');
        }
    }

    private function composeGrid($featured, $normal): array
    {
        $grid = [];
        $fIdx = 0; $nIdx = 0;
        for ($i = 0; $i < self::PER_PAGE; $i++) {
            if (in_array($i, self::SPAN2_INDEXES, true)) {
                $grid[] = isset($featured[$fIdx]) ? $featured[$fIdx++] : null;
            } else {
                $grid[] = isset($normal[$nIdx]) ? $normal[$nIdx++] : null;
            }
        }
        return $grid;
    }

    private function logFetch(string $message, array $context): void
    {
        if (app()->environment(['local', 'development', 'dev'])) {
            \Log::info($message, $context);
        }
    }
}