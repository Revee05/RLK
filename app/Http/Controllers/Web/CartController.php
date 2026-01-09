<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CartItem;
use App\models\MerchProduct;
use App\models\MerchProductVariant;
use App\models\MerchProductVariantSize;
use Illuminate\Support\Facades\Log; // Pastikan ini ada

class CartController extends Controller
{
    // ==========================================
    // 1. HALAMAN KERANJANG (VIEW)
    // ==========================================
    public function index()
    {
        $cartItems = CartItem::with([
                'merchProduct.categories',
                'merchVariant.images',
                'merchSize',
                'auctionProduct',
                'auctionProduct.images'
            ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Log sederhana penanda user membuka keranjang
        // Log ini akan masuk ke storage/logs/laravel.log
        Log::info('User membuka keranjang', ['user_id' => Auth::id()]);

        return view('web.cart', compact('cartItems'));
    }

    // ==========================================
    // 2. TAMBAH KE KERANJANG (POST)
    // ==========================================
    public function addMerchToCart(Request $request)
    {
        // Normalisasi Input
        $inputVariantId = $request->input('variant_id') ?? $request->input('selected_variant_id');
        $inputSizeId    = $request->input('size_id') ?? $request->input('selected_size_id');
        
        $request->merge([
            'variant_id' => $inputVariantId,
            'size_id'    => $inputSizeId
        ]);

        $request->validate([
            'merch_product_id' => 'required|exists:merch_products,id',
            'variant_id'       => 'required|exists:merch_product_variants,id',
            'size_id'          => 'nullable',
            'quantity'         => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $qty = (int) $request->input('quantity', 1);
        
        // Ambil Data Harga & Stok
        $variant = MerchProductVariant::findOrFail($request->variant_id);
        
        $size = null;
        if ($request->size_id) {
            $size = MerchProductVariantSize::find($request->size_id);
        }

        $finalPrice = $size ? $size->price : $variant->price;
        $availableStock = $size ? $size->stock : $variant->stock;

        // Cek Stok Awal
        if ($qty > $availableStock) {
            $msg = "Stok tidak mencukupi. Tersisa: {$availableStock}";
            
            // Log Error ke storage/logs
            Log::error('Gagal tambah ke cart: Stok kurang', [
                'user_id' => $user->id,
                'product_id' => $request->merch_product_id,
                'requested' => $qty,
                'available' => $availableStock
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        // Cek Duplikasi Item
        $existingItem = CartItem::where('user_id', $user->id)
            ->where('merch_product_id', $request->merch_product_id)
            ->where('merch_product_variant_id', $request->variant_id)
            ->where('merch_product_variant_size_id', $request->size_id)
            ->first();

        if ($existingItem) {
            $newTotalQty = $existingItem->quantity + $qty;
            if ($newTotalQty > $availableStock) {
                $msg = "Total pesanan melebihi stok ({$availableStock}).";

                Log::warning('Gagal update cart: Stok total berlebih', [
                    'user_id' => $user->id,
                    'current_qty' => $existingItem->quantity,
                    'add_qty' => $qty,
                    'max_stock' => $availableStock
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }

                return back()->with('error', $msg);
            }
            $existingItem->quantity = $newTotalQty;
            $existingItem->price = $finalPrice;
            $existingItem->save();
        } else {
            CartItem::create([
                'user_id' => $user->id,
                'merch_product_id' => $request->merch_product_id,
                'merch_product_variant_id' => $request->variant_id,
                'merch_product_variant_size_id' => $request->size_id,
                'quantity' => $qty,
                'price' => $finalPrice,
            ]);
        }

        // RESPON SUKSES & LOG
        Log::info('Berhasil tambah item ke keranjang', [
            'user_id' => $user->id,
            'product_id' => $request->merch_product_id,
            'qty' => $qty
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Produk berhasil ditambahkan ke keranjang.'
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    // ==========================================
    // 3. UPDATE QUANTITY (+/-) -> RETURN JSON
    // ==========================================
    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            Log::warning('Percobaan akses ilegal updateQuantity', ['user_id' => Auth::id(), 'cart_item_id' => $cartItem->id]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $newQuantity = (int)$request->input('quantity');
        if ($newQuantity < 1) $newQuantity = 1;

        // Cek Maksimal Stok yang tersedia
        $maxStock = 0;
        if ($cartItem->merch_product_variant_size_id && $cartItem->merchSize) {
            $maxStock = $cartItem->merchSize->stock;
        } elseif ($cartItem->merchVariant) {
            $maxStock = $cartItem->merchVariant->stock;
        } else {
            $maxStock = $cartItem->merchProduct->stock ?? 0;
        }

        // Jika request melebihi stok
        if ($newQuantity > $maxStock) {
            Log::info('Update quantity dibatasi stok', [
                'cart_item_id' => $cartItem->id,
                'requested' => $newQuantity,
                'max' => $maxStock
            ]);

            return response()->json([
                'success' => false,
                'message' => "Stok maksimal hanya {$maxStock}",
                'newQuantity' => $maxStock // Balikkan ke max agar input field terkoreksi
            ], 200); 
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        Log::info('Update quantity berhasil', ['cart_item_id' => $cartItem->id, 'new_qty' => $newQuantity]);

        return response()->json([
            'success' => true,
            'newQuantity' => $cartItem->quantity // Data ini diambil JS untuk update tampilan
        ]);
    }

    // ==========================================
    // 4. UPDATE OPTION (Varian/Size) -> RETURN JSON
    // ==========================================
    public function updateOption(Request $request, $id)
    {
        $cartItem = CartItem::find($id); 
        
        if (!$cartItem) {
            Log::error('Update option gagal: Item tidak ditemukan', ['id' => $id]);
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        // --- A. UPDATE DATABASE ---
        
        // 1. Jika Ganti Varian (Warna/Model)
        if ($request->has('variant_id')) {
            $newVariantId = $request->input('variant_id');
            $cartItem->merch_product_variant_id = $newVariantId;

            // Logic: Auto-select Size pertama dari varian baru
            $newVariant = MerchProductVariant::find($newVariantId);
            if ($newVariant && $newVariant->sizes->count() > 0) {
                $cartItem->merch_product_variant_size_id = $newVariant->sizes->first()->id;
            } else {
                $cartItem->merch_product_variant_size_id = null;
            }
        }

        // 2. Jika Ganti Size
        if ($request->has('size_id')) {
            $cartItem->merch_product_variant_size_id = $request->input('size_id');
        }

        $cartItem->save(); // Simpan perubahan

        // --- B. HITUNG DATA BARU ---
        $cartItem->refresh(); // Ambil data terbaru dari DB
        
        $currentProduct = $cartItem->merchProduct;
        $currentVariant = MerchProductVariant::find($cartItem->merch_product_variant_id);
        $currentSize    = MerchProductVariantSize::find($cartItem->merch_product_variant_size_id);

        // Hitung Harga (Prioritas: Size > Varian > Produk)
        $price = 0;
        if ($currentSize) {
            $price = $currentSize->price; 
        } elseif ($currentVariant) {
            $price = $currentVariant->price;
        } elseif ($currentProduct) {
            $price = $currentProduct->price;
        }

        // Update harga di DB Cart agar sinkron saat checkout
        $cartItem->price = $price;
        $cartItem->save();

        // Cari Gambar (Prioritas: Varian > Produk)
        $newImageUrl = 'https://via.placeholder.com/100';
        if ($currentVariant && $currentVariant->images->count() > 0) {
            $newImageUrl = asset($currentVariant->images->first()->image_path);
        } elseif ($currentProduct && $currentProduct->images->count() > 0) {
            $newImageUrl = asset($currentProduct->images->first()->image_path);
        }

        // Siapkan List Size Baru (untuk dropdown)
        $availableSizes = [];
        if ($currentVariant && $currentVariant->sizes) {
            foreach($currentVariant->sizes as $size) {
                $availableSizes[] = [
                    'id' => $size->id,
                    'size' => $size->size
                ];
            }
        }

        Log::info('Update opsi cart berhasil', ['cart_item_id' => $id]);

        // --- C. KIRIM JSON ---
        return response()->json([
            'success'       => true,
            'new_price'     => $price,
            'new_total'     => $price * $cartItem->quantity,
            'new_image'     => $newImageUrl,
            'new_size_id'   => $cartItem->merch_product_variant_size_id, 
            'available_sizes' => $availableSizes
        ]);

    }

    // ==========================================
    // 5. HAPUS ITEM -> RETURN JSON
    // ==========================================
    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            Log::warning('Percobaan hapus ilegal', ['user_id' => Auth::id(), 'cart_item_id' => $cartItem->id]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        
        $cartItem->delete();
        
        Log::info('Item dihapus dari keranjang', ['cart_item_id' => $cartItem->id, 'user_id' => Auth::id()]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus.'
            ]);
        }
        
        return back()->with('success', 'Item dihapus.');
    }
}