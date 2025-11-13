<?php
namespace App\Http\Controllers\MerchController;

use App\Http\Controllers\Controller;
use App\models\MerchProduct;
use App\models\MerchCategory;
use App\models\MerchProductImage;
use App\Product\Uploads;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchProductController extends Controller
{
    public function index()
    {
        $merchProducts = MerchProduct::with(['categories', 'images'])->get();
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
                'name' => 'required',
                'price' => 'required|integer',
                'stock' => 'required|integer',
                'discount' => 'nullable|integer',
                'status' => 'required',
                'categories' => 'nullable|array',
                'images.*' => 'nullable|image|max:2048',
            ]);

            $data = $request->only(['name', 'description', 'price', 'stock', 'status', 'discount']);
            $data['slug'] = Str::slug($request->name);

            $merchProduct = MerchProduct::create($data);

            // Attach categories
            if ($request->has('categories')) {
                $merchProduct->categories()->sync($request->categories);
            }

            // Handle multi image upload with Uploads class
            if ($request->hasFile('images')) {
                $uploader = new Uploads();
                foreach ($request->file('images') as $img) {
                    $path = $uploader->handleUploadProduct($img);
                    MerchProductImage::create([
                        'merch_product_id' => $merchProduct->id,
                        'image_path' => $path,
                    ]);
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
        $merchProduct = MerchProduct::with(['categories', 'images'])->findOrFail($id);
        $categories = MerchCategory::all();
        return view('admin.master.merchProduct.edit', compact('merchProduct', 'categories'));
    }

    public function update(Request $request, $id)
    {
        try {
            $merchProduct = MerchProduct::findOrFail($id);

            $request->validate([
                'name' => 'required',
                'price' => 'required|integer',
                'stock' => 'required|integer',
                'discount' => 'nullable|integer',
                'status' => 'required',
                'categories' => 'nullable|array',
                'images.*' => 'nullable|image|max:2048',
            ]);

            $data = $request->only(['name', 'description', 'price', 'stock', 'status', 'discount']);
            $data['slug'] = Str::slug($request->name);

            $merchProduct->update($data);

            // Sync categories
            if ($request->has('categories')) {
                $merchProduct->categories()->sync($request->categories);
            }

            // Handle new images with Uploads class
            if ($request->hasFile('images')) {
                $uploader = new Uploads();
                foreach ($request->file('images') as $img) {
                    $path = $uploader->handleUploadProduct($img);
                    MerchProductImage::create([
                        'merch_product_id' => $merchProduct->id,
                        'image_path' => $path,
                    ]);
                }
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
            $merchProduct->images()->delete();
            $merchProduct->categories()->detach();
            $merchProduct->delete();
            return redirect()->route('master.merchProduct.index')->with('success', 'Product deleted!');
        } catch (\Exception $e) {
            \Log::error('MerchProduct Delete Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to delete product. Please check the log for details.');
        }
    }
}