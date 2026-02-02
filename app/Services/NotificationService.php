<?php

namespace App\Services;

use App\UserBannerNotification;
use App\Events\AuctionWinnerEvent;

class NotificationService
{
    public function winner($userId, $productId, $title, $price, $checkoutUrl)
    {
        $notif = UserBannerNotification::create([
            'user_id'      => $userId,
            'product_id'   => $productId,
            'type'         => 'winner',
            'title'        => $title,
            'price'        => $price,
            'checkout_url' => $checkoutUrl,
            'is_read'      => 0
        ]);

        event(new AuctionWinnerEvent($notif));
    }
}