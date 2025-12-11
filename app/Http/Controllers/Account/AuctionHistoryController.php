<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Import Model
use App\Bid;      
use App\Products; 

class AuctionHistoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil User ID
        $userId = Auth::id();

        // 2. Query Data
        $myBids = Bid::with('product')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();

        // 3. Siapkan Variabel Statistik Awal
        $itemsWon = 0;
        $highestBidUser = 0;

        // 4. Looping Logic (Menentukan Status & Statistik)
        $history = $myBids->map(function ($bid) use (&$itemsWon, &$highestBidUser) {
            $product = $bid->product;
            
            // Cek harga tertinggi global saat ini di produk tersebut
            $globalHighestPrice = $product->bid()->max('price');

            $now = Carbon::now();
            $endDate = Carbon::parse($product->end_date); // Pastikan jadi object Carbon
            $hasEnded = $now->greaterThan($endDate);
            
            $statusLabel = '';
            $badgeClass = ''; // Tambahan untuk styling CSS (opsional)

            // Logika Status
            if (!$hasEnded) {
                // Lelang Masih Berjalan
                if ($bid->price >= $globalHighestPrice) {
                    $statusLabel = 'Memimpin'; // Bid kita paling tinggi sementara
                    $badgeClass = 'bg-warning text-dark';
                } else {
                    $statusLabel = 'Tertimpa'; // Ada yang ngebid lebih tinggi
                    $badgeClass = 'bg-secondary';
                }
            } else {
                // Lelang Sudah Berakhir
                if ($bid->price >= $globalHighestPrice) {
                    $statusLabel = 'Menang';
                    $badgeClass = 'bg-success';
                    $itemsWon++; // Tambah counter kemenangan
                } else {
                    $statusLabel = 'Kalah';
                    $badgeClass = 'bg-danger';
                }
            }

            // Update Highest Bid Pribadi (untuk statistik kartu)
            if ($bid->price > $highestBidUser) {
                $highestBidUser = $bid->price;
            }

            // Simpan data tambahan ke object $bid agar mudah dipanggil di View
            $bid->highest_global = $globalHighestPrice;
            $bid->status_label = $statusLabel;
            $bid->badge_class = $badgeClass; // Opsional
            
            return $bid;
        });

        // Hitung total setelah loop
        $totalBids = $myBids->count();

        // ==========================================
        //  BAGIAN RESPON JSON (AJAX)
        // ==========================================
        if ($request->ajax()) {
            
            // Render file partial khusus baris tabel (TR)
            // Pastikan kamu sudah membuat file: resources/views/account/auction/_table_rows.blade.php
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

        // ==========================================
        //  BAGIAN RESPON HALAMAN BIASA (NON-AJAX)
        // ==========================================
        return view('account.auction.auction_history', [
            'history' => $history,
            'totalBids' => $totalBids,
            'itemsWon' => $itemsWon,
            'highestBid' => $highestBidUser
        ]);
    }
}