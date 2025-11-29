<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    protected $table = 'user_notification_settings';

    protected $fillable = [
        'user_id',
        'email_enabled',
        'email_order_status',
        'email_promo',
        'wa_enabled',
        'wa_order_status',
        'wa_promo',
        'banner_enabled',
        'banner_order_status',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'email_order_status' => 'boolean',
        'email_promo' => 'boolean',
        'wa_enabled' => 'boolean',
        'wa_order_status' => 'boolean',
        'wa_promo' => 'boolean',
        'banner_enabled' => 'boolean',
        'banner_order_status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
