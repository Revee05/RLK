<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

// --- MODEL ---
use App\Products; 
use App\Bid;      
use App\CartItem; 

// --- MAIL ---
use App\Mail\OrderShipped;    // Email Pemenang
use App\Mail\BidNotification; // Email Kalah

class CloseExpiredAuctions extends Command
{
    protected $signature = 'lelang:close-expired';
    protected $description = 'Cek lelang berakhir, tentukan pemenang, masuk cart, dan kirim email.';

    public function handle()
    {
        $this->info('--- Memulai Pengecekan Lelang ---');
        $now = Carbon::now();

        // 1. Cari Produk Aktif (status 1) yang waktunya sudah habis
        $expiredProducts = Products::where('status', 1)
                                   ->where('end_date', '<', $now)
                                   ->get();

        if ($expiredProducts->isEmpty()) {
            $this->info('Tidak ada lelang yang expired saat ini.');
            return;
        }

        foreach ($expiredProducts as $product) {
            DB::beginTransaction();
            try {
                // Cari bid tertinggi untuk produk ini
                // PENTING: Gunakan orderByRaw untuk cast price ke integer agar sorting benar
                $winningBid = Bid::where('product_id', $product->id)
                                 ->orderByRaw('CAST(price AS UNSIGNED) DESC')
                                 ->first();

                if ($winningBid) {
                    // ==========================================
                    // SKENARIO A: ADA PEMENANG
                    // ==========================================
                    
                    // 1. Update Status Produk -> 2 (Sold/Waiting Payment)
                    $product->status = 2;
                    $product->winner_id = $winningBid->user_id;
                    $product->save();

                    // 2. Masukkan ke Keranjang Pemenang (Logika Baru)
                    $existingCart = CartItem::where('user_id', $winningBid->user_id)
                                            ->where('product_id', $product->id)
                                            ->first();

                    if (!$existingCart) {
                        CartItem::create([
                            'user_id'          => $winningBid->user_id,
                            'product_id'       => $product->id,    
                            'merch_product_id' => null, 
                            'type'             => 'lelang', 
                            'quantity'         => 1,
                            'price'            => $winningBid->price,
                            'expires_at'       => Carbon::now()->addDays(7), // Expired 7 hari
                        ]);
                        $this->info("🛒 Product {$product->id} masuk cart User ID {$winningBid->user_id}.");
                    }

                    // 3. Kirim Email ke Pemenang (Logika Lama)
                    try {
                        if ($winningBid->user && !empty($winningBid->user->email)) {
                            Mail::to($winningBid->user->email)->send(new OrderShipped($winningBid));
                            $this->info("✉️ Email MENANG dikirim ke: {$winningBid->user->email}");
                        }
                    } catch (\Exception $mailEx) {
                        $this->error("Gagal kirim email pemenang: " . $mailEx->getMessage());
                    }

                    // 4. Kirim Email ke Peserta Kalah (Logika Lama)
                    try {
                        $loserBids = Bid::where('product_id', $product->id)
                                        ->where('user_id', '!=', $winningBid->user_id)
                                        ->get()
                                        ->groupBy('user_id');

                        foreach ($loserBids as $userId => $bids) {
                            // Ambil bid tertinggi user tersebut untuk konteks email
                            $highestUserBid = $bids->sortByDesc('price')->first();
                            
                            if ($highestUserBid && $highestUserBid->user && !empty($highestUserBid->user->email)) {
                                // Kirim notifikasi kalah
                                Mail::to($highestUserBid->user->email)->send(new BidNotification($highestUserBid, $winningBid));
                                $this->info("✉️ Email KALAH dikirim ke: {$highestUserBid->user->email}");
                            }
                        }
                    } catch (\Exception $mailEx) {
                        $this->error("Gagal kirim email kalah: " . $mailEx->getMessage());
                    }

                } else {
                    // ==========================================
                    // SKENARIO B: TIDAK ADA BID (HANGUS)
                    // ==========================================
                    $product->status = 3; // Expired/Hangus
                    $product->winner_id = null;
                    $product->save();
                    $this->info("❌ Product {$product->id} hangus (tidak ada bid).");
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $this->error("Error ID {$product->id}: " . $e->getMessage());
            }
        }
    }
}