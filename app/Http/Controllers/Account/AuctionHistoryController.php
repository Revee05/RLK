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

    // Menggunakan query builder dengan subquery untuk mengambil penawaran TERBARU user per produk
    $latestBidIds = Bid::where('user_id', $userId)
                        ->selectRaw('MAX(id) as id')
                        ->groupBy('product_id');

    $myBids = Bid::with('product')
                ->whereIn('id', $latestBidIds)
                ->orderBy('updated_at', 'desc') // Produk dengan aktivitas terbaru naik ke atas
                ->get();

    $itemsWon = 0;
    $highestBidUser = 0;

    // Mapping data tetap sama dengan logika kamu yang sudah bagus
    $history = $myBids->map(function ($bid) use (&$itemsWon, &$highestBidUser) {
        $product = $bid->product;
        
        // Logika penentuan status (Memimpin, Terlampaui, Menang, dll)
        $globalHighestPrice = $product->bid()->max('price');
        $now = Carbon::now();
        $endDate = Carbon::parse($product->end_date);
        $hasEnded = $now->greaterThan($endDate);
        
        // Cek apakah bid ini masih yang tertinggi di produk tersebut
        $isWinnerCandidate = ($bid->price >= $globalHighestPrice);

        // ... (Logika penentuan $statusLabel dan $badgeClass tetap sama seperti kode kamu) ...
        // Contoh ringkas:
        if ($product->status == 3) {
            $statusLabel = $isWinnerCandidate ? 'Dibatalkan' : 'Kalah';
            $badgeClass = $isWinnerCandidate ? 'badge-status canceled' : 'badge-status lose';
            $actionUrl = '#';
        } elseif (!$hasEnded) {
            $statusLabel = $isWinnerCandidate ? 'Memimpin' : 'Terlampaui';
            $badgeClass = $isWinnerCandidate ? 'badge-status process' : 'badge-status outbid';
            $actionUrl = route('lelang.detail', $product->slug);
        } else {
            if ($isWinnerCandidate) {
                $statusLabel = 'Menang';
                $badgeClass = 'badge-status win';
                $itemsWon++;
                $actionUrl = route('cart.index');
            } else {
                $statusLabel = 'Kalah';
                $badgeClass = 'badge-status lose';
                $actionUrl = route('lelang.detail', $product->slug);
            }
        }

        // Update statistik nominal tertinggi yang pernah dikeluarkan user
        if ($bid->price > $highestBidUser) { $highestBidUser = $bid->price; }

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