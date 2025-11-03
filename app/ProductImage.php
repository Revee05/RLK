<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $fillable = [
        'products_id',
        'name',
        'path',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    function produk(){
        return $this->belongsTo('App\Products','products_id');
    }
}
