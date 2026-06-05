<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// User's personal channel for notifications
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Conversation channel for chat messages
Broadcast::channel('chat.conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    return $conversation->participant_one == $user->id || 
           $conversation->participant_two == $user->id;
});

// Presence channel for online users
Broadcast::channel('online', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->getProfileImageUrl(),
    ];
});