<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * PENTING: Pastikan namespace model di bawah ini sesuai dengan struktur folder kamu.
 * Jika Laravel versi 8 ke atas, biasanya ada di App\Models.
 * Jika versi lama, mungkin langsung di App.
 * (Pilih salah satu dan hapus komentar pada baris yang sesuai)
 */
use App\Products; 
use App\Bid;
// use App\Products;
// use App\Bid;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderShipped;
use App\Mail\BidNotification;

class CloseExpiredAuctions extends Command
{
    /**
     * Nama perintah console.
     * Jalankan dengan: php artisan lelang:close-expired
     */
    protected $signature = 'lelang:close-expired';

    /**
     * Deskripsi perintah.
     */
    protected $description = 'Mengecek lelang expired, update status ke 2 (sold) atau 3 (expired), dan set winner_id.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Eksekusi perintah.
     */
    public function handle()
    {
        $this->info('--- Memulai Pengecekan Lelang ---');

        $now = Carbon::now();

        // 1. Cari Produk: Status Aktif (1) TAPI Waktu Habis (< Now)
        $expiredProducts = Products::where('status', 1)
                                   ->where('end_date', '<', $now)
                                   ->get();

        if ($expiredProducts->isEmpty()) {
            $this->info('Tidak ada lelang yang expired saat ini.');
            return;
        }

        $count = 0;

        foreach ($expiredProducts as $product) {
            DB::beginTransaction(); // Mulai transaksi database
            try {
                // Cari bid tertinggi untuk produk ini
                // PENTING: Gunakan orderByRaw untuk cast price ke integer agar sorting benar
                $winningBid = Bid::where('product_id', $product->id)
                                 ->orderByRaw('CAST(price AS UNSIGNED) DESC')
                                 ->first();

                if ($winningBid) {
                    // --- SKENARIO A: ADA PEMENANG (SOLD) ---
                    
                    // 1. Update Status Produk jadi 2 (Sold)
                    $product->status = 2;

                    // 2. [PERBAIKAN] Simpan ID User pemenang ke kolom winner_id
                    $product->winner_id = $winningBid->user_id;

                    // 3. Simpan perubahan ke database
                    $product->save();

                    $this->info("✅ [SOLD] Product ID {$product->id} - Pemenang User ID: {$winningBid->user_id} (Data Disimpan).");

                    // Kirim email ke pemenang
                    try {
                        if ($winningBid->user && !empty($winningBid->user->email)) {
                            Mail::to($winningBid->user->email)->send(new OrderShipped($winningBid));
                            $this->info("✉️ Email dikirim ke pemenang: {$winningBid->user->email}");
                        }
                    } catch (\Exception $mailEx) {
                        $this->error("Gagal mengirim email ke pemenang ({$product->id}): " . $mailEx->getMessage());
                    }

                    // Kirim notifikasi ke peserta lain (losers)
                    try {
                        $loserBids = Bid::where('product_id', $product->id)
                                        ->where('user_id', '!=', $winningBid->user_id)
                                        ->get()
                                        ->groupBy('user_id');

                        foreach ($loserBids as $userId => $bids) {
                            // Ambil bid tertinggi dari user tersebut untuk konteks email
                            $highestUserBid = $bids->sortByDesc('price')->first();
                            if ($highestUserBid && $highestUserBid->user && !empty($highestUserBid->user->email)) {
                                // Kirim juga data bid pemenang supaya view bisa menampilkan harga pemenang yang benar
                                Mail::to($highestUserBid->user->email)->send(new BidNotification($highestUserBid, $winningBid));
                                $this->info("✉️ Email kalah dikirim ke: {$highestUserBid->user->email}");
                            }
                        }
                    } catch (\Exception $mailEx) {
                        $this->error("Gagal mengirim email notifikasi kalah untuk produk {$product->id}: " . $mailEx->getMessage());
                    }
                } else {
                    // --- SKENARIO B: TIDAK ADA YANG BID (HANGUS) ---
                    
                    // Update Status Produk jadi 3 (Expired/Hangus)
                    $product->status = 3;
                    
                    // Pastikan winner_id null (opsional, untuk menjaga data bersih)
                    $product->winner_id = null;
                    
                    $product->save();

                    // Tidak ada penawar — tidak ada email pemenang / kalah.
                    $this->info("❌ [EXPIRED] Product ID {$product->id} tidak ada penawaran.");
                    $this->info("(No emails) Produk {$product->id} tidak ada penawaran — tidak ada email dikirim.");
                }

                DB::commit(); // Komit perubahan jika tidak ada error
                $count++;

            } catch (\Exception $e) {
                DB::rollback(); // Batalkan perubahan jika terjadi error
                $this->error("Error pada Product ID {$product->id}: " . $e->getMessage());
            }
        }

        $this->info("--- Selesai. Total diproses: {$count} lelang ---");
    }
}