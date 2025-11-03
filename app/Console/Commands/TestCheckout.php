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

class TestCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test checkout kirim email';

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
         $this->line('Start test sending email');
        Log::info("Start Jobs Runing");
        try {

            $idProduk = "1";
            $email = "ulil.digitalsiber@gmail.com";
            $bid = Bid::where('product_id',$idProduk)->orderBy('created_at', 'desc')->first();
            Log::info("Jobs Runing update status ID Product : ".$idProduk);
            $this->line('Process sending email ID :'. $idProduk);
            Mail::to($email)->send(new OrderShipped($bid));
            $this->line('Finish sending ID! '. $idProduk);

        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
