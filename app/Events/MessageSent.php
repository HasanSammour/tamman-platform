<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversation;

    public function __construct(Message $message, Conversation $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;
        $this->message->load('sender');
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
        return 'MessageSent';
    }

    public function broadcastWith()
    {
        $data = [
            'id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'sender_name' => $this->message->sender->name,
                'content' => $this->message->content,
                'is_system_message' => (bool) $this->message->is_system_message,
                'is_deleted_for_everyone' => (bool) $this->message->is_deleted_for_everyone,
                'therapy_session_id' => $this->message->therapy_session_id,
                'sent_at' => $this->message->sent_at->toISOString(),
            ],
        ];

        \Log::info('MessageSent broadcast data:', $data);

        return $data;
    }
}
