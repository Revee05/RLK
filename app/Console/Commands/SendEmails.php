<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Bid;
use App\User;
use App\Products;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderShipped;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkout:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim url checkout jika menang lelang';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Start sending email');
        Log::info("Start Jobs Runing");
        try {
            $products = Products::where(function($query) {
                $start = Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s');
                $end = Carbon::now()->format('Y-m-d H:i:s');

                $query->whereBetween('end_date', [ $start, $end, ]);
                $query->where('status', '=', '1');
            })->get();

            if ($products) {
                foreach($products as $prod){
                    $bid = Bid::where('product_id',$prod->id)->orderBy('created_at', 'desc')->first();
                    $prod->update(['status'=> 2]);
                    Log::info("Jobs Runing update status ID Product : ".$prod->id);
                    $this->line('Process sending email ID :'. $prod->id);
                    Mail::to($bid->user->email)->send(new OrderShipped($bid));
                    $this->line('Finish sending ID! '. $prod->id);

                }
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
