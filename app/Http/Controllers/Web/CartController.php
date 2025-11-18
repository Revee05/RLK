<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CartItem;
use App\Product;
use App\models\MerchProduct;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // PERUBAHAN: Hanya memuat relasi merchProduct
        // Kita juga tambahkan whereNotNull agar item lelang lama (jika ada) tidak ikut terambil
        $cartItems = CartItem::with([
                                'merchProduct.images',
                                'merchProduct.categories' 
                             ])
                             ->where('user_id', Auth::id())
                             ->whereNotNull('merch_product_id') // Hanya ambil Merch
                             ->get();

        return view('web.cart', compact('cartItems'));
    }

    public function addMerchToCart(Request $request, $merchProductId)
    {
        $merchProduct = MerchProduct::findOrFail($merchProductId);
        $user = Auth::user();
        
        $existingItem = CartItem::where('user_id', $user->id)
                                ->where('merch_product_id', $merchProduct->id)
                                ->first();

        if ($existingItem) {
            $existingItem->quantity += $request->input('quantity', 1);
            $existingItem->save();
        } else {
            CartItem::create([
                'user_id' => $user->id,
                // 'product_id' tidak perlu diisi (otomatis null di database)
                'merch_product_id' => $merchProduct->id, 
                'quantity' => $request->input('quantity', 1),
                'price' => $merchProduct->price,
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', 'Merchandise ditambahkan.');
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $newQuantity = (int)$request->input('quantity');
        if ($newQuantity < 1) $newQuantity = 1;

        // Cek Stok Merch (Langsung cek, tanpa if/else lelang)
        if ($cartItem->merch_product_id) {
            $cartItem->load('merchProduct'); 
            $stock = $cartItem->merchProduct->stock;
            
            if ($newQuantity > $stock) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Stok tidak mencukupi (sisa ' . $stock . ')',
                    'quantity' => $stock 
                ], 422);
            }
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
        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus.');
    }
}
