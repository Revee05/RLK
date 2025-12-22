<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionResultEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $type;     // winner / loser
    public $title;    // nama produk
    public $price;    // harga akhir
    public $checkoutUrl;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $type, $title, $price, $checkoutUrl = null)
    {
        $this->userId      = $userId;
        $this->type        = $type;
        $this->title       = $title;
        $this->price       = $price;
        $this->checkoutUrl = $checkoutUrl;
    }

    /**
     * Broadcast to a PRIVATE channel hanya user yang bersangkutan dapat notifikasi.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('auction-result.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'AuctionResultEvent';
    }

    // optional: payload customization
    public function broadcastWith()
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'price' => $this->price,
            'checkout_url' => $this->checkoutUrl,
        ];
    }
}
