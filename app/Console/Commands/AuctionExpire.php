<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Products;
use Carbon\Carbon;

class AuctionExpire extends Command
{
    protected $signature = 'auction:expire';
    protected $description = 'Menandai produk expired (fallback)';

    public function handle()
    {
        $now = Carbon::now();

        // Produk yang sudah lama selesai (3 hari) tapi belum diambil user
        Products::where('end_date', '<=', $now->subDays(3))
                ->where('status', 2) // sudah ada pemenang tapi belum checkout
                ->update(['status' => 3]); // expired
    }
}
