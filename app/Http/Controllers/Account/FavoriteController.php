<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    /**
     * Menampilkan halaman daftar favorit user
     */
    public function index()
    {
        try {
            $favorites = Favorite::with('product.variants.images')
                ->where('user_id', Auth::id())
                ->get();

            return view('account.favorites.favorites', compact('favorites'));

        } catch (\Exception $e) {
            Log::error('Error fetching favorites: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat daftar favorit.');
        }
    }

    /**
     * Toggle Favorite (Add / Remove AJAX)
     */
    public function toggle(Request $request)
    {
        try {
            $productId = $request->product_id;
            $userId = Auth::id();

            // cek jika sudah ada favorit
            $favorite = Favorite::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($favorite) {
                $favorite->delete();

                return response()->json([
                    'status' => 'removed',
                    'message' => 'Produk dihapus dari favorit'
                ]);
            }

            Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            return response()->json([
                'status' => 'added',
                'message' => 'Produk berhasil ditambahkan ke favorit'
            ]);

        } catch (\Exception $e) {
            Log::error('Favorite Toggle Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }

    /**
     * Menghapus dari daftar favorit (dipakai di halaman favorites)
     */
    public function remove($id)
    {
        try {
            $favorite = Favorite::findOrFail($id);

            if ($favorite->user_id != Auth::id()) {
                return redirect()->back()->with('error', 'Tidak memiliki akses.');
            }

            $favorite->delete();

            return redirect()->back()->with('success', 'Produk dihapus dari favorit.');

        } catch (\Exception $e) {
            Log::error('Error removing favorite: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus dari favorit.');
        }
    }
}
