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
    /* --------------------------------- Public --------------------------------- */
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
            $request->validate($this->storeValidationRules());

            // Debug: Log request variants data
            // \Log::info('Store Request Variants Data', ['variants' => $request->variants]);

            $merchProduct = MerchProduct::create($this->buildProductPayload($request));
            $this->syncCategories($merchProduct, $request->get('categories', []), false);

            $defaultVariantRaw = $request->input('default_variant');
            foreach ($request->variants as $variantIdx => $variantData) {
                $this->upsertVariant($merchProduct, $variantData, $variantIdx, $defaultVariantRaw);
            }

            \Log::info('MerchProduct Store Success', [
                'product' => $merchProduct->load(['categories', 'variants.images', 'variants.sizes'])
            ]);

            $this->recomputeAndPersistAggregates($merchProduct->fresh(['variants.images', 'variants.sizes']));
        });

        return redirect()->route('master.merchProduct.index')->with('success', 'Produk merchandise berhasil ditambahkan!');
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
            $request->validate($this->updateValidationRules($merchProduct->id));

            // Debug: Log request variants data
            // \Log::info('Update Request Variants Data', ['variants' => $request->variants]);

            $merchProduct->update($this->buildProductPayload($request, $merchProduct->id));
            $this->syncCategories($merchProduct, $request->get('categories', []), true);

            // Reset default flags before processing incoming variants
            $merchProduct->variants()->update(['is_default' => 0]);
            $defaultVariantRaw = $request->input('default_variant');

            $keptVariantIds = [];
            foreach ($request->variants as $variantIdx => $variantData) {
                $variant = $this->upsertVariant($merchProduct, $variantData, $variantIdx, $defaultVariantRaw);
                $keptVariantIds[] = $variant->id;
            }

            // Remove variants not present in request
            $merchProduct->variants()->whereNotIn('id', $keptVariantIds)->delete();

            // Ensure at least one default variant remains
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

        return redirect()->route('master.merchProduct.index')->with('success', 'Produk merchandise berhasil diperbarui!');
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
            
            // Delete variants and their dependencies
            $product->variants->each(function ($variant) {
                // Delete image files from storage and database
                $variant->images->each(function ($image) {
                    if ($image->image_path && file_exists(public_path($image->image_path))) {
                        @unlink(public_path($image->image_path));
                    }
                });
                $variant->images()->delete();
                
                // Delete sizes
                $variant->sizes()->delete();
                
                // Delete variant
                $variant->delete();
            });
            
            // Delete product
            $product->delete();
        });

        return redirect()->route('master.merchProduct.index')->with('success', 'Produk merchandise berhasil dihapus!');
    }
    /* ------------------------------- Variants Ops ------------------------------ */
    private function upsertVariant(MerchProduct $product, array $variantData, int $idx, $defaultRaw)
    {
        $isDefault = $this->isDefaultSelection($defaultRaw, $variantData['id'] ?? null, $idx) ? 1 : 0;
        $hasSizes = !empty($variantData['sizes']);

        $payload = [
            'name' => $variantData['name'],
            'code' => $variantData['code'] ?? null,
            'is_default' => $isDefault,
            'stock' => $hasSizes ? null : ($variantData['stock'] ?? 0),
            'price' => $hasSizes ? null : ($variantData['price'] ?? null),
            'discount' => $hasSizes ? null : ($variantData['discount'] ?? 0),
            'weight' => $variantData['weight'] ?? null,
        ];

        // Debug: Log variant data and payload
        // \Log::info('Upsert Variant', [
        //     'variantData' => $variantData,
        //     'payload' => $payload,
        //     'weight_exists' => isset($variantData['weight']),
        //     'weight_value' => $variantData['weight'] ?? 'NOT SET'
        // ]);

        $variant = !empty($variantData['id'])
            ? tap($product->variants()->where('id', $variantData['id'])->firstOrFail())->update($payload)
            : $product->variants()->create($payload);

        $this->syncImages($variant, $variantData['images'] ?? []);
        $this->syncSizes($variant, $variantData['sizes'] ?? []);

        return $variant;
    }

    /* ------------------------------ Attach/Sync Cat ----------------------------- */
    private function syncCategories(MerchProduct $product, array $categoryIds, bool $sync): void
    {
        if (empty($categoryIds)) {
            return; // do nothing if none provided
        }
        $sync ? $product->categories()->sync($categoryIds) : $product->categories()->attach($categoryIds);
    }

    /* -------------------------------- Upload/Image ------------------------------ */
    private function syncImages($variant, $images): void
    {
        $kept = [];
        foreach ($images as $img) {
            $file = $img['image_path'] ?? null;
            if (!empty($img['id'])) {
                $image = $variant->images()->where('id', $img['id'])->firstOrFail();
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    if ($image->image_path && file_exists(public_path($image->image_path))) {
                        @unlink(public_path($image->image_path));
                    }
                    $path = (new Uploads())->handleUpload($file);
                    $image->update(['image_path' => $path, 'label' => $img['label'] ?? null]);
                } else {
                    $image->update(['label' => $img['label'] ?? null]);
                }
                $kept[] = $image->id;
                continue;
            }
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $path = (new Uploads())->handleUpload($file);
                $new = $variant->images()->create([
                    'image_path' => $path,
                    'label' => $img['label'] ?? null,
                ]);
                $kept[] = $new->id;
            }
        }

        $variant->images()->whereNotIn('id', $kept)->get()->each(function ($deleted) {
            if ($deleted->image_path && file_exists(public_path($deleted->image_path))) {
                @unlink(public_path($deleted->image_path));
            }
        });
        $variant->images()->whereNotIn('id', $kept)->delete();
    }

    private function syncSizes($variant, $sizes): void
    {
        if (empty($sizes)) {
            $variant->sizes()->delete();
            return;
        }
        $kept = [];
        foreach ($sizes as $sz) {
            $payload = [
                'size' => $sz['size'],
                'stock' => $sz['stock'] ?? 0,
                'price' => $sz['price'] ?? null,
                'discount' => $sz['discount'] ?? 0,
            ];
            $sizeModel = !empty($sz['id'])
                ? tap($variant->sizes()->where('id', $sz['id'])->firstOrFail())->update($payload)
                : $variant->sizes()->create($payload);
            $kept[] = $sizeModel->id;
        }
        $variant->sizes()->whereNotIn('id', $kept)->delete();
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
        $allSizes = $product->variants->flatMap(fn($v) => $v->sizes);
        $totalStock = (int) $allSizes->sum(fn($s) => (int)($s->stock ?? 0));
        $price = $allSizes->pluck('price')
            ->filter(fn($p) => !is_null($p) && $p !== '')
            ->map(fn($p) => (float)$p)
            ->min();

        $variantDiscounts = $product->variants->map(function ($v) {
            return $v->sizes->pluck('discount')
                ->filter(fn($d) => !is_null($d) && $d !== '')
                ->map(fn($d) => (float)$d)
                ->min();
        })->filter(fn($d) => !is_null($d));
        $discount = $variantDiscounts->count() ? $variantDiscounts->min() : 0;

        $defaultVariant = $product->variants->firstWhere('is_default', 1) ?: $product->variants->first();
        $displayImage = $defaultVariant && $defaultVariant->images->count() ? $defaultVariant->images->first()->image_path : null;

        $updates = ['stock' => $totalStock];
        if (Schema::hasColumn('merch_products', 'price') && !is_null($price)) $updates['price'] = $price;
        if (Schema::hasColumn('merch_products', 'discount')) $updates['discount'] = $discount ?? 0;
        if (!is_null($displayImage)) {
            if (Schema::hasColumn('merch_products', 'image')) $updates['image'] = $displayImage;
            elseif (Schema::hasColumn('merch_products', 'image_path')) $updates['image_path'] = $displayImage;
        }
        if ($updates) $product->update($updates);
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

    /* --------------------------------- Validation -------------------------------- */
    private function storeValidationRules(): array
    {
        return array_merge($this->baseProductRules(), $this->variantRules(false));
    }

    private function updateValidationRules(int $productId): array
    {
        $rules = $this->baseProductRules($productId);
        $rules['default_variant'] = 'required';
        return array_merge($rules, $this->variantRules(true));
    }

    private function baseProductRules(int $excludeId = null): array
    {
        $uniqueName = 'required|string|max:255';
        if (!is_null($excludeId)) {
            $uniqueName .= '|unique:merch_products,name,' . $excludeId;
        }
        return [
            'name' => $uniqueName,
            'status' => 'required|in:active,inactive',
            'type' => 'required|in:normal,featured',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:merch_categories,id',
            'variants' => 'required|array',
        ];
    }

    private function variantRules(bool $updating): array
    {
        return [
            'variants.*.id' => $updating ? 'nullable|exists:merch_product_variants,id' : 'nullable',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.images' => 'nullable|array',
            'variants.*.images.*.id' => $updating ? 'nullable|exists:merch_product_variant_images,id' : 'nullable',
            'variants.*.images.*.image_path' => 'nullable|file|image|max:2048',
            'variants.*.sizes' => 'nullable|array',
            'variants.*.sizes.*.id' => $updating ? 'nullable|exists:merch_product_variant_sizes,id' : 'nullable',
            'variants.*.sizes.*.size' => 'required_with:variants.*.sizes|string|max:50',
            'variants.*.sizes.*.stock' => 'nullable|integer|min:0',
            'variants.*.sizes.*.price' => 'nullable|numeric|min:0',
            'variants.*.sizes.*.discount' => 'nullable|numeric|min:0|max:100',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.discount' => 'nullable|numeric|min:0|max:100',
            'variants.*.weight' => 'required|numeric|min:0',
        ];
    }

    /* ---------------------------------- Payload ---------------------------------- */
    private function buildProductPayload(Request $request, $excludeId = null): array
    {
        $data = $request->only(['name', 'description', 'status', 'type']);
        $data['slug'] = $this->generateUniqueSlug($request->name, $excludeId);
        return $data;
    }
}