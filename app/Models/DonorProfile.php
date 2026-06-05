<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'organization_name', 
        'tax_id', 
        'total_donated'
    ];

    protected $casts = [
        'total_donated' => 'decimal:2',
    ];

    // Relationships 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creditTransactions()
    {
        return $this->hasManyThrough(CreditTransaction::class, User::class, 'id', 'donor_id', 'user_id');
    }
}
