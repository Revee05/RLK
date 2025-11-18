<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MerchProductVariantImage extends Model
{
    protected $table = 'merch_product_variant_images';

    protected $fillable = [
        'merch_product_variant_id', 'image_path', 'label'
    ];

    public function variant()
    {
        return $this->belongsTo(MerchProductVariant::class, 'merch_product_variant_id');
    }
}