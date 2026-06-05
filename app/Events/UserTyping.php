<?php

namespace App\Events;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $conversation;
    public $isTyping;

    public function __construct(User $user, Conversation $conversation, bool $isTyping)
    {
        $this->user = $user;
        $this->conversation = $conversation;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn()
    {
        // Get the other participant (receiver)
        $receiverId = $this->conversation->participant_one == $this->user->id 
            ? $this->conversation->participant_two 
            : $this->conversation->participant_one;

        return [
            new PrivateChannel('chat.' . $receiverId),
        ];
    }

    public function broadcastAs()
    {
        return 'UserTyping';
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'conversation_id' => $this->conversation->id,
            'is_typing' => $this->isTyping,
        ];
    }
}