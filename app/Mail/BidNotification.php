<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Bid;

class BidNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bid;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Bid $bid)
    {
         $this->bid = $bid;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bid Notification')->view('mail.bid_notifikasi');
    }
}
