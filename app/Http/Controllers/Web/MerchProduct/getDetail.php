<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;

class getDetail extends Controller
{
    /**
     * BLOCK: Entry point - resolve product detail by slug.
     */
    public function __invoke(string $slug)
    {
        // BLOCK: Fetch Product with required relations
        $product = $this->fetchProductWithRelations($slug);

        // BLOCK: Augment product variants with display_* fields
        $this->augmentVariantsDisplayFields($product->variants);

        // BLOCK: Fetch Related Products
        $relatedProducts = $this->fetchRelatedProducts($product);

        // BLOCK: Augment related products with display price/discount based on default variant
        $this->augmentRelatedProductsDisplayFields($relatedProducts);

        // BLOCK: Conditional logging (local/dev only)
        $this->logDebugData($product, $relatedProducts);

        // BLOCK: Return View
        return view('web.productsPage.MerchDetailProductPage', [
            'product' => $product,
            'relatedProducts' => $relatedProducts->map(function($rel) {
                $variant = $rel->variants->firstWhere('is_default', 1) ?: $rel->variants->first();
                $img = ($variant && $variant->images->count())
                    ? asset($variant->images->first()->image_path)
                    : 'https://placehold.co/300x250?text=No+Image';

                return [
                    'id' => $rel->id,
                    'slug' => $rel->slug,
                    'name' => $rel->name,
                    'display_price' => $rel->display_price,
                    'display_discount' => $rel->display_discount,
                    'image' => $img,
                ];
            }),
        ]);
    }

    /**
     * BLOCK: Query builder for a single product with eager-loaded relations.
     */
    private function fetchProductWithRelations(string $slug): MerchProduct
    {
        return MerchProduct::select('id', 'name', 'slug', 'description', 'status', 'type')
            ->with([
                'categories:id,name',
                'variants' => function ($q) {
                    $q->select('id', 'merch_product_id', 'name', 'code', 'is_default', 'stock', 'price', 'discount');
                },
                'variants.images:id,merch_product_variant_id,image_path,label',
                'variants.sizes:id,merch_product_variant_id,size,stock,price,discount',
            ])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * BLOCK: Compute display_* fields (price, stock, discount) for each variant collection.
     */
    private function augmentVariantsDisplayFields($variants): void
    {
        foreach ($variants as $variant) {
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
    }

    /**
     * BLOCK: Fetch related products limited by shared categories (excluding current product).
     */
    private function fetchRelatedProducts(MerchProduct $product)
    {
        return MerchProduct::select('id', 'slug', 'name', 'type', 'status')
            ->with([
                'variants' => function ($q) {
                    $q->select('id', 'merch_product_id', 'name', 'code', 'is_default', 'stock', 'price', 'discount');
                },
                'variants.images:id,merch_product_variant_id,image_path,label',
            ])
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('merch_categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->limit(6)
            ->get();
    }

    /**
     * BLOCK: Compute display fields for related products using their default (or first) variant.
     */
    private function augmentRelatedProductsDisplayFields($relatedProducts): void
    {
        foreach ($relatedProducts as $rel) {
            $defaultVariant = $rel->variants->firstWhere('is_default', 1) ?: $rel->variants->first();

            if (!$defaultVariant) {
                $rel->display_price = null;
                $rel->display_discount = null;
                continue;
            }

            if ($defaultVariant->sizes && $defaultVariant->sizes->count()) {
                $rel->display_price = $defaultVariant->sizes->min('price');
                $rel->display_discount = $defaultVariant->sizes->max('discount');
            } else {
                $rel->display_price = $defaultVariant->price;
                $rel->display_discount = $defaultVariant->discount;
            }
        }
    }

    /**
     * BLOCK: Debug logging helper (only runs in local/dev environments).
     */
    private function logDebugData(MerchProduct $product, $relatedProducts): void
    {
        if (!app()->environment(['local', 'development', 'dev'])) {
            return;
        }
        \Log::info('Detail Product:', $product->toArray());
        \Log::info('Related Products:', $relatedProducts->toArray());
    }
}