<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $fillable = [
        'name',
        'slug',
        'cat_type',
    ];
    public function scopeProduct($query)
    {
        return $query->where('cat_type','product');
    }
    public function scopeBlog($query)
    {
        return $query->where('cat_type','blog');
    }
    
    public function products()
    {
        return $this->hasMany('App\Products', 'kategori_id');
    }
    
}
