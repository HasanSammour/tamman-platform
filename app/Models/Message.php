<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'conversation_id',
        'content',
        'is_read',
        'is_system_message',
        'is_deleted_by_sender',
        'is_deleted_by_receiver',
        'is_deleted_for_everyone',
        'sent_at',
        'edited_at',
        'deleted_for_everyone_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_system_message' => 'boolean',
        'is_deleted_by_sender' => 'boolean',
        'is_deleted_by_receiver' => 'boolean',
        'is_deleted_for_everyone' => 'boolean',
        'sent_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_for_everyone_at' => 'datetime',
    ];

    // Relationships
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

     // ==================== HELPER METHODS ====================

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    /**
     * Check if message is visible to a specific user
     */
    public function isVisibleToUser($userId)
    {
        if ($userId == $this->sender_id) {
            return !$this->is_deleted_by_sender;
        }
        if ($userId == $this->receiver_id) {
            return !$this->is_deleted_by_receiver;
        }
        return false;
    }

    /**
     * Soft delete for specific user
     */
    public function deleteForUser($userId)
    {
        if ($userId == $this->sender_id) {
            $this->update(['is_deleted_by_sender' => true]);
        } elseif ($userId == $this->receiver_id) {
            $this->update(['is_deleted_by_receiver' => true]);
        }

        // If both deleted, hard delete
        if ($this->is_deleted_by_sender && $this->is_deleted_by_receiver) {
            $this->delete();
        }

        return true;
    }

    /**
     * Edit message content
     */
    public function editContent($newContent)
    {
        return $this->update([
            'content' => $newContent,
            'edited_at' => now(),
        ]);
    }

    /**
     * Delete message for everyone (both users)
     */
    public function deleteForEveryone(): void
    {
        $this->update([
            'content' => 'This message was deleted',
            'is_deleted_for_everyone' => true,
            'deleted_for_everyone_at' => now(),
            'edited_at' => null,
        ]);
    }
}
