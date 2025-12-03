<?php

namespace App\Events;

use App\User;
use App\Bid;
use App\Products;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
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
    public function __construct(User $user, $bid, $tanggal)
    {
        $this->user = $user;
        $this->bid      = $bid;
        $this->tanggal  = $tanggal;
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('product.' . request()->produk);
    }
}
