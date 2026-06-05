<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'recipient_id',
        'amount',
        'status',
        'type',
        'parent_transaction_id',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    // Type constants
    const TYPE_CREDIT_REQUEST = 'credit_request';
    const TYPE_DONATION = 'donation';
    const TYPE_DONATION_ALLOCATION = 'donation_allocation';
}
