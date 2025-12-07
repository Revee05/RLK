<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CartItem;
use App\models\MerchProduct;
use App\models\MerchProductVariant;
use App\models\MerchProductVariantSize;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with([
                'merchProduct.categories',
                'merchVariant.images',
                'merchSize'
            ])
            ->where('user_id', Auth::id())
            ->whereNotNull('merch_product_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('web.cart', compact('cartItems'));
    }

    public function addMerchToCart(Request $request)
    {
        // 1. Normalisasi Input
        $inputVariantId = $request->input('variant_id') ?? $request->input('selected_variant_id');
        $inputSizeId    = $request->input('size_id') ?? $request->input('selected_size_id');
        
        $request->merge([
            'variant_id' => $inputVariantId,
            'size_id'    => $inputSizeId
        ]);

        // 2. Validasi (UPDATED: product_id diganti merch_product_id)
        $request->validate([
            'merch_product_id' => 'required|exists:merch_products,id', // <-- NAMA BARU
            'variant_id'       => 'required|exists:merch_product_variants,id',
            'size_id'          => 'nullable',
            'quantity'         => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $qty = (int) $request->input('quantity', 1);
        
        // 3. Ambil Data Harga & Stok
        $variant = MerchProductVariant::findOrFail($request->variant_id);
        
        $size = null;
        if ($request->size_id) {
            $size = MerchProductVariantSize::find($request->size_id);
        }

        $finalPrice = $size ? $size->price : $variant->price;
        $availableStock = $size ? $size->stock : $variant->stock;

        // 4. Cek Stok Awal
        if ($qty > $availableStock) {
            $msg = "Stok tidak mencukupi. Tersisa: {$availableStock}";
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // 5. Cek Duplikasi Item
        $existingItem = CartItem::where('user_id', $user->id)
            ->where('merch_product_id', $request->merch_product_id) // <-- NAMA BARU
            ->where('merch_product_variant_id', $request->variant_id)
            ->where('merch_product_variant_size_id', $request->size_id)
            ->first();

        if ($existingItem) {
            $newTotalQty = $existingItem->quantity + $qty;
            if ($newTotalQty > $availableStock) {
                $msg = "Total pesanan melebihi stok ({$availableStock}).";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }
            $existingItem->quantity = $newTotalQty;
            $existingItem->price = $finalPrice;
            $existingItem->save();
        } else {
            // 6. Simpan Item Baru
            CartItem::create([
                'user_id' => $user->id,
                'merch_product_id' => $request->merch_product_id, // <-- NAMA BARU (Simpan ke kolom merch_product_id)
                'merch_product_variant_id' => $request->variant_id,
                'merch_product_variant_size_id' => $request->size_id,
                'quantity' => $qty,
                'price' => $finalPrice,
            ]);
        }

        // RESPON SUKSES
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Produk berhasil ditambahkan ke keranjang.'
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $newQuantity = (int)$request->input('quantity');
        if ($newQuantity < 1) $newQuantity = 1;

        $maxStock = 0;
        if ($cartItem->merch_product_variant_size_id && $cartItem->merchSize) {
            $maxStock = $cartItem->merchSize->stock;
        } elseif ($cartItem->merchVariant) {
            $maxStock = $cartItem->merchVariant->stock;
        } else {
            $maxStock = $cartItem->merchProduct->stock ?? 0;
        }

        if ($newQuantity > $maxStock) {
            return response()->json([
                'success' => false,
                'message' => "Stok maksimal hanya {$maxStock}",
                'quantity' => $maxStock
            ], 200); 
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'newQuantity' => $cartItem->quantity
        ]);
    }


    public function updateOption(\Illuminate\Http\Request $request, $id)
    {
        $cartItem = \App\CartItem::find($id); 
        
        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        // --- LOGIC GANTI VARIAN ---
        if ($request->has('variant_id')) {
            $newVariantId = $request->input('variant_id');
            $cartItem->merch_product_variant_id = $newVariantId;

            // Reset Size ke size pertama dari varian baru
            $newVariant = \App\models\MerchProductVariant::find($newVariantId);
            
            if ($newVariant && $newVariant->sizes->count() > 0) {
                $cartItem->merch_product_variant_size_id = $newVariant->sizes->first()->id;
            } else {
                $cartItem->merch_product_variant_size_id = null;
            }
        }

        // --- LOGIC GANTI SIZE ---
        if ($request->has('size_id')) {
            $cartItem->merch_product_variant_size_id = $request->input('size_id');
        }

        $cartItem->save();

        // --- HITUNG DATA BARU UNTUK DIKIRIM KE FRONTEND ---
        
        // 1. Ambil Varian & Size Terbaru
        $currentVariant = \App\models\MerchProductVariant::find($cartItem->merch_product_variant_id);
        $currentSize    = \App\models\MerchProductVariantSize::find($cartItem->merch_product_variant_size_id);

        // 2. Tentukan Harga (Prioritas: Harga Size > Harga Varian > Harga Produk)
        // Sesuaikan dengan logic harga di aplikasi kamu
        $price = 0;
        if ($currentSize) {
            $price = $currentSize->price; 
        } elseif ($currentVariant) {
            $price = $currentVariant->price;
        } else {
            $price = $cartItem->merchProduct->price;
        }

        // 3. Update Harga di Cart Item (Opsional, jika kamu simpan harga statis di tabel cart)
        $cartItem->price = $price;
        $cartItem->save();

        // 4. Tentukan Gambar Baru
        $newImageUrl = 'https://via.placeholder.com/100'; // Default
        if ($currentVariant && $currentVariant->images && $currentVariant->images->count() > 0) {
            $newImageUrl = asset($currentVariant->images->first()->image_path);
        } elseif ($cartItem->merchProduct->images && $cartItem->merchProduct->images->count() > 0) {
            $newImageUrl = asset($cartItem->merchProduct->images->first()->image_path);
        }

        // 5. Siapkan List Size Baru (Jika varian berubah, list size di dropdown harus berubah)
        $availableSizes = [];
        if ($currentVariant && $currentVariant->sizes) {
            foreach($currentVariant->sizes as $size) {
                $availableSizes[] = [
                    'id' => $size->id,
                    'size' => $size->size
                ];
            }
        }

        return response()->json([
            'success' => true,
            'new_price' => $price,
            'new_total' => $price * $cartItem->quantity,
            'new_image' => $newImageUrl,
            'new_size_id' => $cartItem->merch_product_variant_size_id, // Size ID yang terpilih otomatis
            'available_sizes' => $availableSizes // List size baru untuk dropdown
        ]);
    }

    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        
        $cartItem->delete();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus.'
            ]);
        }
        
        return back()->with('success', 'Item dihapus.');
    }
}