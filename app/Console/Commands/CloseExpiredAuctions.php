<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// --- PENTING: SESUAIKAN NAMESPACE MODEL ---
// Cek folder App kamu, apakah model ada di folder 'App\Models' atau langsung 'App'?
// Gunakan salah satu baris di bawah ini sesuai struktur projectmu:

// OPSI 1: Jika model ada di luar (Laravel versi lama)
use App\Products;
use App\Bid;

// OPSI 2: Jika model ada di dalam folder Models (Laravel 8 ke atas)
// use App\Models\Products;
// use App\Models\Bid;

class CloseExpiredAuctions extends Command
{
    /**
     * Nama perintah yang akan kita ketik di terminal nanti.
     * Contoh: php artisan lelang:close-expired
     */
    protected $signature = 'lelang:close-expired';

    /**
     * Penjelasan singkat tentang apa fungsi perintah ini.
     */
    protected $description = 'Mengecek lelang yang expired, menentukan pemenang, dan update status.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Di sinilah logika utamanya berjalan.
     */
    public function handle()
    {
        $this->info('--- Memulai Pengecekan Lelang ---');

        $now = Carbon::now();

        // 1. Cari Produk yang Statusnya MASIH 1 (Live) TAPI Waktunya SUDAH LEWAT
        // Kita pakai get() biasa.
        $expiredProducts = Products::where('status', 1)
                                   ->where('end_date', '<', $now)
                                   ->get();

        if ($expiredProducts->isEmpty()) {
            $this->info('Tidak ada lelang yang expired saat ini.');
            return;
        }

        $count = 0;

        foreach ($expiredProducts as $product) {
            DB::beginTransaction(); // Mulai transaksi biar aman
            try {
                // Cek bid tertinggi
                $winningBid = Bid::where('product_id', $product->id)
                                 ->orderBy('price', 'desc')
                                 ->first();

                if ($winningBid) {
                    // --- SKENARIO A: ADA PEMENANG ---
                    
                    // 1. Update Produk jadi Status 2 (Sold/Terjual)
                    $product->status = 2;
                    $product->save();

                    // (Opsional) Update status di tabel bid jika ada kolom status
                    // $winningBid->status = 2; 
                    // $winningBid->save();

                    $this->info("✅ [SOLD] Product ID {$product->id} dimenangkan oleh User ID {$winningBid->user_id} di harga Rp " . number_format($winningBid->price));
                } else {
                    // --- SKENARIO B: GAK ADA YANG BID ---
                    
                    // Update Produk jadi Status 3 (Expired/Hangus)
                    $product->status = 3;
                    $product->save();

                    $this->info("❌ [EXPIRED] Product ID {$product->id} tidak ada penawaran.");
                }

                DB::commit(); // Simpan perubahan
                $count++;

            } catch (\Exception $e) {
                DB::rollback(); // Batalkan jika error
                $this->error("Error pada Product ID {$product->id}: " . $e->getMessage());
            }
        }

        $this->info("--- Selesai. Total diproses: {$count} lelang ---");
    }
}