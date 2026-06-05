<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'points', 
        'type', 
        'source', 
        'reference_id', 
        'reference_type', 
        'description'
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // This is for Polymorphic Relationships - it allows a model to belong to 
    // multiple other models.
    public function reference()
    {
        return $this->morphTo();
    }
    // Explanation:
    // In PointTransaction, the reference() method lets a transaction point to 
    // different types of records:
    // A point transaction can reference:
    // - MoodLog (points from mood tracking)
    // - TherapySession (points from attending session)
    // - TestResult (points from taking test)
    // - TreatmentTask (points from completing task)
    
    // How it works: The table has two columns:
    // reference_id - stores the ID of the related record
    // reference_type - stores the model class name (e.g., 'App\Models\MoodLog')

    // Sources constants
    const SOURCE_MOOD_TRACKING = 'mood_tracking';
    const SOURCE_SESSION_ATTENDANCE = 'session_attendance';
    const SOURCE_TEST_COMPLETED = 'test_completed';
    const SOURCE_TASK_COMPLETED = 'task_completed';
    const SOURCE_SPECIALIST_RATING = 'specialist_rating';
}
