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
        // Jaga-jaga jika view mengirim 'selected_variant_id' atau 'variant_id'
        $inputVariantId = $request->input('variant_id') ?? $request->input('selected_variant_id');
        $inputSizeId    = $request->input('size_id') ?? $request->input('selected_size_id');
        
        // Merge kembali ke request agar validasi berjalan mulus
        $request->merge([
            'variant_id' => $inputVariantId,
            'size_id'    => $inputSizeId
        ]);

        // 2. Validasi
        $request->validate([
            'product_id' => 'required|exists:merch_products,id',
            'variant_id' => 'required|exists:merch_product_variants,id',
            'size_id'    => 'nullable', // Boleh null
            'quantity'   => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $qty = (int) $request->input('quantity', 1);
        
        // 3. Ambil Data Harga & Stok
        $variant = MerchProductVariant::findOrFail($request->variant_id);
        
        // Cek size valid jika ada ID-nya
        $size = null;
        if ($request->size_id) {
            $size = MerchProductVariantSize::find($request->size_id);
        }

        $finalPrice = $size ? $size->price : $variant->price;
        $availableStock = $size ? $size->stock : $variant->stock;

        // 4. Cek Stok Awal
        if ($qty > $availableStock) {
            return back()->with('error', "Stok tidak mencukupi. Tersisa: {$availableStock}");
        }

        // 5. Cek Duplikasi Item
        $existingItem = CartItem::where('user_id', $user->id)
            ->where('merch_product_id', $request->product_id)
            ->where('merch_product_variant_id', $request->variant_id)
            ->where('merch_product_variant_size_id', $request->size_id) // Bisa null
            ->first();

        if ($existingItem) {
            $newTotalQty = $existingItem->quantity + $qty;
            if ($newTotalQty > $availableStock) {
                return back()->with('error', "Total pesanan melebihi stok ({$availableStock}).");
            }
            $existingItem->quantity = $newTotalQty;
            $existingItem->price = $finalPrice;
            $existingItem->save();
        } else {
            CartItem::create([
                'user_id' => $user->id,
                'merch_product_id' => $request->product_id,
                'merch_product_variant_id' => $request->variant_id,
                'merch_product_variant_size_id' => $request->size_id, // Bisa null
                'quantity' => $qty,
                'price' => $finalPrice,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $newQuantity = (int)$request->input('quantity');
        if ($newQuantity < 1) $newQuantity = 1;

        // Cek Stok Maksimal
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
            ], 422);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'newQuantity' => $cartItem->quantity
        ]);
    }

    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) abort(403);
        
        $cartItem->delete();
        
        return redirect()->route('cart.index')->with('success', 'Item dihapus.');
    }
}