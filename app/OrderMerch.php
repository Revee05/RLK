<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderMerch extends Model
{
    protected $table = 'orders_merch';

    protected $fillable = [
        'user_id',
        'address_id',
        'items',
        'shipping',
        'gift_wrap',
        'shipper_id',
        'jenis_ongkir',
        'total_ongkir',
        'total_tagihan',
        'invoice',
        'status',
        'payment_method',
        'payment_channel',
        'payment_destination',
        'stock_reduced',
        'snap_token',
        'payment_url',
        'paid_at',
        'note',
        'email_sent',
    ];

    protected $casts = [
        'items' => 'array',
        'shipping' => 'array',
        'gift_wrap' => 'boolean',
        'stock_reduced' => 'boolean',
        'email_sent' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address(){
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function shipper(){
        return $this->belongsTo(Shipper::class);
    }
}
