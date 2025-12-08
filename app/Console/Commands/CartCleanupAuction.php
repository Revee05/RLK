<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CartItem;
use App\Products; // Sesuaikan namespace
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CartCleanupAuction extends Command
{
    protected $signature = 'cart:cleanup-auction';
    protected $description = 'Hapus item lelang di keranjang yang sudah expired';

    public function handle()
    {
        $this->info('--- Cek Keranjang Expired ---');

        $now = Carbon::now();

        // Cari item tipe 'lelang' yang expires_at-nya sudah lewat
        $expiredCarts = CartItem::where('type', 'lelang')
                                ->whereNotNull('expires_at')
                                ->where('expires_at', '<', $now)
                                ->get();

        if ($expiredCarts->isEmpty()) {
            $this->info('Tidak ada item expired.');
            return;
        }

        foreach ($expiredCarts as $item) {
            DB::beginTransaction();
            try {
                // 1. Update Status Produk Asli jadi Hangus (Status 3)
                if ($item->product_id) {
                    $product = Products::find($item->product_id);
                    if ($product) {
                        $product->status = 3; // Hangus
                        $product->winner_id = null; // Hapus winner
                        $product->save();
                        $this->info("🔥 Produk {$product->title} di-set hangus (Gagal bayar).");
                    }
                }

                // 2. Hapus dari Keranjang
                $item->delete();
                
                DB::commit();
                $this->info("🗑️ Item Cart ID {$item->id} dihapus.");
                
            } catch (\Exception $e) {
                DB::rollback();
                $this->error("Gagal proses ID {$item->id}: " . $e->getMessage());
            }
        }
    }
}