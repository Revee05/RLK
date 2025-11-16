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
        $cartItems = CartItem::with([
                             'product.images',         
                             'product.kategori',       
                             'merchProduct.images',    
                             'merchProduct.categories' 
                         ])
                         ->where('user_id', Auth::id())
                         ->get();

        return view('web.cart', compact('cartItems'));
    }

    // Anda juga butuh fungsi untuk memasukkan data
    // saat lelang berakhir (ini hanya CONTOH)
    // Ini dipanggil oleh sistem Anda saat lelang ditutup
    public function processAuctionWinner($productId, $userId, $winningPrice)
    {
        // Cek dulu
        $existing = CartItem::where('user_id', $userId)->where('products_id', $productId)->first();

        if (!$existing) {
             CartItem::create([
                'user_id' => $userId,
                'products_id' => $productId,
                'quantity' => 1,
                'price' => $winningPrice // Harga final dari tabel 'bid'
            ]);
        }
    }

    public function addMerchToCart(Request $request, $merchProductId)
    {
        $merchProduct = MerchProduct::findOrFail($merchProductId);
        $user = Auth::user();

        // Cek stok jika perlu (Anda punya 'stock' di tabel merch)
        // if ($merchProduct->stock < 1) { ... }

        // Cek apakah item (merch) sudah ada di keranjang user
        $existingItem = CartItem::where('user_id', $user->id)
                                ->where('merch_product_id', $merchProduct->id)
                                ->first();

        if ($existingItem) {
            // Jika sudah ada, tambahkan quantity-nya
            $existingItem->quantity += $request->input('quantity', 1);
            $existingItem->save();
        } else {
            // Jika belum ada, buat record baru
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => null, // PENTING: null untuk lelang
                'merch_product_id' => $merchProduct->id, // PENTING: isi ID merch
                'quantity' => $request->input('quantity', 1),
                'price' => $merchProduct->price, // Salin harga merch saat ini
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Merchandise ditambahkan.');
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        // 1. Keamanan: Pastikan item ini milik user
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
        }

        // 2. Validasi input dari JavaScript
        $newQuantity = (int)$request->input('quantity');
        if ($newQuantity < 1) {
            $newQuantity = 1;
        }

        // 3. Cek Stok (HANYA jika ini item merchandise)
        if ($cartItem->merch_product_id) {
            // Kita muat relasinya (jika belum)
            $cartItem->load('merchProduct'); 
            $stock = $cartItem->merchProduct->stock;
            
            if ($newQuantity > $stock) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Stok tidak mencukupi (sisa ' . $stock . ')',
                    'quantity' => $stock // Kembalikan ke angka stok maks
                ], 422); // 422 = Unprocessable Entity
            }
        
        // 4. Cek kuantitas untuk LELANG (tidak boleh > 1)
        } elseif ($cartItem->product_id) {
            if ($newQuantity > 1) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Kuantitas barang lelang hanya bisa 1.',
                    'quantity' => 1 // Paksa kembali ke 1
                ], 422);
            }
        }

        // 5. Simpan ke database
        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        // 6. Kirim respons sukses kembali ke JavaScript
        return response()->json([
            'success' => true,
            'newQuantity' => $cartItem->quantity
        ]);
    }

    public function destroy(CartItem $cartItem)
    {
        // Keamanan: Pastikan item ini milik user yang sedang login
        if ($cartItem->user_id !== Auth::id()) {
            abort(403, 'Tindakan tidak diizinkan.');
        }

        $cartItem->delete();

        // 'with('success', ...)' ini opsional, untuk menampilkan pesan
        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus.');
    }
}
