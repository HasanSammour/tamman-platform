<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RewardRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reward_id', 'points_spent', 'status', 
        'notes', 'metadata', 'redeemed_at', 'completed_at'
    ];

    protected $casts = [
        'points_spent' => 'integer',
        'metadata' => 'array',
        'redeemed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function therapySession()
    {
        return $this->hasOne(TherapySession::class, 'reward_redemption_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helper
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    public function markAsFailed($reason = null)
    {
        $this->status = 'failed';
        $this->notes = $reason;
        $this->save();
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }
}