<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderConfirmationEmail
{
    public function __construct()
    {
        //
    }

    public function handle(OrderStatusUpdated $event)
    {
        $order = $event->order;

        if ($order->status === 'success' && $order->user && $order->user->email) {
            Mail::to($order->user->email)->send(new OrderConfirmationMail($order));

            // Tandai email sudah dikirim
            $order->update(['email_sent' => 1]);
        }
    }
}
