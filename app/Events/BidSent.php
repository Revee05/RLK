<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $price;
    public $productId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($price, $productId)
    {
        $this->price = $price;
        $this->productId = $productId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastAs()
    {
        return 'BidSent';
    }

    public function broadcastWith()
    {
        \Log::info('[BidSent Event] Broadcasting data', [
            'price' => $this->price,
            'productId' => $this->productId,
            'channel' => 'product.' . $this->productId
        ]);
        
        return [
            'price' => $this->price,
            'productId' => $this->productId
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('product.' . $this->productId);
    }
}
