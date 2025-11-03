<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = "setting";
    protected $fillable = [
        'id',
        'title',
        'tagline',
        'address',
        'phone',
        'wa',
        'email',
        'favicon',
        'logo',
        'social'
    ];
     protected $casts = [
        'social' => 'array',

    ];
}
