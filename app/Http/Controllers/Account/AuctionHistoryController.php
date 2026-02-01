<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Bid;       
use App\Products; 
use App\Order; 

class AuctionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Ambil ID bid TERBARU
        $latestBidIds = Bid::where('user_id', $userId)
                            ->selectRaw('MAX(id) as id')
                            ->groupBy('product_id');

        // 2. Ambil data bid lengkap
        $myBids = Bid::with('product')
                    ->whereIn('id', $latestBidIds)
                    ->orderBy('updated_at', 'desc')
                    ->get();

        $itemsWon = 0;
        $highestBidUser = 0;

        // 3. Mapping data
        $history = $myBids->map(function ($bid) use (&$itemsWon, &$highestBidUser, $userId) {
            $product = $bid->product;
            
            $globalHighestPrice = $product->bid()->max('price');
            
            $now = Carbon::now();
            $endDate = Carbon::parse($product->end_date);
            $hasEnded = $now->greaterThan($endDate);
            
            $isWinnerCandidate = ($bid->price >= $globalHighestPrice);

            // Default Values
            $statusLabel = '';
            $badgeClass = '';
            $actionUrl = '#';

            // --- LOGIKA UTAMA ---
            
            if ($product->status == 3) {
                // KASUS 1: Dibatalkan oleh Admin (Produknya yang cancel)
                $statusLabel = $isWinnerCandidate ? 'Dibatalkan' : 'Kalah';
                $badgeClass = $isWinnerCandidate ? 'badge-status canceled' : 'badge-status lose';
                $actionUrl = '#'; 

            } elseif (!$hasEnded) {
                // KASUS 2: Lelang Masih Berjalan
                if ($isWinnerCandidate) {
                    $statusLabel = 'Memimpin';
                    $badgeClass = 'badge-status process'; 
                } else {
                    $statusLabel = 'Terlampaui';
                    $badgeClass = 'badge-status outbid'; 
                }
                $actionUrl = route('lelang.detail', $product->slug);

            } else {
                // KASUS 3: Lelang Sudah Berakhir
                if ($isWinnerCandidate) {
                    
                    // --- PERUBAHAN DI SINI ---
                    // Ambil data order asli, bukan hanya cek exist
                    $existingOrder = Order::where('user_id', $userId)
                                          ->where('product_id', $product->id)
                                          ->latest() // Ambil yang paling baru jika ada duplikat
                                          ->first();

                    if ($existingOrder) {
                        // SUDAH CHECKOUT (Data ada di tabel orders)
                        
                        // Cek status di tabel orders (sesuai gambar: 'expired', 'pending', dll)
                        if ($existingOrder->status == 'expired') {
                            // Jika Expired, maka dianggap BATAL (hangus)
                            $statusLabel = 'Dibatalkan';
                            $badgeClass = 'badge-status canceled'; // Pastikan ada style CSS untuk class ini (biasanya warna abu/merah)
                            $actionUrl = '#'; // Atau link ke detail untuk lihat kenapa batal
                            
                            // Note: $itemsWon TIDAK ditambah karena batal/expired
                        } else {
                            // Status: pending, success, capture, settlement, dll.
                            $itemsWon++; 
                            $statusLabel = 'Menang'; 
                            $badgeClass = 'badge-status win';
                            
                            // Arahkan ke Riwayat Pembelian
                            $actionUrl = route('account.purchase.history'); 
                        }

                    } else {
                        // BELUM CHECKOUT (Belum ada di tabel orders) -> Arahkan ke Keranjang
                        $itemsWon++;
                        $statusLabel = 'Menang'; 
                        $badgeClass = 'badge-status win';
                        $actionUrl = route('cart.index');
                    }

                } else {
                    // KALAH LELANG
                    $statusLabel = 'Kalah';
                    $badgeClass = 'badge-status lose';
                    $actionUrl = route('lelang.detail', $product->slug);
                }
            }

            if ($bid->price > $highestBidUser) { 
                $highestBidUser = $bid->price; 
            }

            $bid->highest_global = $globalHighestPrice;
            $bid->status_label = $statusLabel;
            $bid->badge_class = $badgeClass;
            $bid->action_url = $actionUrl;
            
            return $bid;
        });

        $totalBids = $myBids->count();

        // RESPONSE JSON
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

        // RESPONSE VIEW
        return view('account.auction.auction_history', [
            'history' => $history,
            'totalBids' => $totalBids,
            'itemsWon' => $itemsWon,
            'highestBid' => $highestBidUser
        ]);
    }
}