<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'description',
        'status',
    ];
}
