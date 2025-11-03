<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{

    protected $table = 'tags';
    protected $fillable = [
        'id',
        'name',
        'slug',
    ];
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
