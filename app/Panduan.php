<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Panduan extends Model
{
    protected $table = 'panduan';

    protected $fillable = [
        'title',
        'slug',
        'file_path',
    ];
}
