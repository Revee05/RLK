<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    protected static function boot()
    {
        parent::boot();

        $forget = function () {
            Cache::forget('merch_categories_list');
            Cache::forget('merch_categories_version');
        };

        static::created($forget);
        static::updated($forget);
        static::deleted($forget);
    }
}