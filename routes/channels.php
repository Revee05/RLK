<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// routes/channels.php
Broadcast::channel('chat', function ($user) {
  return Auth::check();
});
Broadcast::channel('bid', function ($user) {
  return Auth::check();
});

Broadcast::channel('auction-result.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('product.{id}', function ($user, $id) {
    return Auth::check();
});

