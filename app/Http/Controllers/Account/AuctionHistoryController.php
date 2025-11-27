<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// --- IMPORT MODEL SESUAI NAMESPACE KAMU ---
use App\Bid;      
use App\Products; 

class AuctionHistoryController extends Controller
{
    public function index(Request $request) // Tambahkan Request $request
    {
        // 1. Ambil User ID
        $userId = Auth::id();

        // 2. Query Data (SAMA SEPERTI SEBELUMNYA)
        $myBids = Bid::with('product')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();

        // 3. Siapkan Variabel Statistik Awal
        $totalBids = $myBids->count();
        $itemsWon = 0;
        $highestBidUser = 0;

        // 4. Looping Logic (SAMA SEPERTI SEBELUMNYA)
        $history = $myBids->map(function ($bid) use (&$itemsWon, &$highestBidUser) {
            $product = $bid->product;
            
            // Menggunakan Opsi 1 (Function bid() singular sesuai model kamu)
            $globalHighestPrice = $product->bid()->max('price');

            $now = Carbon::now();
            $endDate = $product->end_date; 
            $hasEnded = $now->greaterThan($endDate);
            
            $statusLabel = '';

            if (!$hasEnded) {
                $statusLabel = 'Dalam Proses';
            } else {
                if ($bid->price >= $globalHighestPrice) {
                    $statusLabel = 'Menang';
                    $itemsWon++; 
                } else {
                    $statusLabel = 'Kalah';
                }
            }

            if ($bid->price > $highestBidUser) {
                $highestBidUser = $bid->price;
            }

            $bid->highest_global = $globalHighestPrice;
            $bid->status_label = $statusLabel;
            
            return $bid;
        });

        // ==========================================
        //  MODIFIKASI BAGIAN RETURN DI BAWAH INI
        // ==========================================

        // Jika request datang dari AJAX (JavaScript)
        if ($request->ajax()) {
            // Render file partial yang baru kita buat jadi string HTML
            $tableHtml = view('account.auction._table_rows', ['history' => $history])->render();

            // Return data dalam format JSON
            return response()->json([
                'html' => $tableHtml,
                'stats' => [
                    'totalBids' => $totalBids,
                    'itemsWon'  => $itemsWon,
                    'highestBid'=> number_format($highestBidUser, 0, ',', '.')
                ]
            ]);
        }

        // Jika request biasa (Buka halaman pertama kali)
        return view('account.auction.auction_history', [
            'history' => $history,
            'totalBids' => $totalBids,
            'itemsWon' => $itemsWon,
            'highestBid' => $highestBidUser
        ]);
    }
}