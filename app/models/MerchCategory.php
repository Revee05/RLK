<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MerchCategory extends Model
{
    protected $table = 'merch_categories';

    protected $fillable = [
        'name', 'slug'
    ];

    public function merchProducts()
    {
        return $this->belongsToMany(MerchProduct::class, 'merch_category_product', 'merch_category_id', 'merch_product_id');
    }

    protected static function booted()
    {
        static::saved(function () {
            \Cache::forget('merch_categories_list');
            \Cache::forget('merch_categories_version');
        });
        static::deleted(function () {
            \Cache::forget('merch_categories_list');
            \Cache::forget('merch_categories_version');
        });
    }
}