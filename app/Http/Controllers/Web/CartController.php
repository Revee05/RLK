<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CartItem;
use App\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // UBAH BARIS INI:
        // $cartItems = CartItem::with('product.productImages') ...

        // MENJADI INI: (Gunakan nama relasi 'images' dari model Products Anda)
        $cartItems = CartItem::with('product.images', 'product.kategori')
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
