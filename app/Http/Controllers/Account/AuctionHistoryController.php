<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Bid;      
use App\Products; 

class AuctionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Ambil data bid user
        $myBids = Bid::with('product')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();

        $itemsWon = 0;
        $highestBidUser = 0;

        // Proses Mapping Data
        $history = $myBids->map(function ($bid) use (&$itemsWon, &$highestBidUser) {
            $product = $bid->product;
            
            // 1. Data Pendukung
            $globalHighestPrice = $product->bid()->max('price');
            $now = Carbon::now();
            $endDate = Carbon::parse($product->end_date);
            $hasEnded = $now->greaterThan($endDate);
            $isWinnerCandidate = ($bid->price >= $globalHighestPrice);

            // 2. Default Values
            $statusLabel = '';
            $badgeClass = '';
            $actionUrl = '#'; 

            // ====================================================
            // LOGIKA UTAMA (URUTAN PENTING)
            // ====================================================

            // KONDISI 1: STATUS DIBATALKAN / HANGUS (Priority Tertinggi)
            if ($product->status == 3) {
                if ($isWinnerCandidate) {
                    // Menang tapi batal/hangus
                    $statusLabel = 'Dibatalkan'; 
                    $badgeClass = 'badge-status canceled'; // Merah
                    $actionUrl = '#'; // Link mati
                } else {
                    // Kalah & batal
                    $statusLabel = 'Kalah';
                    $badgeClass = 'badge-status lose';
                    $actionUrl = route('lelang.detail', $product->slug);
                }
            }
            
            // KONDISI 2: LELANG MASIH BERJALAN
            elseif (!$hasEnded) {
                if ($isWinnerCandidate) {
                    $statusLabel = 'Memimpin';
                    $badgeClass = 'badge-status process'; 
                    $actionUrl = route('lelang.detail', $product->slug);
                } else {
                    $statusLabel = 'Terlampaui'; 
                    $badgeClass = 'badge-status lose';    
                    $actionUrl = route('lelang.detail', $product->slug);
                }
            } 
            
            // KONDISI 3: LELANG BERAKHIR & NORMAL (Status 1 atau 2)
            else {
                if ($isWinnerCandidate) {
                    $statusLabel = 'Menang';
                    $badgeClass = 'badge-status win';     
                    $itemsWon++; 
                    $actionUrl = route('cart.index'); // Masuk Keranjang
                } else {
                    $statusLabel = 'Kalah';
                    $badgeClass = 'badge-status lose';
                    $actionUrl = route('lelang.detail', $product->slug);
                }
            }

            // Update Statistik Highest Bid User
            if ($bid->price > $highestBidUser) {
                $highestBidUser = $bid->price;
            }

            // Simpan data ke object untuk View
            $bid->highest_global = $globalHighestPrice;
            $bid->status_label = $statusLabel;
            $bid->badge_class = $badgeClass;
            $bid->action_url = $actionUrl;
            
            return $bid;
        });

        $totalBids = $myBids->count();

        // RESPONSE
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

        return view('account.auction.auction_history', [
            'history' => $history,
            'totalBids' => $totalBids,
            'itemsWon' => $itemsWon,
            'highestBid' => $highestBidUser
        ]);
    }
}