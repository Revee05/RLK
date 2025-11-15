<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;
use Illuminate\Http\Request;

class GetMerchProduct extends Controller
{
    /**
     * Fetch merch products in batches of 21, only selected fields.
     * Example: /api/merch-products?batch=1
     */
    public function __invoke(Request $request)
    {
        $batch = max(1, (int) $request->query('batch', 1));
        $perPage = 21;

        // Strict: force perPage to always be 21, ignore any user input for limit
        $products = MerchProduct::with('images')
            ->select(['id', 'name', 'slug', 'price', 'stock', 'status', 'discount'])
            ->where('status', 'active')
            ->skip(($batch - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Ensure strict max 21 per batch
        if ($products->count() > $perPage) {
            $products = $products->slice(0, $perPage);
        }

        // Log hasil fetching
        \Log::info('Fetch merch products batch', [
            'batch' => $batch,
            'count' => $products->count(),
            'product_ids' => $products->pluck('id')->toArray(),
        ]);

        return response()->json([
            'batch' => $batch,
            'count' => $products->count(),
            'products' => $products->values(),
        ]);
    }
}