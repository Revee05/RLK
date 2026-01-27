<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Posts;

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
        return $this->belongsToMany(
            Posts::class,
            'posts_tags',
            'tags_id',
            'posts_id'
        )->withTimestamps();
    }
}
