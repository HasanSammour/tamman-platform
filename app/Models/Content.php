<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by', 
        'title', 
        'body', 
        'type', 
        'media_url', 
        'is_published', 
        'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper Functions
    // Scope for published content
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at');
    }
    // Scope by type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
