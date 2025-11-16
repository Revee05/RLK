<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MerchProductImage extends Model
{
    protected $table = 'merch_product_images';

    protected $fillable = [
        'merch_product_id', 'image_path', 'label'
    ];

    public function merchProduct()
    {
        return $this->belongsTo(MerchProduct::class, 'merch_product_id');
    }
}