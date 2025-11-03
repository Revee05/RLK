<?php
 
namespace App\Services;
 
use Midtrans\Snap;
 
class CreateSnapTokenService extends Midtrans
{
    protected $order;
 
    public function __construct($order)
    {
        parent::__construct();
 
        $this->order = $order;
    }
 
    public function getSnapToken()
    {
        
        $createdAt = \Carbon\Carbon::parse($this->order->created_at);
        $expiredTime = $createdAt->format('Y-m-d h:m:s')." +0700";
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->orderid_uuid,
                'gross_amount' => $this->order->total_tagihan,
            ],
            'item_details' => [
                [
                    'id' => $this->order->product->id,
                    'price' => $this->order->total_tagihan,
                    'quantity' => 1,
                    'name' => $this->order->product->title,
                ]
            ],
            'customer_details' => [
                'first_name' => $this->order->name,
                'email' => $this->order->user->email,
                'phone' => $this->order->phone,
            ],
             'page_expiry'=> [
                'unit' => 'hours',
                'duration' => 24 // 24 jam
            ],
        ];
 
        $snapToken = Snap::getSnapToken($params);
 
        return $snapToken;
    }
}