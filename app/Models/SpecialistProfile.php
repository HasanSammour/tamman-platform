<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecialistProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'specialization',
        'qualifications',
        'bio',
        'consultation_fee',
        'languages',
        'experience_years',
        'certificate_file',
        'license_file',
        'application_notes',
        'application_status',
        'applied_at',
        'rating_avg',
        'total_sessions',
        'is_verified',
        'verified_at'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'consultation_fee' => 'decimal:2',
        'rating_avg' => 'float',
        'experience_years' => 'integer',
    ];

    // Relationships 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function therapySessions()
    {
        return $this->hasManyThrough(TherapySession::class, User::class, 'id', 'specialist_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'specialist_id', 'user_id');
    }

    public function availability()
    {
        return $this->hasMany(Availability::class, 'specialist_id', 'user_id');
    }

    public function updateRating()
    {
        $average = $this->reviews()->avg('rating');
        $this->rating_avg = $average ?? 0;
        $this->save();
    }

    /**
     * Get the full URL for the certificate file.
     * Checks multiple possible paths including seeded data.
     * 
     * @return string|null
     */
    public function getCertificateUrl()
    {
        if (!$this->certificate_file) {
            return null;
        }

        $filename = basename($this->certificate_file);

        // Try different possible paths
        $possiblePaths = [
            $this->certificate_file, // Original path from DB
            'storage/' . $this->certificate_file, // Storage disk path
            'certificates/' . $filename, // Certificates folder in public
            'images/certificate_file_seed/' . $filename, // Seeded certificate folder
            'certificate_file_seed/' . $filename, // Direct seeded folder
        ];

        foreach ($possiblePaths as $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                return asset($path);
            }
        }

        // Also try using Storage facade for storage disk
        if (\Storage::disk('public')->exists($this->certificate_file)) {
            return \Storage::disk('public')->url($this->certificate_file);
        }

        return null;
    }

    /**
     * Get the full URL for the license file.
     * Checks multiple possible paths including seeded data.
     * 
     * @return string|null
     */
    public function getLicenseUrl()
    {
        if (!$this->license_file) {
            return null;
        }

        $filename = basename($this->license_file);

        // Try different possible paths
        $possiblePaths = [
            $this->license_file, // Original path from DB
            'storage/' . $this->license_file, // Storage disk path
            'licenses/' . $filename, // Licenses folder in public
            'images/licence_file_seed/' . $filename, // Seeded license folder
            'licence_file_seed/' . $filename, // Direct seeded folder
        ];

        foreach ($possiblePaths as $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                return asset($path);
            }
        }

        // Also try using Storage facade for storage disk
        if (\Storage::disk('public')->exists($this->license_file)) {
            return \Storage::disk('public')->url($this->license_file);
        }

        return null;
    }

    /**
     * Get certificate info with URL and file type.
     * 
     * @return array
     */
    public function getCertificateInfo()
    {
        $url = $this->getCertificateUrl();
        $extension = $url ? strtolower(pathinfo($url, PATHINFO_EXTENSION)) : null;

        return [
            'url' => $url,
            'has_file' => !is_null($url),
            'is_image' => in_array($extension, ['jpg', 'jpeg', 'png', 'gif']),
            'extension' => $extension,
            'filename' => basename($this->certificate_file ?? ''),
        ];
    }

    /**
     * Get license info with URL and file type.
     * 
     * @return array
     */
    public function getLicenseInfo()
    {
        $url = $this->getLicenseUrl();
        $extension = $url ? strtolower(pathinfo($url, PATHINFO_EXTENSION)) : null;

        return [
            'url' => $url,
            'has_file' => !is_null($url),
            'is_image' => in_array($extension, ['jpg', 'jpeg', 'png', 'gif']),
            'extension' => $extension,
            'filename' => basename($this->license_file ?? ''),
        ];
    }

    /**
     * Calculate total earnings for a given period
     */
    public function calculateEarnings($startDate = null, $endDate = null)
    {
        $query = TherapySession::where('specialist_id', $this->user_id)
            ->where('status', 'completed');
        
        if ($startDate) {
            $query->whereDate('session_datetime', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('session_datetime', '<=', $endDate);
        }
        
        $sessions = $query->get();
        $fee = $this->consultation_fee;
        $earnings = 0;
        
        foreach ($sessions as $session) {
            if ($session->session_type === 'video') {
                $earnings += $fee;
            } elseif ($session->session_type === 'audio') {
                $earnings += $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $earnings += $fee * 0.8;
            }
        }
        
        return $earnings;
    }
}