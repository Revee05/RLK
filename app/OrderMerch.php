<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MerchOrder extends Model
{
    protected $table = 'orders_merch';

    protected $fillable = [
        'user_id',
        'address_id',
        'items',
        'shipper_id',
        'jenis_ongkir',
        'total_ongkir',
        'total_tagihan',
        'invoice',
        'status',
        'snap_token',
    ];

    protected $casts = [
        'items' => 'array',
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
