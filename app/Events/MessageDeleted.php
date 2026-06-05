<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversation;
    public $deletedBy;

    public function __construct(Message $message, Conversation $conversation, string $deletedBy)
    {
        $this->message = $message;
        $this->conversation = $conversation;
        $this->deletedBy = $deletedBy; // 'sender', 'receiver', or 'everyone'
    }

    public function broadcastOn()
    {
        // Get both participants
        $participantOne = $this->conversation->participant_one;
        $participantTwo = $this->conversation->participant_two;

        // Broadcast to both users in the conversation
        return [
            new PrivateChannel('chat.' . $participantOne),
            new PrivateChannel('chat.' . $participantTwo),
        ];
    }

    public function broadcastAs()
    {
        return 'MessageDeleted';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
            'deleted_by' => $this->deletedBy,
            'is_deleted_for_everyone' => $this->message->is_deleted_for_everyone,
        ];
    }
}