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
    public $winnerBid;
    public $winnerPrice;

    public function __construct($bid, $winnerBid = null)
    {
        $this->bid = $bid;
        $this->winnerBid = $winnerBid;
        $this->winnerPrice = $winnerBid ? $winnerBid->price : null;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Hasil Lelang — Anda belum menang')
                    ->view('mail.bid_notifikasi')
                    ->with([
                        'bid' => $this->bid,
                        'winnerBid' => $this->winnerBid,
                        'winnerPrice' => $this->winnerPrice,
                    ]);
    }
}
