<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;

class getDetail extends Controller
{
    public function __invoke($slug)
    {
        // Ambil produk lengkap dengan relasi yang diperlukan
        $product = MerchProduct::select('id', 'name', 'slug', 'description', 'status', 'type')
            ->with([
                'categories:id,name',
                'variants' => function($q) {
                    $q->select('id', 'merch_product_id', 'name', 'code', 'is_default', 'stock', 'price', 'discount');
                },
                'variants.images:id,merch_product_variant_id,image_path,label',
                'variants.sizes:id,merch_product_variant_id,size,stock,price,discount'
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Hitung display_price, display_stock, display_discount untuk setiap variant
        foreach ($product->variants as $variant) {
            if ($variant->sizes && $variant->sizes->count()) {
                $variant->display_price = $variant->sizes->min('price');
                $variant->display_stock = $variant->sizes->sum('stock');
                $variant->display_discount = $variant->sizes->max('discount');
            } else {
                $variant->display_price = $variant->price;
                $variant->display_stock = $variant->stock;
                $variant->display_discount = $variant->discount;
            }
        }

        // Produk terkait (related products)
        $relatedProducts = MerchProduct::select('id', 'slug', 'name', 'type', 'status')
            ->with([
                'variants' => function($q) {
                    $q->select('id', 'merch_product_id', 'name', 'code', 'is_default', 'stock', 'price', 'discount');
                },
                'variants.images:id,merch_product_variant_id,image_path,label'
            ])
            ->whereHas('categories', function($q) use ($product) {
                return $q->whereIn('merch_categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->limit(6)
            ->get();

        // Hitung display_price, display_discount untuk related products
        foreach ($relatedProducts as $rel) {
            $defaultVariant = $rel->variants->where('is_default', 1)->first() ?: $rel->variants->first();
            if ($defaultVariant) {
                if ($defaultVariant->sizes && $defaultVariant->sizes->count()) {
                    $rel->display_price = $defaultVariant->sizes->min('price');
                    $rel->display_discount = $defaultVariant->sizes->max('discount');
                } else {
                    $rel->display_price = $defaultVariant->price;
                    $rel->display_discount = $defaultVariant->discount;
                }
            } else {
                $rel->display_price = null;
                $rel->display_discount = null;
            }
        }

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