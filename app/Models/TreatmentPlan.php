<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreatmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialist_id',
        'patient_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function tasks()
    {
        return $this->hasMany(TreatmentTask::class, 'plan_id');
    }
}
