<?php


// === Namespace untuk event MessageSent ===
namespace App\Events;


// === Import model dan trait yang diperlukan untuk event broadcasting ===
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


// === Event MessageSent digunakan untuk broadcast pesan/bid baru ke client yang subscribe channel produk terkait ===
class MessageSent implements ShouldBroadcastNow
{
    // === Trait Laravel untuk event broadcasting dan serialisasi ===
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // === User yang mengirim pesan/bid ===
    public $user;
    // === Nilai bid atau pesan yang dikirim ===
    public $bid;
    // === Tanggal/waktu bid dikirim ===
    public $tanggal;
    // === ID produk yang terkait dengan bid/pesan ===
    public $productId;

    // === Konstruktor event, menerima user, bid, tanggal, dan productId ===
    public function __construct(User $user, $bid, $tanggal, $productId)
    {
        $this->user = $user;
        $this->bid = $bid;
        $this->tanggal = $tanggal;
        $this->productId = $productId;
    }

    // === Nama event yang akan dibroadcast ke client (frontend) ===
    public function broadcastAs()
    {
        return 'MessageSent';
    }

    // === Data yang dikirim ke client saat event dibroadcast ===
    public function broadcastWith()
    {
        \Log::info('[MessageSent Event] Broadcasting data', [
            'user' => $this->user->name,
            'bid' => $this->bid,
            'productId' => $this->productId,
            'channel' => 'product.' . $this->productId
        ]);

        // ambil produk untuk compute step/nominals (safe-check)
        $product = \App\Products::find($this->productId);
        $step = 0;
        if ($product) {
            $step = intval($product->kelipatan_bid ?? $product->kelipatan ?? 0);
        }
        $useStep = $step > 0 ? $step : 10000;
        $nominals = [];
        for ($i = 1; $i <= 5; $i++) {
            $nominals[] = intval($this->bid) + ($useStep * $i);
        }

        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'bid' => $this->bid,
            'message' => $this->bid,
            'tanggal' => $this->tanggal,
            'productId' => $this->productId,
            'step' => $step,
            'nominals' => $nominals,
        ];
    }

    // === Channel privat tempat event akan dibroadcast, hanya user tertentu yang bisa subscribe ===
    public function broadcastOn()
    {
        return new PrivateChannel('product.' . $this->productId);
    }
}