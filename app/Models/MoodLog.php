<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MoodLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'mood_value', 
        'mood_label', 
        'notes', 
        'log_date'
    ];

    protected $casts = [
        'log_date' => 'date',
        'mood_value' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
