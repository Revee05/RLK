<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $items;

    public function __construct($order)
    {
        $this->order = $order;

        $this->items = is_array($order->items)
            ? $order->items
            : json_decode($order->items, true);
    }

    public function build()
    {
        return $this->subject('Konfirmasi Pesanan Anda')
                    ->markdown('emails.orders.confirmation')
                    ->with([
                        'order' => $this->order,
                        'items' => $this->items,
                    ]);
    }
}
