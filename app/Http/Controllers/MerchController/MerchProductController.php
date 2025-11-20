<?php
namespace App\Http\Controllers\MerchController;

use App\Http\Controllers\Controller;
use App\Models\MerchProduct;
use App\Models\MerchCategory;
use App\Models\MerchProductVariant;
use App\Models\MerchProductVariantImage;
use App\Models\MerchProductVariantSize;
use App\Product\Uploads;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class MerchProductController extends Controller
{
    public function index()
    {
        $merchProducts = MerchProduct::with([
            'categories',
            'variants.images',
            'variants.sizes'
        ])
        ->orderByDesc('created_at')
        ->get();

        return view('admin.master.merchProduct.index', compact('merchProducts'));
    }

    public function create()
    {
        $categories = MerchCategory::all();
        return view('admin.master.merchProduct.create', compact('categories'));
    }

    public function store(Request $request)
    {
        \DB::transaction(function () use ($request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
                'type' => 'required|in:normal,featured',
                'variants' => 'required|array',
                'variants.*.name' => 'required|string|max:255',
                'variants.*.images' => 'nullable|array',
                'variants.*.images.*.image_path' => 'nullable|file|image|max:2048',
                'variants.*.sizes' => 'nullable|array',
                'variants.*.sizes.*.size' => 'required_with:variants.*.sizes|string|max:50',
                'variants.*.sizes.*.stock' => 'nullable|integer|min:0',
                'variants.*.sizes.*.price' => 'nullable|numeric|min:0',
                'variants.*.sizes.*.discount' => 'nullable|numeric|min:0|max:100',
                'variants.*.stock' => 'nullable|integer|min:0',
                'variants.*.price' => 'nullable|numeric|min:0',
                'variants.*.discount' => 'nullable|numeric|min:0|max:100',
            ]);

            $data = $request->only(['name', 'description', 'status', 'type']);
            $data['slug'] = $this->generateUniqueSlug($request->name);

            $merchProduct = MerchProduct::create($data);

            if ($request->has('categories')) {
                $merchProduct->categories()->attach($request->categories);
            }

            $defaultVariantRaw = $request->input('default_variant');
            foreach ($request->variants as $variantIdx => $variantData) {
                $isDefault = $this->isDefaultSelection($defaultVariantRaw, null, $variantIdx) ? 1 : 0;

                // Cek apakah variant punya sizes
                $hasSizes = !empty($variantData['sizes']);

                $variant = $merchProduct->variants()->create([
                    'name' => $variantData['name'],
                    'code' => $variantData['code'] ?? null,
                    'is_default' => $isDefault,
                    'stock' => $hasSizes ? null : ($variantData['stock'] ?? 0),
                    'price' => $hasSizes ? null : ($variantData['price'] ?? null),
                    'discount' => $hasSizes ? null : ($variantData['discount'] ?? 0),
                ]);

                $this->syncImages($variant, $variantData['images'] ?? []);
                $this->syncSizes($variant, $variantData['sizes'] ?? []);
            }

            \Log::info('MerchProduct Store Success', [
                'product' => $merchProduct->load(['categories', 'variants.images', 'variants.sizes'])
            ]);

            $this->recomputeAndPersistAggregates($merchProduct->fresh(['variants.images', 'variants.sizes']));
        });

        return redirect()->route('master.merchProduct.index')->with('success', 'Product created!');
    }

    public function edit($id)
    {
        $merchProduct = MerchProduct::with([
            'categories',
            'variants.images',
            'variants.sizes'
        ])->findOrFail($id);

        $categories = MerchCategory::all();

        return view('admin.master.merchProduct.edit', compact('merchProduct', 'categories'));
    }

    public function update(Request $request, $id)
    {
        \DB::transaction(function () use ($request, $id) {
            $merchProduct = MerchProduct::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255|unique:merch_products,name,' . $merchProduct->id,
                'status' => 'required|in:active,inactive',
                'type' => 'required|in:normal,featured',
                'categories' => 'nullable|array',
                'categories.*' => 'exists:merch_categories,id',
                'default_variant' => 'required',
                'variants' => 'required|array',
                'variants.*.id' => 'nullable|exists:merch_product_variants,id',
                'variants.*.name' => 'required|string|max:255',
                'variants.*.images' => 'nullable|array',
                'variants.*.images.*.id' => 'nullable|exists:merch_product_variant_images,id',
                'variants.*.images.*.image_path' => 'nullable|file|image|max:2048',
                'variants.*.sizes' => 'nullable|array',
                'variants.*.sizes.*.id' => 'nullable|exists:merch_product_variant_sizes,id',
                'variants.*.sizes.*.size' => 'required_with:variants.*.sizes|string|max:50',
                'variants.*.sizes.*.stock' => 'nullable|integer|min:0',
                'variants.*.sizes.*.price' => 'nullable|numeric|min:0',
                'variants.*.sizes.*.discount' => 'nullable|numeric|min:0|max:100',
                'variants.*.stock' => 'nullable|integer|min:0',
                'variants.*.price' => 'nullable|numeric|min:0',
                'variants.*.discount' => 'nullable|numeric|min:0|max:100',
            ]);

            $data = $request->only(['name', 'description', 'status', 'type']);
            $data['slug'] = $this->generateUniqueSlug($request->name, $merchProduct->id);

            $merchProduct->update($data);

            if ($request->has('categories')) {
                $merchProduct->categories()->sync($request->categories);
            }

            $merchProduct->variants()->update(['is_default' => 0]);
            $defaultVariantRaw = $request->input('default_variant');

            $keptVariantIds = [];
            foreach ($request->variants as $variantIdx => $variantData) {
                $isDefault = $this->isDefaultSelection($defaultVariantRaw, $variantData['id'] ?? null, $variantIdx) ? 1 : 0;
                $hasSizes = !empty($variantData['sizes']);

                if (!empty($variantData['id'])) {
                    $variant = $merchProduct->variants()->where('id', $variantData['id'])->firstOrFail();
                    $variant->update([
                        'name' => $variantData['name'],
                        'code' => $variantData['code'] ?? null,
                        'is_default' => $isDefault,
                        'stock' => $hasSizes ? null : ($variantData['stock'] ?? 0),
                        'price' => $hasSizes ? null : ($variantData['price'] ?? null),
                        'discount' => $hasSizes ? null : ($variantData['discount'] ?? 0),
                    ]);
                } else {
                    $variant = $merchProduct->variants()->create([
                        'name' => $variantData['name'],
                        'code' => $variantData['code'] ?? null,
                        'is_default' => $isDefault,
                        'stock' => $hasSizes ? null : ($variantData['stock'] ?? 0),
                        'price' => $hasSizes ? null : ($variantData['price'] ?? null),
                        'discount' => $hasSizes ? null : ($variantData['discount'] ?? 0),
                    ]);
                }

                $keptVariantIds[] = $variant->id;

                $this->syncImages($variant, $variantData['images'] ?? []);
                $this->syncSizes($variant, $variantData['sizes'] ?? []);
            }

            $merchProduct->variants()->whereNotIn('id', $keptVariantIds)->delete();

            if (!$merchProduct->variants()->where('is_default', 1)->exists()) {
                $firstId = $merchProduct->variants()->whereIn('id', $keptVariantIds)->value('id');
                if ($firstId) {
                    $merchProduct->variants()->where('id', $firstId)->update(['is_default' => 1]);
                }
            }

            \Log::info('MerchProduct Update Success', [
                'product' => $merchProduct->load(['categories', 'variants.images', 'variants.sizes'])
            ]);

            $this->recomputeAndPersistAggregates($merchProduct->fresh(['variants.images', 'variants.sizes']));
        });

        return redirect()->route('master.merchProduct.index')->with('success', 'Product updated!');
    }

    public function destroy($id)
    {
        \DB::transaction(function () use ($id) {
            $product = MerchProduct::with([
                'categories',
                'variants.images',
                'variants.sizes'
            ])->findOrFail($id);

            // Detach categories
            $product->categories()->detach();

            // Hapus semua variant beserta images & sizes
            foreach ($product->variants as $variant) {
                // Hapus images
                $variant->images()->delete();
                // Hapus sizes
                $variant->sizes()->delete();
                // Hapus variant
                $variant->delete();
            }

            // Hapus produk utama
            $product->delete();
        });

        return redirect()->route('master.merchProduct.index')->with('success', 'Product deleted!');
    }

    private function syncImages($variant, $images)
    {
        $keptImageIds = [];
        foreach ($images as $img) {
            if (!empty($img['id'])) {
                $image = $variant->images()->where('id', $img['id'])->firstOrFail();
                if (isset($img['image_path']) && $img['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                    // Hapus file lama jika ada
                    if ($image->image_path && file_exists(public_path($image->image_path))) {
                        @unlink(public_path($image->image_path));
                    }
                    $uploader = new Uploads();
                    $path = $uploader->handleUpload($img['image_path']);
                    $image->update([
                        'image_path' => $path,
                        'label' => $img['label'] ?? null,
                    ]);
                } else {
                    $image->update([
                        'label' => $img['label'] ?? null,
                    ]);
                }
                $keptImageIds[] = $image->id;
            } elseif (isset($img['image_path']) && $img['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                $uploader = new Uploads();
                $path = $uploader->handleUpload($img['image_path']);
                $newImage = $variant->images()->create([
                    'image_path' => $path,
                    'label' => $img['label'] ?? null,
                ]);
                $keptImageIds[] = $newImage->id;
            }
        }

        // Hapus file fisik untuk image yang dihapus dari database
        $deletedImages = $variant->images()->whereNotIn('id', $keptImageIds)->get();
        foreach ($deletedImages as $deletedImage) {
            if ($deletedImage->image_path && file_exists(public_path($deletedImage->image_path))) {
                @unlink(public_path($deletedImage->image_path));
            }
        }
        $variant->images()->whereNotIn('id', $keptImageIds)->delete();
    }

    private function syncSizes($variant, $sizes) 
    {
        $keptSizeIds = [];
        if (!empty($sizes)) {
            foreach ($sizes as $sz) {
                if (!empty($sz['id'])) {
                    $size = $variant->sizes()->where('id', $sz['id'])->firstOrFail();
                    $size->update([
                        'size' => $sz['size'],
                        'stock' => $sz['stock'] ?? 0,
                        'price' => $sz['price'] ?? null,
                        'discount' => $sz['discount'] ?? 0,
                    ]);
                    $keptSizeIds[] = $size->id;
                } else {
                    $newSize = $variant->sizes()->create([
                        'size' => $sz['size'],
                        'stock' => $sz['stock'] ?? 0,
                        'price' => $sz['price'] ?? null,
                        'discount' => $sz['discount'] ?? 0,
                    ]);
                    $keptSizeIds[] = $newSize->id;
                }
            }
            $variant->sizes()->whereNotIn('id', $keptSizeIds)->delete();
        } else {
            // Jika tidak ada sizes, hapus semua sizes milik variant ini
            $variant->sizes()->delete();
        }
    }

    /**
     * Determine if current variant should be marked as default.
     * $defaultRaw can be numeric id, numeric index, or string like 'new_#'.
     */
    private function isDefaultSelection($defaultRaw, $variantIdOrNull, int $variantIdx): bool
    {
        if (is_null($defaultRaw) || $defaultRaw === '') {
            return $variantIdx === 0; // fallback to first
        }

        // If raw equals existing id
        if (!empty($variantIdOrNull) && is_numeric($defaultRaw)) {
            return (int)$defaultRaw === (int)$variantIdOrNull;
        }

        // If raw provided as 'new_#'
        if (is_string($defaultRaw) && Str::startsWith($defaultRaw, 'new_')) {
            return $defaultRaw === ('new_' . $variantIdx);
        }

        // If raw numeric and refers to index (create flow)
        if (is_numeric($defaultRaw)) {
            return (int)$defaultRaw === (int)$variantIdx;
        }

        return false;
    }

    /**
     * Recompute and persist product-level aggregates for stock/price/discount/image.
     */
    private function recomputeAndPersistAggregates(MerchProduct $product): void
    {
        $product->loadMissing(['variants.sizes', 'variants.images']);

        $allSizes = $product->variants->flatMap(function ($v) {
            return $v->sizes;
        });

        $totalStock = (int) $allSizes->sum(function ($s) {
            return (int)($s->stock ?? 0);
        });

        // Choose a display price: the minimum price across all sizes (if any)
        $price = $allSizes->pluck('price')->filter(function ($p) {
            return !is_null($p) && $p !== '';
        })->map(function ($p) {
            return (float)$p;
        })->min();

        // Compute discount: if multiple variants, use the smallest discount among variants (each variant discount = min of its sizes)
        $variantDiscounts = $product->variants->map(function ($v) {
            return $v->sizes->pluck('discount')->filter(function ($d) {
                return !is_null($d) && $d !== '';
            })->map(function ($d) {
                return (float)$d;
            })->min();
        })->filter(function ($d) {
            return !is_null($d);
        });
        $discount = $variantDiscounts->count() ? $variantDiscounts->min() : 0;

        // Pick display image from default variant if available
        $defaultVariant = $product->variants->firstWhere('is_default', 1) ?: $product->variants->first();
        $displayImage = $defaultVariant && $defaultVariant->images->count() ? $defaultVariant->images->first()->image_path : null;

        $updates = ['stock' => $totalStock];
        if (Schema::hasColumn('merch_products', 'price') && !is_null($price)) {
            $updates['price'] = $price;
        }
        if (Schema::hasColumn('merch_products', 'discount')) {
            $updates['discount'] = $discount ?? 0;
        }
        // Try to persist display image if the column exists
        if (!is_null($displayImage)) {
            if (Schema::hasColumn('merch_products', 'image')) {
                $updates['image'] = $displayImage;
            } elseif (Schema::hasColumn('merch_products', 'image_path')) {
                $updates['image_path'] = $displayImage;
            }
        }

        if (!empty($updates)) {
            $product->update($updates);
        }
    }

    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $counter = 1;

        while (MerchProduct::where('slug', $slug)->when($excludeId, function ($query) use ($excludeId) {
            return $query->where('id', '!=', $excludeId);
        })->exists()) {
            $slug = Str::slug($name) . '-' . $counter++;
        }

        return $slug;
    }
}