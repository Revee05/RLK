<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
         Commands\SendEmails::class,
         Commands\EndDate::class,
         Commands\SyncRajaOngkir::class,
         Commands\AuctionProcess::class,
         Commands\AuctionExpire::class,
         // --- TAMBAHAN BARU ---
         // Daftarkan command robot lelang baru kita di sini
         Commands\CloseExpiredAuctions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // --- LOGIKA UTAMA (ROBOT LELANG BARU) ---
        // Menjalankan pengecekan pemenang & expired setiap menit.
        // Ini adalah command yang baru saja kita buat.
        $schedule->command('lelang:close-expired')->everyMinute();


        // --- CATATAN PENTING UNTUK KODE LAMA ---
        // Saya sarankan baris 'auction:process' di bawah ini di-KOMEN (//) 
        // jika fungsinya mirip dengan robot baru kita, supaya tidak bentrok (double proses).
        // Tapi kalau fungsinya beda, silakan nyalakan lagi.
        
        // $schedule->command('auction:process')->everyMinute(); 

        // Cleanup produk yang tak diambil (biarkan jika masih perlu)
        $schedule->command('auction:expire')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}