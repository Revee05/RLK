<?php


// === Namespace untuk event BidSent ===
namespace App\Events;


// === Import trait dan interface yang diperlukan untuk event broadcasting ===
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


// === Event BidSent digunakan untuk broadcast bid terbaru secara realtime ke client yang subscribe channel produk terkait ===
class BidSent implements ShouldBroadcastNow
{
    // === Trait Laravel untuk event broadcasting dan serialisasi ===
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // === Harga bid terbaru yang akan dibroadcast ===
    public $price;
    // === ID produk yang terkait dengan bid ===
    public $productId;

    // === Konstruktor event, menerima harga dan productId ===
    public function __construct($price, $productId)
    {
        $this->price = $price;
        $this->productId = $productId;
    }

    // === Nama event yang akan dibroadcast ke client (frontend) ===
    public function broadcastAs()
    {
        return 'BidSent';
    }

    // === Data yang dikirim ke client saat event dibroadcast ===
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

    // === Channel privat tempat event akan dibroadcast, hanya user tertentu yang bisa subscribe ===
    public function broadcastOn()
    {
        return new PrivateChannel('product.' . $this->productId);
    }
}
