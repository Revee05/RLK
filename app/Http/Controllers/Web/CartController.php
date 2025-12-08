<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CartItem;
use App\models\MerchProduct;
use App\models\MerchProductVariant;
use App\models\MerchProductVariantSize;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
                'auctionProduct' 
            ])
            ->where('user_id', Auth::id())
            // ->whereNotNull('merch_product_id') // <--- Baris ini sudah benar dikomentari
            ->orderBy('created_at', 'desc')
            ->get();

        // ======================================================
        // TAMBAHAN: LOG DATA CART (JSON FORMAT)
        // ======================================================
        // Ini akan mencatat data ke file: storage/logs/laravel.log
        \Illuminate\Support\Facades\Log::info('--- LOAD CART USER ID: ' . Auth::id() . ' ---');
        \Illuminate\Support\Facades\Log::info($cartItems->toJson(JSON_PRETTY_PRINT));

        // Opsi Alternatif: Jika ingin melihat JSON langsung di Browser (Debugging Cepat)
        // return response()->json($cartItems); 

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
            
            if ($request->ajax() || $request->wantsJson()) {
                $this->logResponse('addMerchToCart', [
                    'status' => 'error',
                    'reason' => 'stock_insufficient',
                    'message' => $msg,
                    'availableStock' => $availableStock,
                    'requestedQty' => $qty,
                    'merch_product_id' => $request->merch_product_id,
                    'variant_id' => $request->variant_id,
                    'size_id' => $request->size_id,
                ]);

                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            $this->logResponse('addMerchToCart', [
                'status' => 'error',
                'reason' => 'stock_insufficient',
                'message' => $msg,
                'availableStock' => $availableStock,
                'requestedQty' => $qty,
                'merch_product_id' => $request->merch_product_id,
                'variant_id' => $request->variant_id,
                'size_id' => $request->size_id,
            ]);

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
                if ($request->ajax() || $request->wantsJson()) {
                    $this->logResponse('addMerchToCart', [
                        'status' => 'error',
                        'reason' => 'stock_exceeded_total',
                        'message' => $msg,
                        'availableStock' => $availableStock,
                        'existingQty' => $existingItem->quantity,
                        'requestedQty' => $qty,
                        'merch_product_id' => $request->merch_product_id,
                    ]);

                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                $this->logResponse('addMerchToCart', [
                    'status' => 'error',
                    'reason' => 'stock_exceeded_total',
                    'message' => $msg,
                    'availableStock' => $availableStock,
                    'existingQty' => $existingItem->quantity,
                    'requestedQty' => $qty,
                    'merch_product_id' => $request->merch_product_id,
                ]);

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

        // RESPON SUKSES
        $this->logResponse('addMerchToCart', [
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'merch_product_id' => $request->merch_product_id,
            'variant_id' => $request->variant_id,
            'size_id' => $request->size_id,
            'quantity' => $qty,
            'price' => $finalPrice,
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
            $this->logResponse('updateQuantity', [
                'status' => 'error',
                'reason' => 'unauthorized',
                'cart_item_id' => $cartItem->id,
            ]);

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
            $this->logResponse('updateQuantity', [
                'status' => 'error',
                'reason' => 'stock_exceeded',
                'maxStock' => $maxStock,
                'requested' => $newQuantity,
                'cart_item_id' => $cartItem->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => "Stok maksimal hanya {$maxStock}",
                'newQuantity' => $maxStock // Balikkan ke max agar input field terkoreksi
            ], 200); 
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        $this->logResponse('updateQuantity', [
            'status' => 'success',
            'newQuantity' => $cartItem->quantity,
            'cart_item_id' => $cartItem->id,
        ]);

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
            $this->logResponse('updateOption', [
                'status' => 'error',
                'reason' => 'not_found',
                'id' => $id,
            ]);

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
            if (request()->ajax() || request()->wantsJson()) {
                $this->logResponse('destroy', [
                    'status' => 'error',
                    'reason' => 'unauthorized',
                    'cart_item_id' => $cartItem->id,
                ]);

                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            $this->logResponse('destroy', [
                'status' => 'error',
                'reason' => 'unauthorized',
                'cart_item_id' => $cartItem->id,
            ]);

            abort(403);
        }
        
        $cartItem->delete();
        
        $this->logResponse('destroy', [
            'status' => 'success',
            'message' => 'Item dihapus',
            'cart_item_id' => $cartItem->id,
        ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus.'
            ]);
        }
        
        return back()->with('success', 'Item dihapus.');
    }

    /**
     * Conditional logger for local/development environment.
     *
     * @param string $method
     * @param array $data
     * @return void
     */
    private function logResponse(string $method, array $data = [])
    {
        if (app()->environment('local') || app()->environment('development')) {
            $payload = array_merge([
                'method' => $method,
                'env' => app()->environment(),
                'timestamp' => now()->toDateTimeString(),
            ], $data);

            Log::info('CartController response', $payload);
        }
    }
}