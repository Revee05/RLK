<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Posts extends Model
{
    // Nama tabel
    protected $table = 'posts';

    // Kolom yang boleh diisi
    protected $fillable = [
        'id',
        'user_id',
        'image',
        'slug',
        'title',
        'body',
        'status',
        'publish_date',
        'post_type',
        'kategori_id',
        'views'
    ];

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    // Hanya ambil data yang tipe-nya "page"
    public function scopePage($query)
    {
        return $query->where('post_type', 'page');
    }

    // Hanya ambil data yang tipe-nya "blog"
    public function scopeBlog($query)
    {
        return $query->where('post_type', 'blog');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Relasi ke tabel tags (many to many)
    public function tags()
    {
        return $this->belongsToMany(Tags::class)->withTimestamps();
    }

    // Relasi ke kategori (many to one)
    public function kategori()
    {
        return $this->belongsTo('App\Kategori', 'kategori_id');
    }

    // Relasi ke user (author)
    public function author()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    // Format tanggal ke gaya Indonesia
    public function getDateIndoAttribute()
    {
        return Carbon::parse($this->created_at)->isoFormat('dddd, D MMMM Y H:mm:ss');
    }

    public function getBodyAttribute($value)
    {
        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5);
    }

    public function images()
    {
        return $this->hasMany(\App\BlogImage::class, 'post_id');
    }

    public function getExcerptAttribute()
    {
        $body = $this->attributes['body'] ?? '';

        if (!$body) {
            return '';
        }

        // Coba decode JSON (editor baru)
        $decoded = json_decode($body, true);

        if (is_array($decoded)) {
            foreach ($decoded as $block) {
                if (
                    isset($block['type']) &&
                    $block['type'] === 'text' &&
                    !empty($block['html'])
                ) {
                    return strip_tags(html_entity_decode($block['html']));
                }
            }
        }

        // Fallback untuk konten lama (HTML biasa)
        return strip_tags(html_entity_decode($body));
    }
}
