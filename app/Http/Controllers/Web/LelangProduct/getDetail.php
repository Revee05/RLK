<?php

namespace App\Http\Controllers\Web\LelangProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Products;
use App\Bid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;

class getDetail extends Controller
{
    /**
     * Menampilkan detail produk lelang
     */
    public function show($slug)
    {
        // 1. Validasi slug
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:products,slug']
        ]);

        if ($validator->fails()) {
            return abort(404);
        }

        try {
            // 2. Ambil Data Produk beserta Relasi
            $product = Products::where('slug', $slug)
                ->with(['imageUtama', 'images', 'kategori', 'karya', 'kelengkapans'])
                ->first();

            if (empty($product)) {
                return abort(404);
            }

            // 3. Ambil Semua History Bidding (Urutkan dari Terbaru)
            $bids = Bid::with('user')
                ->where('product_id', $product->id)
                ->orderBy('created_at', 'desc') 
                ->get();

            // ---------------------------------------------------------
            // PERBAIKAN PENTING: FORMAT DATA KHUSUS UNTUK VUE JS
            // ---------------------------------------------------------
            // Kita mapping data agar strukturnya sesuai dengan props 'messages' di Vue
            $initialMessages = $bids->map(function ($item) {
                return [
                    'user' => [
                        'id' => $item->user->id ?? 0,
                        'name' => $item->user->name ?? 'Pengguna',
                        'email' => $item->user->email ?? '-'
                    ],
                    // Message harus berisi HARGA (integer), bukan string format
                    'message' => $item->price,
                    // Tanggal format ISO/String
                    'tanggal' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

            // Data terbaru harus di atas (descending by created_at)
            $initialMessages = $initialMessages->values();


            // 4. Hitung Highest Bid
            // Jika ada bid, ambil yang pertama (tertinggi). Jika tidak, pakai harga awal produk.
            $highestBid = $bids->first() ? $bids->first()->price : $product->price;


            // 5. Hitung Pilihan Nominal (Kelipatan)
            // Cek ketersediaan kolom kelipatan_bid atau kelipatan
            $step = 0;
            if (isset($product->kelipatan_bid)) {
                $step = intval($product->kelipatan_bid);
            } elseif (isset($product->kelipatan)) {
                $step = intval($product->kelipatan);
            }

            // Buat opsi dropdown (misal: 5 opsi kenaikan)
            $nominals = [];
            if ($step > 0) {
                for ($i = 1; $i <= 5; $i++) {
                    $nominals[] = $highestBid + ($step * $i);
                }
            } else {
                // Fallback jika kelipatan 0/null, misal naik 10rb per bid (optional)
                $defaultStep = 10000;
                for ($i = 1; $i <= 5; $i++) {
                    $nominals[] = $highestBid + ($defaultStep * $i);
                }
            }


            // 6. Ambil Related Products
            $related = Products::where('kategori_id', $product->kategori_id)
                ->where('id', '!=', $product->id)
                ->inRandomOrder()
                ->limit(4)
                ->with('imageUtama')
                ->get();


            // 7. Return ke View
            return view('web.detail_lelang.detail', [
                'product'    => $product,
                'bids'       => $bids,            // Data raw untuk user Guest (foreach biasa)
                'initialMessages' => $initialMessages, // Data JSON untuk user Login (Vue JS)
                'highestBid' => $highestBid,
                'nominals'   => $nominals,
                'related'    => $related
            ]);

        } catch (Exception $e) {
            Log::error('Detail Lelang Product Error: ' . $e->getMessage());
            return abort(500);
        }
    }
}