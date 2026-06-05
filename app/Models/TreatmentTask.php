<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreatmentTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id', 
        'title', 
        'description', 
        'is_completed', 
        'due_date', 
        'points_reward', 
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'points_reward' => 'integer',
    ];

    // Relationships
    public function plan()
    {
        return $this->belongsTo(TreatmentPlan::class, 'plan_id');
    }
}
