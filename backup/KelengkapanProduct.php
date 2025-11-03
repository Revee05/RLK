<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KelengkapanProduct extends Model
{
    protected $table = 'kelengkapan_products';
    protected $fillable = [
        'product_id',
        'kelengkapan_id',
    ];
    public function kelengkapans()
    {
        return $this->belongsToMany(Kelengkapan::class);
    }
    public function products()
    {
        return $this->belongsToMany(Products::class);
    }
}
