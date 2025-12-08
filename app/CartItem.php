<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User'); 
    }

    // --- RELASI LAMA (MERCH) ---
    public function merchProduct()
    {
        return $this->belongsTo('App\models\MerchProduct', 'merch_product_id');
    }

    public function merchVariant()
    {
        return $this->belongsTo('App\models\MerchProductVariant', 'merch_product_variant_id');
    }

    public function merchSize()
    {
        return $this->belongsTo('App\models\MerchProductVariantSize', 'merch_product_variant_size_id');
    }

    // --- TAMBAHAN BARU (LELANG) ---
    
    // 1. Relasi ke Produk Lelang (Karya)
    // Pastikan namespace 'App\Products' sesuai dengan model Product kamu
    public function auctionProduct()
    {
        return $this->belongsTo('App\Products', 'product_id');
    }

    // 2. Helper Cek Tipe
    public function isAuction()
    {
        return $this->type === 'lelang';
    }
}