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
    public $shipping;
    public $orderType;

    public function __construct($order)
    {
        $this->order = $order;

        // Determine order type
        $this->orderType = $order instanceof \App\Order ? 'lelang' : 'merch';

        // Parse items JSON
        $this->items = is_array($order->items)
            ? $order->items
            : json_decode($order->items, true) ?? [];

        // Parse shipping JSON
        $this->shipping = is_array($order->shipping)
            ? $order->shipping
            : json_decode($order->shipping, true) ?? [];
    }

    public function build()
    {
        return $this->subject('Konfirmasi Pesanan Anda - ' . ($this->order->invoice ?? 'N/A'))
                    ->markdown('emails.orders.confirmation')
                    ->with([
                        'order' => $this->order,
                        'items' => $this->items,
                        'shipping' => $this->shipping,
                        'orderType' => $this->orderType,
                    ]);
    }
}
