<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\Models\MerchProduct; // perbaikan namespace Models
use App\Models\MerchCategory; // perbaikan namespace Models
use Illuminate\Http\Request;

class GetMerchProduct extends Controller
{
    // Konstanta layout & pagination
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
        $this->augmentMetrics($result);

        $response = [
            'batch' => $batch,
            'count' => count(array_filter($result)),
            'products' => array_values($result),
            'has_more_featured' => $featured->hasMorePages(),
            'has_more_normal' => $normal->hasMorePages(),
        ];

        $this->logFetch('Fetch merch products batch', [
            'batch' => $batch,
            'count' => $response['count'],
            'product_ids' => collect($result)->filter()->pluck('id')->toArray(),
        ]);
        $this->logFetch('Fetch merch products API response', $response);

        return response()->json($response);
    }

    /* ------------------------------- Param Handling ------------------------------ */
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

    /* -------------------------------- Build Query -------------------------------- */
    private function buildQuery(string $type, ?string $search, ?string $category, string $sort)
    {
        $query = MerchProduct::with([
            'defaultVariant.images' => fn($q) => $q->select('id', 'merch_product_variant_id', 'image_path', 'label')->orderBy('id'),
            'defaultVariant.sizes' => fn($q) => $q->orderBy('id'),
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
            $query->orderBy('id'); // placeholder: adjust if product price column exists
        } elseif ($sort === 'priciest') {
            $query->orderByDesc('id');
        } else {
            $query->orderByDesc('created_at');
        }
    }

    /* --------------------------------- Grid Build -------------------------------- */
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

    /* ------------------------------- Metrics Augment ----------------------------- */
    private function augmentMetrics(array &$products): void
    {
        foreach ($products as $p) {
            if (!$p) continue;
            $variant = $p->defaultVariant;
            if (!$variant) {
                $p->display_price = $p->display_stock = $p->display_discount = null;
                continue;
            }
            if ($variant->sizes && $variant->sizes->count()) {
                $p->display_price = $variant->sizes->min('price');
                $p->display_stock = $variant->sizes->sum('stock');
                $p->display_discount = $variant->sizes->max('discount');
            } else {
                $p->display_price = $variant->price;
                $p->display_stock = $variant->stock;
                $p->display_discount = $variant->discount;
            }
        }
    }

    /* ---------------------------------- Logging ---------------------------------- */
    private function logFetch(string $message, array $context): void
    {
        if (app()->environment(['local', 'development', 'dev'])) {
            \Log::info($message, $context);
        }
    }
}