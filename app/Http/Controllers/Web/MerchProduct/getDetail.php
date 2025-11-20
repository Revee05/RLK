<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;

class getDetail extends Controller
{
    public function __invoke($slug)
    {
        // get produk lengkap dengan variants, images, sizes, categories
        $product = MerchProduct::select('id', 'name', 'slug', 'description', 'price', 'discount', 'stock')
            ->with([
                'categories:id,name',
                'variants' => function($q) {
                    $q->select('id', 'merch_product_id', 'name', 'code', 'is_default');
                },
                'variants.images:id,merch_product_variant_id,image_path,label',
                'variants.sizes:id,merch_product_variant_id,size,stock,price,discount'
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // get related products (boleh tetap simple)
        $relatedProducts = MerchProduct::select('id', 'slug', 'name', 'price', 'discount')
            ->with([
                'variants' => function($q) {
                    $q->select('id', 'merch_product_id', 'name', 'code', 'is_default');
                },
                'variants.images:id,merch_product_variant_id,image_path,label'
            ])
            ->whereHas('categories', function($q) use ($product) {
                return $q->whereIn('merch_categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->limit(6)
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