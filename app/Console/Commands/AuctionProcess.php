<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Products;
use App\Bid;
use Mail;
use App\Mail\OrderShipped;
use Carbon\Carbon;
use App\Events\AuctionResultEvent;
use Illuminate\Support\Facades\Log;

class AuctionProcess extends Command
{
    protected $signature = 'auction:process';
    protected $description = 'Menentukan pemenang lelang dan mengirim checkout email';

    public function handle()
    {
        $now = Carbon::now();

        $products = Products::where('end_date', '<=', $now)
                            ->where('status', 1)
                            ->get();

        foreach ($products as $product) {
            $bid = Bid::where('product_id', $product->id)
                      ->orderByRaw('CAST(price AS UNSIGNED) DESC')
                      ->first();

            if (!$bid) {
                $product->update(['status' => 3]); // expired, no bids
                continue;
            }

            // Update status product -> 2 (menunggu checkout)
            $product->update(['status' => 2]);

            // Kirim email pemenang
            try {
                Mail::to($bid->user->email)->send(new OrderShipped($bid));
            } catch (\Throwable $e) {
                Log::error("Mail error: ".$e->getMessage());
            }

            // Buat checkout url (sesuaikan dengan route kamu)
            // Jika route checkout membutuhkan encrypted slug, buat enkripsi yang sesuai
            try {
                $encrypted = \Crypt::encrypt($product->slug);
                $checkoutUrl = route('checkout.cart', $encrypted); // sesuai route kamu
            } catch (\Throwable $e) {
                $checkoutUrl = url('/account/checkout'); // fallback
            }

            // Kirim notifikasi realtime ke pemenang
            event(new AuctionResultEvent(
                $bid->user_id,               // untuk channel user.{id}
                'winner',
                $product->title,
                $bid->price,
                route('checkout.cart', $product->slug)
            ));


            // (Optional) Notifikasi ke semua bidder lain bahwa mereka kalah
            // user lain yang kalah
            $losers = Bid::where('product_id', $product->id)
                        ->where('user_id', '!=', $bid->user_id)
                        ->groupBy('user_id')
                        ->get();

            foreach ($losers as $loser) {
            event(new AuctionResultEvent(
                $loser->user_id,
                'loser',
                $product->title,
                $bid->price,
                null
            ));
        }
        }
    }
}
