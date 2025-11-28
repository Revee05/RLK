<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Karya extends Model
{
    protected $table = "karya";
    protected $fillable = [
        'name',
        'slug',
        'description',
        'bio',
        'social',
        'image',
        'address',
    ];
    protected $casts = [
        'social' => 'array',

    ];
    public function product(){
        return $this->hasMany('App\Products');
    }
}
