<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = [];

    // Relasi ke User (Sudah Benar)
    public function user()
    {
        return $this->belongsTo('App\User'); 
    }

    public function product()
    {
        // Ganti 'App\Product' menjadi 'App\Products'
        return $this->belongsTo('App\Products', 'product_id'); 
    }
}
