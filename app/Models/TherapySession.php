<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TherapySession extends Model
{
    use HasFactory;

    protected $table = 'therapy_sessions';

    protected $fillable = [
        'patient_id',
        'specialist_id',
        'session_datetime',
        'duration_minutes',
        'status',
        'session_type',
        'meeting_link',
        'secure_room_name',
        'specialist_joined',
        'patient_joined',
        'specialist_joined_at',
        'patient_joined_at',
        'notes',
        'points_awarded',
        'is_paid_by_credit',
        'is_free',
        'reward_redemption_id',
    ];

    protected $casts = [
        'session_datetime' => 'datetime',
        'is_paid_by_credit' => 'boolean',
        'points_awarded' => 'integer',
        'specialist_joined' => 'boolean',
        'patient_joined' => 'boolean',
        'specialist_joined_at' => 'datetime',
        'patient_joined_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'session_id');
    }

    public function rewardRedemption()
    {
        return $this->belongsTo(RewardRedemption::class, 'reward_redemption_id');
    }

    public function messages()
    {
        return $this->hasManyThrough(Message::class, Conversation::class, 'therapy_session_id', 'conversation_id');
    }

    /**
     * Get the conversation for this session (one-to-one)
     */
    public function conversation()
    {
        return $this->hasOne(Conversation::class, 'therapy_session_id');
    }

    /**
     * Check if session is free (redeemed from reward)
     */
    public function isFree(): bool
    {
        return $this->is_free || $this->reward_redemption_id !== null;
    }

    /**
     * Generate a cryptographically secure, unguessable room name
     * 
     * @return string
     */
    public static function generateSecureRoomName()
    {
        // Generate 64 characters of random hex (32 bytes = 64 hex chars)
        $randomBytes = random_bytes(32);
        $randomHex = bin2hex($randomBytes);

        return 'tamman-' . $randomHex;
    }

    /**
     * Check if both participants have joined the session
     */
    public function isFull(): bool
    {
        return $this->specialist_joined && $this->patient_joined;
    }

    /**
     * Check if a specific user has joined
     */
    public function hasUserJoined($userId): bool
    {
        if ($this->specialist_id == $userId) {
            return $this->specialist_joined;
        }
        if ($this->patient_id == $userId) {
            return $this->patient_joined;
        }
        return false;
    }
}
