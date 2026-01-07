<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Bid;       
use App\Products; 
use App\Order; // Model Order wajib di-import

class AuctionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Ambil ID bid TERBARU milik user untuk setiap produk (Group by product)
        $latestBidIds = Bid::where('user_id', $userId)
                            ->selectRaw('MAX(id) as id')
                            ->groupBy('product_id');

        // 2. Ambil data bid lengkap berdasarkan ID di atas
        $myBids = Bid::with('product')
                    ->whereIn('id', $latestBidIds)
                    ->orderBy('updated_at', 'desc') // Urutkan dari aktivitas terbaru
                    ->get();

        $itemsWon = 0;
        $highestBidUser = 0;

        // 3. Mapping data untuk menentukan status (Menang/Kalah/Memimpin)
        $history = $myBids->map(function ($bid) use (&$itemsWon, &$highestBidUser, $userId) {
            $product = $bid->product;
            
            // Ambil harga tertinggi global saat ini di produk tersebut
            $globalHighestPrice = $product->bid()->max('price');
            
            // Cek waktu
            $now = Carbon::now();
            $endDate = Carbon::parse($product->end_date);
            $hasEnded = $now->greaterThan($endDate);
            
            // Cek apakah bid user ini adalah yang tertinggi
            $isWinnerCandidate = ($bid->price >= $globalHighestPrice);

            // Default Values
            $statusLabel = '';
            $badgeClass = '';
            $actionUrl = '#';

            // --- LOGIKA UTAMA ---
            
            if ($product->status == 3) {
                // KASUS 1: Dibatalkan oleh Admin
                $statusLabel = $isWinnerCandidate ? 'Dibatalkan' : 'Kalah';
                $badgeClass = $isWinnerCandidate ? 'badge-status canceled' : 'badge-status lose';
                $actionUrl = '#'; 

            } elseif (!$hasEnded) {
                // KASUS 2: Lelang Masih Berjalan
                if ($isWinnerCandidate) {
                    $statusLabel = 'Memimpin';
                    $badgeClass = 'badge-status process'; // Kuning/Biru Muda
                } else {
                    $statusLabel = 'Terlampaui';
                    $badgeClass = 'badge-status outbid'; // Merah/Orange
                }
                $actionUrl = route('lelang.detail', $product->slug);

            } else {
                // KASUS 3: Lelang Sudah Berakhir
                if ($isWinnerCandidate) {
                    $itemsWon++;
                    
                    // Cek apakah data sudah masuk ke tabel 'orders'
                    // Artinya user sudah checkout atau invoice sudah terbit
                    $orderExists = Order::where('user_id', $userId)
                                        ->where('product_id', $product->id)
                                        ->exists();

                    // Tampilan Badge TETAP HIJAU
                    $badgeClass = 'badge-status win';
                    $statusLabel = 'Menang'; 

                    if ($orderExists) {
                        // SUDAH CHECKOUT -> Arahkan ke Riwayat Pembelian
                        // Menggunakan nama route dari RiwayatPembelianController yang kamu kirim
                        $actionUrl = route('account.purchase.history'); 
                    } else {
                        // BELUM CHECKOUT -> Arahkan ke Keranjang
                        $actionUrl = route('cart.index');
                    }

                } else {
                    // KALAH LELANG
                    $statusLabel = 'Kalah';
                    $badgeClass = 'badge-status lose'; // Merah
                    $actionUrl = route('lelang.detail', $product->slug);
                }
            }

            // Update statistik bid tertinggi user
            if ($bid->price > $highestBidUser) { 
                $highestBidUser = $bid->price; 
            }

            // Simpan data tambahan ke object bid untuk dipakai di View
            $bid->highest_global = $globalHighestPrice;
            $bid->status_label = $statusLabel;
            $bid->badge_class = $badgeClass;
            $bid->action_url = $actionUrl;
            
            return $bid;
        });

        $totalBids = $myBids->count();

        // RESPONSE JSON (Untuk Load More / Ajax)
        if ($request->ajax()) {
            $tableHtml = view('account.auction._table_rows', ['history' => $history])->render();
            return response()->json([
                'status' => 'success',
                'html' => $tableHtml,
                'stats' => [
                    'totalBids' => $totalBids,
                    'itemsWon'  => $itemsWon,
                    'highestBid'=> 'Rp ' . number_format($highestBidUser, 0, ',', '.')
                ]
            ]);
        }

        // RESPONSE VIEW BIASA
        return view('account.auction.auction_history', [
            'history' => $history,
            'totalBids' => $totalBids,
            'itemsWon' => $itemsWon,
            'highestBid' => $highestBidUser
        ]);
    }
}