<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MerchProduct extends Model
{
    protected $table = 'merch_products';

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'stock', 'status', 'discount', 'type'
    ];


    // Relasi ke kategori (many to many)
    public function variants()
    {
        return $this->hasMany(MerchProductVariant::class, 'merch_product_id');
    }

    public function categories()
    {
        return $this->belongsToMany(MerchCategory::class, 'merch_category_product', 'merch_product_id', 'merch_category_id');
    }
}