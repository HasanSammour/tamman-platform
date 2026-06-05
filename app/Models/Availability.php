<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialist_id', 
        'day_of_week', 
        'start_time', 
        'end_time',
        'is_recurring', 
        'specific_date', 
        'is_available'
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_available' => 'boolean',
        // 'start_time' => 'datetime:H:i',
        // 'end_time' => 'datetime:H:i',
        'specific_date' => 'date',
    ];

    // Relationships
    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    /**
     * Get the day name for this availability slot
     */
    public function getDayNameAttribute()
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        
        return __($days[$this->day_of_week] ?? 'Sunday');
    }
}
