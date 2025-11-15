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

        // Ambil produk featured dan normal
        $featured = MerchProduct::with('images')
            ->select(['id', 'name', 'slug', 'price', 'stock', 'status', 'discount', 'type'])
            ->where('status', 'active')
            ->where('type', 'featured')
            ->orderByDesc('created_at')
            ->skip(($batch - 1) * 3)
            ->take(3)
            ->get()
            ->values();

        $normal = MerchProduct::with('images')
            ->select(['id', 'name', 'slug', 'price', 'stock', 'status', 'discount', 'type'])
            ->where('status', 'active')
            ->where('type', 'normal')
            ->orderByDesc('created_at')
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

        // Log hasil fetching
        \Log::info('Fetch merch products batch', [
            'batch' => $batch,
            'count' => count(array_filter($result)),
            'product_ids' => collect($result)->filter()->pluck('id')->toArray(),
        ]);

        return response()->json([
            'batch' => $batch,
            'count' => count(array_filter($result)),
            'products' => array_values($result),
        ]);
    }
}