<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    // Pastikan semua nama kolom ini ada di sini!
    protected $fillable = [
        'title', 
        'subtitle', 
        'online_period', // PENTING: Tambahkan ini
        'offline_date',  // PENTING: Tambahkan ini
        'location',      // PENTING: Tambahkan ini
        'image', 
        'image_mobile',  // PENTING: Tambahkan ini
        'link', 
        'description', 
        'status'
    ];
}
