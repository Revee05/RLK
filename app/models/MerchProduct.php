<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MerchProduct extends Model
{
    protected $table = 'merch_products';

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'stock', 'status', 'discount', 'type',
        'size_guide_content', 'size_guide_image', 'guide_button_label' // kolom baru
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

    public function defaultVariant()
    {
        return $this->hasOne(\App\models\MerchProductVariant::class, 'merch_product_id')->where('is_default', 1);
    }

    // Helper untuk cek ketersediaan panduan produk
    public function hasGuide()
    {
        return !empty($this->size_guide_content) || !empty($this->size_guide_image);
    }
}