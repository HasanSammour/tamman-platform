<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecialistPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialist_id',
        'amount',
        'month_year',
        'platform_fee',
        'final_amount',
        'status',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helpers
    public function markAsPaid($notes = null)
    {
        $this->status = 'paid';
        $this->paid_at = now();
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();
    }

    public function markAsFailed($reason = null)
    {
        $this->status = 'failed';
        if ($reason) {
            $this->notes = $reason;
        }
        $this->save();
    }

    public static function getMonthYear($date = null)
    {
        $date = $date ?? now();
        return $date->format('m/Y');
    }
}