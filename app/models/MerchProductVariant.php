<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MerchProductVariant extends Model
{
    protected $table = 'merch_product_variants';

    protected $fillable = [
        'merch_product_id',
        'name',
        'code',
        'is_default',
        'stock',
        'price',
        'discount',
    ];

    public function product()
    {
        return $this->belongsTo(MerchProduct::class, 'merch_product_id');
    }

    public function images()
    {
        return $this->hasMany(MerchProductVariantImage::class, 'merch_product_variant_id');
    }

    public function sizes()
    {
        return $this->hasMany(MerchProductVariantSize::class, 'merch_product_variant_id');
    }

    // helper untuk cek apakah variant punya size
    public function hasSizes()
    {
        return $this->sizes()->count() > 0;
    }
}