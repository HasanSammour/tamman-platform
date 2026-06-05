<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 
        'reviewer_id', 
        'specialist_id', 
        'rating', 
        'comment'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(TherapySession::class, 'session_id');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }
}
