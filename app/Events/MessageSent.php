<?php

namespace App\Events;

use App\User;
use App\Bid;
use App\Products;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
     /**
     * User that sent the message
     *
     * @var User
     */
    public $user;
    public $bid;
    public $tanggal;
    public $productId;
    /**
     * Bid details
     *
     * @var Bid
     */
    // public $bid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $bid, $tanggal, $productId)
    {
        $this->user = $user;
        $this->bid = $bid;
        $this->tanggal = $tanggal;
        $this->productId = $productId;
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
    
    public function broadcastWith()
    {
        \Log::info('[MessageSent Event] Broadcasting data', [
            'user' => $this->user->name,
            'bid' => $this->bid,
            'productId' => $this->productId,
            'channel' => 'product.' . $this->productId
        ]);
        
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'bid' => $this->bid,
            'message' => $this->bid,
            'tanggal' => $this->tanggal,
            'productId' => $this->productId
        ];
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('product.' . $this->productId);
    }
}
