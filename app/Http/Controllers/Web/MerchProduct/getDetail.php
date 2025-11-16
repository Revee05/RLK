<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;

class getDetail extends Controller
{
    public function __invoke($slug)
    {
        // get produk 
        $product = MerchProduct::select('id', 'name', 'slug', 'description', 'price', 'discount', 'stock')
            ->with([
                'images:id,merch_product_id,image_path',
                'categories:id,name'
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // get related products
        $relatedProducts = MerchProduct::select('id', 'slug', 'name', 'price', 'discount')
            ->with(['images:id,merch_product_id,image_path'])
            ->whereHas('categories', function($q) use ($product) {
                return $q->whereIn('merch_categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        // Log hasil fetching hanya jika environment local atau development
        if (app()->environment(['local', 'development', 'dev'])) {
            \Log::info('Detail Product:', $product->toArray());
            \Log::info('Related Products:', $relatedProducts->toArray());
        }

        return view('web.productsPage.MerchDetailProductPage', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}