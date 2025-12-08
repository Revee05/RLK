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
use Carbon\Carbon; // Pastikan ini ada

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
            // 2. Ambil Data Produk
            $product = Products::where('slug', $slug)
                ->with(['imageUtama', 'images', 'kategori', 'karya', 'kelengkapans'])
                ->first();

            if (empty($product)) {
                return abort(404);
            }

            // 3. Ambil History Bidding
            $bids = Bid::with('user')
                ->where('product_id', $product->id)
                ->orderBy('created_at', 'desc') 
                ->get();

            // ---------------------------------------------------------
            // PERBAIKAN: FORMAT DATA "SUPER SAFE MODE"
            // ---------------------------------------------------------
            $initialMessages = $bids->map(function ($item) {
                
                // A. Penanganan Tanggal (Anti Error String/Null)
                try {
                    if ($item->created_at) {
                        // Paksa parse ke Carbon dulu, baru format
                        $tanggal = Carbon::parse($item->created_at)->format('Y-m-d H:i:s');
                    } else {
                        // Jika null, pakai waktu sekarang
                        $tanggal = Carbon::now()->format('Y-m-d H:i:s');
                    }
                } catch (\Throwable $e) {
                    // Jika parsing gagal total, fallback ke waktu sekarang
                    $tanggal = Carbon::now()->format('Y-m-d H:i:s');
                }

                // B. Penanganan User (Anti Error User Terhapus)
                $userId = 0;
                $userName = 'Pengguna';
                $userEmail = '-';

                // Cek apakah user ada (tidak null)
                if ($item->user) {
                    $userId = $item->user->id ?? 0;
                    $userName = $item->user->name ?? 'Pengguna';
                    $userEmail = $item->user->email ?? '-';
                }

                return [
                    'user' => [
                        'id' => $userId,
                        'name' => $userName,
                        'email' => $userEmail
                    ],
                    'message' => $item->price,
                    'tanggal' => $tanggal,
                ];
            });

            $initialMessages = $initialMessages->values();


            // 4. Hitung Highest Bid
            $highestBid = $bids->first() ? $bids->first()->price : $product->price;


            // 5. Hitung Pilihan Nominal
            // Ambil kelipatan dari field 'kelipatan' (bukan 'kelipatan_bid' yang merupakan accessor)
            $step = intval($product->kelipatan ?? 0);
            
            // Jika step masih 0 atau negatif, gunakan default
            if ($step <= 0) {
                $step = 10000;
            }

            // DEBUG LOG
            Log::info('[getDetail] Kelipatan check:', [
                'product_id' => $product->id,
                'kelipatan_raw' => $product->kelipatan,
                'kelipatan_type' => gettype($product->kelipatan),
                'step_computed' => $step,
                'highestBid' => $highestBid
            ]);

            // Hitung nominals berdasarkan step yang sudah valid
            $nominals = [];
            for ($i = 1; $i <= 5; $i++) {
                $nominals[] = $highestBid + ($step * $i);
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
                'bids'       => $bids,
                'initialMessages' => $initialMessages,
                'highestBid' => $highestBid,
                'nominals'   => $nominals,
                'step'       => $step,  // PENTING: kirim step ke view!
                'related'    => $related
            ]);

        } catch (Exception $e) {
            Log::error('Detail Lelang Error: ' . $e->getMessage());
            // Tampilkan error biar jelas (Hanya saat development)
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
}