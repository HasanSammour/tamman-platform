<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'points_needed', 
        'type', 
        'session_type', 
        'credit_amount', 
        'description', 
        'is_active', 
        'sort_order'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'credit_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'points_needed' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function redemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeFreeSessions($query)
    {
        return $query->where('type', 'free_session');
    }

    public function scopeDonates($query)
    {
        return $query->where('type', 'donate');
    }

    // Helpers
    public function getName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $names = $this->name;
        return $names[$locale] ?? $names['en'] ?? '';
    }

    public function getDescription($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $descriptions = $this->description;
        return $descriptions[$locale] ?? $descriptions['en'] ?? '';
    }

    /**
     * Check if user has a pending free session redemption
     */
    public function userHasPendingFreeSession($userId): bool
    {
        return RewardRedemption::where('user_id', $userId)
            ->where('reward_id', $this->id)
            ->where('status', 'completed')
            ->whereDoesntHave('therapySession')
            ->exists();
    }
    
    /**
     * Get user's pending free session redemption
     */
    public function getPendingFreeSessionRedemption($userId)
    {
        return RewardRedemption::where('user_id', $userId)
            ->where('reward_id', $this->id)
            ->where('status', 'completed')
            ->whereDoesntHave('therapySession')
            ->first();
    }
}