<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'notif_web',
        'notif_email',
        'notif_whatsapp',
        'type',
        'title',
        'price',
        'checkout_url',
        'is_read',
    ];
}
