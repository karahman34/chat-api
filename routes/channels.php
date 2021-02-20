<?php

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('user.{user}', function ($auth, User $user) {
    return (int) $auth->id === (int) $user->id;
});

Broadcast::channel('conversation.to.{user}', function ($auth, User $user) {
    return Conversation::where('user_id', $auth->id)
    					->where('receiver_id', $user->id)
    					->exists();
});
