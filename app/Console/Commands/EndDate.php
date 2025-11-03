<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Products;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EndDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bid:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengubah status berdasarkan tanggal berakhir lelang jika sudah kelewat';

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
        try {
            $this->line('Start Update produk expired');
            Log::info("Start Jobs expired");
            //tanggal hari ini
            $dataNoew = Carbon::now();
            $produk = Products::where('end_date', "<=", $dataNoew->format("Y-m-d H:i:s"))->where('status',"!=",'3')->update(['status' => 3]);
            
            $this->line('Finish Update produk expired');
            Log::info("Finish update status Produk");
        
        } catch (Exception $e) {
            Log::error($exception);
        }
    }
}
