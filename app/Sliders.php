<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sliders extends Model
{
    protected $table = 'sliders';
    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
