<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserBannerNotification extends Model
{
    protected $table = 'user_banner_notification';

    protected $fillable = [
        'user_id',
        'product_id',
        'type',
        'title',
        'price',
        'checkout_url',
        'is_read',
    ];
}
