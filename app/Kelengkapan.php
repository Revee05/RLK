<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kelengkapan extends Model
{
    protected $table = 'kelengkapans';
    protected $fillable = [
        'id',
        'name',
        'slug',
    ];
    public function products()
    {
        return $this->belongsToMany(Products::class);
    }
}
