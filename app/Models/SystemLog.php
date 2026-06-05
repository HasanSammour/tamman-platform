<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id', 
        'action', 
        'details'
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

     /**
     * Get details as formatted JSON string for display
     */
    public function getDetailsFormattedAttribute()
    {
        if (is_array($this->details)) {
            return json_encode($this->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        return $this->details;
    }

    /**
     * Get details as readable text
     */
    public function getDetailsTextAttribute()
    {
        if (is_array($this->details)) {
            $text = '';
            foreach ($this->details as $key => $value) {
                if ($value && $value !== 'null') {
                    $keyName = ucfirst(str_replace('_', ' ', $key));
                    $text .= $keyName . ': ' . (is_array($value) ? json_encode($value) : $value) . "\n";
                }
            }
            return trim($text);
        }
        return $this->details;
    }
}
