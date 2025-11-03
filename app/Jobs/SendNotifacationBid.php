<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Bid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BidNotification;

class SendNotifacationBid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
    }

    /**
     * Execute the job.
     *
     * 
     * @return void
     */
    public function handle()
    {

        $mail = config('mail.bid_notification_mail');

        if ($this->bid) {
            Mail::to($mail)->send(new BidNotification($this->bid));
            Log::info("Bid Notifikasi: => {$this->bid->user->name}");
        }
    }
}
