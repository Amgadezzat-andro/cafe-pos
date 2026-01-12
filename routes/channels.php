<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The required callback is used to determine if an
| authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channels - allow users to listen on their own chat channels
Broadcast::privateChannel('chat-{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Notification channels - allow users to listen on their own notification channels
Broadcast::privateChannel('notifications-{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
