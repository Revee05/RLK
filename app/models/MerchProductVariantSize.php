<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MerchProductVariantSize extends Model
{
    protected $table = 'merch_product_variant_sizes';

    protected $fillable = [
        'merch_product_variant_id', 'size', 'stock', 'price', 'discount'
    ];

    public function variant()
    {
        return $this->belongsTo(MerchProductVariant::class, 'merch_product_variant_id');
    }
}