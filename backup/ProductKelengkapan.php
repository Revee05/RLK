<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductKelengkapan extends Model
{
    public function kelengkapans()
    {
        return $this->belongsToMany(Kelengkapan::class);
    }
    public function products()
    {
        return $this->belongsToMany(Products::class);
    }
}
