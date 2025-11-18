<?php
namespace App\Http\Controllers\MerchController;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;
use App\models\MerchCategory;
use App\models\MerchProductVariant;
use App\models\MerchProductVariantImage;
use App\models\MerchProductVariantSize;
use App\Product\Uploads;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchProductController extends Controller
{
    public function index()
    {
        $merchProducts = MerchProduct::with([
            'categories',
            'variants.images',
            'variants.sizes'
        ])->get();

        return view('admin.master.merchProduct.index', compact('merchProducts'));
    }

    public function create()
    {
        $categories = MerchCategory::all();
        return view('admin.master.merchProduct.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer',
                'stock' => 'required|integer',
                'status' => 'required|in:active,inactive',
                'type' => 'required|in:normal,featured',
                'discount' => 'nullable|integer',
                'variants' => 'array',
                'variants.*.name' => 'required|string|max:255',
                'variants.*.code' => 'nullable|string|max:255',
                'variants.*.is_default' => 'nullable|boolean',
                'variants.*.sizes' => 'array',
                'variants.*.sizes.*.size' => 'required|string|max:50',
                'variants.*.sizes.*.stock' => 'nullable|integer',
                'variants.*.sizes.*.price' => 'nullable|integer',
                'variants.*.sizes.*.discount' => 'nullable|integer',
                'variants.*.images' => 'array',
                'variants.*.images.*.image_path' => 'required|file|image|max:2048',
                'variants.*.images.*.label' => 'nullable|string|max:255',
            ]);

            $data = $request->only(['name', 'description', 'price', 'stock', 'status', 'discount', 'type']);
            $data['slug'] = Str::slug($request->name);

            $merchProduct = MerchProduct::create($data);

            if ($request->has('categories')) {
                $merchProduct->categories()->attach($request->categories);
            }

            // Tentukan variant default dari input radio (misal: name="default_variant" value=idx)
            $defaultVariantIdx = $request->input('default_variant');

            if ($request->filled('variants')) {
                foreach ($request->variants as $variantIdx => $variantData) {
                    $isDefault = ($variantIdx == $defaultVariantIdx) ? 1 : 0;

                    $variant = $merchProduct->variants()->create([
                        'name' => $variantData['name'],
                        'code' => $variantData['code'] ?? null,
                        'is_default' => $isDefault,
                    ]);

                    // Handle images upload
                    if (!empty($variantData['images'])) {
                        foreach ($variantData['images'] as $imgIdx => $img) {
                            if (isset($img['image_path']) && $img['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                                $uploader = new Uploads();
                                $path = $uploader->handleUpload($img['image_path']);
                            } else {
                                $path = $img['image_path'] ?? null;
                            }
                            $variant->images()->create([
                                'image_path' => $path,
                                'label' => $img['label'] ?? null,
                            ]);
                        }
                    }

                    // Handle sizes
                    if (!empty($variantData['sizes'])) {
                        foreach ($variantData['sizes'] as $size) {
                            $variant->sizes()->create([
                                'size' => $size['size'],
                                'stock' => $size['stock'] ?? 0,
                                'price' => $size['price'] ?? null,
                                'discount' => $size['discount'] ?? 0,
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('master.merchProduct.index')->with('success', 'Product created!');
        } catch (\Exception $e) {
            \Log::error('MerchProduct Create Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to create product. Please check the log for details.');
        }
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
        try {
            $merchProduct = MerchProduct::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer',
                'stock' => 'required|integer',
                'status' => 'required|in:active,inactive',
                'type' => 'required|in:normal,featured',
                'discount' => 'nullable|integer',
                'variants' => 'array',
                'variants.*.id' => 'nullable|integer',
                'variants.*.name' => 'required|string|max:255',
                'variants.*.code' => 'nullable|string|max:255',
                'variants.*.is_default' => 'nullable|boolean',
                'variants.*.sizes' => 'array',
                'variants.*.sizes.*.id' => 'nullable|integer',
                'variants.*.sizes.*.size' => 'required|string|max:50',
                'variants.*.sizes.*.stock' => 'nullable|integer',
                'variants.*.sizes.*.price' => 'nullable|integer',
                'variants.*.sizes.*.discount' => 'nullable|integer',
                'variants.*.images' => 'array',
                'variants.*.images.*.id' => 'nullable|integer',
                'variants.*.images.*.image_path' => 'nullable|file|image|max:2048',
                'variants.*.images.*.label' => 'nullable|string|max:255',
            ]);

            $data = $request->only(['name', 'description', 'price', 'stock', 'status', 'discount', 'type']);
            $data['slug'] = Str::slug($request->name);

            $merchProduct->update($data);

            if ($request->has('categories')) {
                $merchProduct->categories()->sync($request->categories);
            }

            // Reset semua is_default ke 0 dulu
            $merchProduct->variants()->update(['is_default' => 0]);
            $defaultVariantIdx = $request->input('default_variant');

            // Sync variants, images, sizes
            $keptVariantIds = [];
            foreach ((array) $request->variants as $variantIdx => $variantData) {
                $isDefault = ($variantIdx == $defaultVariantIdx) ? 1 : 0;

                if (!empty($variantData['id'])) {
                    $variant = $merchProduct->variants()->where('id', $variantData['id'])->firstOrFail();
                    $variant->update([
                        'name' => $variantData['name'],
                        'code' => $variantData['code'] ?? null,
                        'is_default' => $isDefault,
                    ]);
                } else {
                    $variant = $merchProduct->variants()->create([
                        'name' => $variantData['name'],
                        'code' => $variantData['code'] ?? null,
                        'is_default' => $isDefault,
                    ]);
                }

                $keptVariantIds[] = $variant->id;

                // images
                $keptImageIds = [];
                foreach ((array) ($variantData['images'] ?? []) as $imgIdx => $img) {
                    if (!empty($img['id'])) {
                        $image = $variant->images()->where('id', $img['id'])->firstOrFail();
                        // Jika ada file baru, upload dan replace
                        if (isset($img['image_path']) && $img['image_path'] instanceof \Illuminate\Http\UploadedFile) {
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
                    } else {
                        if (isset($img['image_path']) && $img['image_path'] instanceof \Illuminate\Http\UploadedFile) {
                            $uploader = new Uploads();
                            $path = $uploader->handleUpload($img['image_path']);
                        } else {
                            $path = $img['image_path'] ?? null;
                        }
                        $image = $variant->images()->create([
                            'image_path' => $path,
                            'label' => $img['label'] ?? null,
                        ]);
                    }
                    $keptImageIds[] = $image->id;
                }
                if (!empty($keptImageIds)) {
                    $variant->images()->whereNotIn('id', $keptImageIds)->delete();
                }

                // sizes
                $keptSizeIds = [];
                foreach ((array) ($variantData['sizes'] ?? []) as $szIdx => $sz) {
                    if (!empty($sz['id'])) {
                        $size = $variant->sizes()->where('id', $sz['id'])->firstOrFail();
                        $size->update([
                            'size' => $sz['size'],
                            'stock' => $sz['stock'] ?? 0,
                            'price' => $sz['price'] ?? null,
                            'discount' => $sz['discount'] ?? 0,
                        ]);
                    } else {
                        $size = $variant->sizes()->create([
                            'size' => $sz['size'],
                            'stock' => $sz['stock'] ?? 0,
                            'price' => $sz['price'] ?? null,
                            'discount' => $sz['discount'] ?? 0,
                        ]);
                    }
                    $keptSizeIds[] = $size->id;
                }
                if (!empty($keptSizeIds)) {
                    $variant->sizes()->whereNotIn('id', $keptSizeIds)->delete();
                }
            }

            if (!empty($keptVariantIds)) {
                $merchProduct->variants()->whereNotIn('id', $keptVariantIds)->delete();
            }

            return redirect()->route('master.merchProduct.index')->with('success', 'Product updated!');
        } catch (\Exception $e) {
            \Log::error('MerchProduct Update Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update product. Please check the log for details.');
        }
    }

    public function destroy($id)
    {
        try {
            $merchProduct = MerchProduct::findOrFail($id);

            // detach categories; variants + children akan terhapus via cascade
            $merchProduct->categories()->detach();
            $merchProduct->delete();

            return redirect()->route('master.merchProduct.index')->with('success', 'Product deleted!');
        } catch (\Exception $e) {
            \Log::error('MerchProduct Delete Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to delete product. Please check the log for details.');
        }
    }
}