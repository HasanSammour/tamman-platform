<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TherapySession;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_one',
        'participant_two',
        'is_locked',
        'locked_at',
        'last_message',
        'last_message_at',
        'last_message_by',
        'unread_count_p_one',
        'unread_count_p_two',
        'therapy_session_id',
        'is_text_session',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'last_message_at' => 'datetime',
        'unread_count_p_one' => 'integer',
        'unread_count_p_two' => 'integer',
        'is_text_session' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function participantOne()
    {
        return $this->belongsTo(User::class, 'participant_one');
    }

    public function participantTwo()
    {
        return $this->belongsTo(User::class, 'participant_two');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('sent_at', 'asc');
    }

    public function lastMessageObj()
    {
        return $this->belongsTo(Message::class, 'last_message_by', 'sender_id');
    }

    public function therapySession()
    {
        return $this->belongsTo(TherapySession::class, 'therapy_session_id');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get the other participant in the conversation
     */
    public function getOtherParticipant($userId)
    {
        if ($this->participant_one == $userId) {
            return $this->participant_two;
        }
        return $this->participant_one;
    }

    /**
     * Get all participants of this conversation
     */
    public function getParticipants()
    {
        return [$this->participant_one, $this->participant_two];
    }

    /**
     * Check if a user is part of this conversation
     */
    public function hasParticipant($userId)
    {
        return $this->participant_one == $userId || $this->participant_two == $userId;
    }

    /**
     * Get unread count for a specific user
     */
    public function getUnreadCountForUser($userId)
    {
        if ($this->participant_one == $userId) {
            return $this->unread_count_p_one;
        }
        if ($this->participant_two == $userId) {
            return $this->unread_count_p_two;
        }
        return 0;
    }

    /**
     * Increment unread count for a user
     */
    public function incrementUnreadCount($userId)
    {
        if ($this->participant_one == $userId) {
            $this->increment('unread_count_p_one');
        } elseif ($this->participant_two == $userId) {
            $this->increment('unread_count_p_two');
        }
    }

    /**
     * Reset unread count for a user
     */
    public function resetUnreadCount($userId)
    {
        if ($this->participant_one == $userId) {
            $this->update(['unread_count_p_one' => 0]);
        } elseif ($this->participant_two == $userId) {
            $this->update(['unread_count_p_two' => 0]);
        }
    }

    /**
     * Check if user can send messages in this conversation
     */
    public function canSendMessage($userId)
    {
        $user = User::find($userId);

        // User must be a participant
        if (!$this->hasParticipant($userId)) {
            return false;
        }

        // Specialist can always send
        if ($user && $user->hasRole('specialist')) {
            return true;
        }

        // Patient can send only if conversation is NOT locked
        if ($user && $user->hasRole('patient')) {
            return !$this->is_locked;
        }

        return true;
    }

    /**
     * Lock the conversation (specialist ends session)
     */
    public function lock()
    {
        return $this->update([
            'is_locked' => true,
            'locked_at' => now(),
        ]);
    }

    /**
     * Unlock the conversation (specialist reopens session)
     */
    public function unlock()
    {
        return $this->update([
            'is_locked' => false,
            'locked_at' => null,
        ]);
    }

    /**
     * Update last message preview
     */
    public function updateLastMessage(Message $message)
    {
        return $this->update([
            'last_message' => substr($message->content, 0, 100),
            'last_message_at' => $message->sent_at,
            'last_message_by' => $message->sender_id,
        ]);
    }

    /**
     * Check if conversation has upcoming text session
     */
    public function hasUpcomingTextSession()
    {
        return TherapySession::where(function ($q) {
            $q->where('patient_id', $this->participant_one)
                ->where('specialist_id', $this->participant_two);
        })->orWhere(function ($q) {
            $q->where('patient_id', $this->participant_two)
                ->where('specialist_id', $this->participant_one);
        })->where('session_type', 'text')
            ->where('session_datetime', '>', now())
            ->where('status', 'scheduled')
            ->exists();
    }

    /**
     * Get upcoming text session info (for badge)
     */
    public function getUpcomingTextSession()
    {
        return TherapySession::where(function ($q) {
            $q->where('patient_id', $this->participant_one)
                ->where('specialist_id', $this->participant_two);
        })->orWhere(function ($q) {
            $q->where('patient_id', $this->participant_two)
                ->where('specialist_id', $this->participant_one);
        })->where('session_type', 'text')
            ->where('session_datetime', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('session_datetime', 'asc')
            ->first();
    }

    public function otherUser()
    {
        $userId = auth()->id();
        if ($this->participant_one == $userId) {
            return User::find($this->participant_two);
        }
        return User::find($this->participant_one);
    }
}
