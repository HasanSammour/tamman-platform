<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'date_of_birth',
        'profile_image',
        'is_active',
        'total_points',
        'credit_balance',
        'referral_code',
        'referral_used_count',
        'last_referral_reset',
        'referred_by',
        'email_verified_at',
        'last_login_at',
        'last_activity_at',
        'is_online',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'total_points' => 'integer',
            'credit_balance' => 'decimal:2',
            'referral_used_count' => 'integer',
            'last_referral_reset' => 'datetime',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'is_online' => 'boolean',
        ];
    }

    /**
     * Get the full URL for the user's profile image.
     * Checks multiple possible paths and returns the first valid one.
     * 
     * @return string|null
     */
    public function getProfileImageUrl()
    {
        if (!$this->profile_image) {
            return null;
        }

        // Try different possible paths
        $possiblePaths = [
            $this->profile_image, // Original path
            'storage/' . $this->profile_image, // Storage path
            'images/' . $this->profile_image, // Images folder
            'images/profile_seed/' . basename($this->profile_image), // Direct to profile_seed folder
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }

    /**
     * Get profile image URL or return placeholder class for avatar
     * 
     * @return array
     */
    public function getProfileImageAttributeWithFallback()
    {
        $url = $this->getProfileImageUrl();
        $firstLetter = mb_substr($this->name, 0, 1, 'UTF-8');

        return [
            'url' => $url,
            'has_image' => !is_null($url),
            'initial' => $firstLetter,
        ];
    }

    /**
     * Check if user can take a specific test this month
     */
    public function canTakeTest($testType)
    {
        $lastTest = $this->testResults()
            ->where('test_type', $testType)
            ->orderBy('test_date', 'desc')
            ->first();

        if (!$lastTest) {
            return true;
        }

        $now = now();
        $lastDate = $lastTest->test_date;

        // Different calendar month?
        return !($lastDate->year == $now->year && $lastDate->month == $now->month);
    }

    /**
     * Get next available date for a test
     */
    public function getNextTestAvailableDate($testType)
    {
        $lastTest = $this->testResults()
            ->where('test_type', $testType)
            ->orderBy('test_date', 'desc')
            ->first();

        if (!$lastTest) {
            return now();
        }

        return $lastTest->test_date->addMonth()->startOfMonth();
    }

    // Relationships
    public function specialistProfile()
    {
        return $this->hasOne(SpecialistProfile::class);
    }

    public function donorProfile()
    {
        return $this->hasOne(DonorProfile::class);
    }

    public function therapySessionsAsPatient()
    {
        return $this->hasMany(TherapySession::class, 'patient_id');
    }

    public function therapySessionsAsSpecialist()
    {
        return $this->hasMany(TherapySession::class, 'specialist_id');
    }

    public function availability()
    {
        return $this->hasMany(Availability::class, 'specialist_id');
    }

    public function moodLogs()
    {
        return $this->hasMany(MoodLog::class);
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function treatmentPlansAsSpecialist()
    {
        return $this->hasMany(TreatmentPlan::class, 'specialist_id');
    }

    public function treatmentPlansAsPatient()
    {
        return $this->hasMany(TreatmentPlan::class, 'patient_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'specialist_id');
    }

    public function creditTransactionsAsDonor()
    {
        return $this->hasMany(CreditTransaction::class, 'donor_id');
    }

    public function creditTransactionsAsRecipient()
    {
        return $this->hasMany(CreditTransaction::class, 'recipient_id');
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'created_by');
    }

    public function systemLogs()
    {
        return $this->hasMany(SystemLog::class, 'admin_id');
    }

    // Payment Relationships
    public function specialistPayments()
    {
        return $this->hasMany(SpecialistPayment::class, 'specialist_id');
    }

    public function completedSessions()
    {
        return $this->hasMany(TherapySession::class, 'specialist_id')
            ->where('status', 'completed');
    }

    public function getMonthlyEarnings($year, $month)
    {
        return $this->completedSessions()
            ->whereYear('session_datetime', $year)
            ->whereMonth('session_datetime', $month)
            ->get();
    }

    /**
     * Get total earnings (all time)
     */
    public function getTotalEarnings()
    {
        $sessions = $this->completedSessions;
        $fee = $this->specialistProfile->consultation_fee ?? 0;
        $total = 0;

        foreach ($sessions as $session) {
            if ($session->session_type === 'video') {
                $total += $fee;
            } elseif ($session->session_type === 'audio') {
                $total += $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $total += $fee * 0.8;
            }
        }

        return $total;
    }

    /**
     * Get pending payout amount (earnings not yet paid)
     */
    public function getPendingPayoutAmount()
    {
        $fee = $this->specialistProfile->consultation_fee ?? 0;

        // Get all completed sessions
        $sessions = $this->completedSessions()
            ->orderBy('session_datetime', 'asc')
            ->get();

        // Get paid months
        $paidMonths = $this->specialistPayments()
            ->where('status', 'paid')
            ->pluck('month_year')
            ->toArray();

        $pendingEarnings = 0;
        $currentMonth = null;
        $monthEarnings = 0;

        foreach ($sessions as $session) {
            $monthYear = $session->session_datetime->format('m/Y');

            // If month changed, check if this month was paid
            if ($currentMonth && $monthYear != $currentMonth) {
                if (!in_array($currentMonth, $paidMonths)) {
                    $pendingEarnings += $monthEarnings;
                }
                $monthEarnings = 0;
            }

            $currentMonth = $monthYear;

            // Calculate session earning based on type
            if ($session->session_type === 'video') {
                $monthEarnings += $fee;
            } elseif ($session->session_type === 'audio') {
                $monthEarnings += $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $monthEarnings += $fee * 0.8;
            }
        }

        // Add last month if not paid
        if ($currentMonth && !in_array($currentMonth, $paidMonths)) {
            $pendingEarnings += $monthEarnings;
        }

        return $pendingEarnings;
    }

    // ==================== REFERRAL SYSTEM ====================

    /**
     * Generate a unique referral code for new user
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = 'TAM' . Str::upper(Str::random(6));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * User who referred this user
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Users referred by this user
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Check if user can use referral code this week
     */
    public function canUseReferralCode(): bool
    {
        $now = now();

        if (!$this->last_referral_reset) {
            return true;
        }

        // Reset weekly counter if last reset was more than 7 days ago
        if ($this->last_referral_reset->diffInDays($now) >= 7) {
            $this->referral_used_count = 0;
            $this->last_referral_reset = $now;
            $this->save();
            return true;
        }

        return $this->referral_used_count < 1; // 1 referral per week max
    }

    /**
     * Add points from referral
     */
    public function addReferralPoints()
    {
        if ($this->canUseReferralCode()) {
            $points = 100;
            $this->total_points += $points;
            $this->referral_used_count++;
            $this->last_referral_reset = $this->last_referral_reset ?? now();
            $this->save();

            // Record points transaction
            PointTransaction::create([
                'user_id' => $this->id,
                'points' => $points,
                'type' => 'earned',
                'source' => 'referral',
                'description' => 'Bonus for referring a new user',
            ]);

            return true;
        }

        return false;
    }

    // ==================== HELPER METHODS ====================

    public function isSpecialist(): bool
    {
        return $this->hasRole('specialist') && $this->specialistProfile;
    }

    public function isVerifiedSpecialist(): bool
    {
        return $this->isSpecialist() && $this->specialistProfile->is_verified;
    }

    public function isDonor(): bool
    {
        return $this->hasRole('donor');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    /**
     * Get blocked dates
     */
    public function blockedDates()
    {
        return $this->hasMany(Availability::class, 'specialist_id')
            ->where('is_recurring', false)
            ->where('is_available', false);
    }

    // Custome Email Notifications
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Check if user wants to receive a specific notification type
     */
    public function wantsNotification($type)
    {
        $settings = $this->notification_settings ?? [
            'session_reminders' => true,
            'points_earned' => true,
            'new_messages' => true,
            'treatment_tasks' => true,
            'promotional_emails' => false,
        ];

        // If settings is still a JSON string, decode it
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }

        return $settings[$type] ?? true;
    }

    // Reward System Functions //
    public function addPoints($points, $source, $description = null, $referenceId = null, $referenceType = null)
    {
        $this->total_points += $points;
        $this->save();

        return PointTransaction::create([
            'user_id' => $this->id,
            'points' => $points,
            'type' => 'earned',
            'source' => $source,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'description' => $description,
        ]);
    }

    /**
     * Get total points earned (not including redeemed)
     */
    public function getTotalEarnedPoints()
    {
        return PointTransaction::where('user_id', $this->id)
            ->where('type', 'earned')
            ->sum('points');
    }

    /**
     * Get total points redeemed
     */
    public function getTotalRedeemedPoints()
    {
        return PointTransaction::where('user_id', $this->id)
            ->where('type', 'redeemed')
            ->sum('points');
    }

    /**
     * Check if user can redeem a reward
     */
    public function canRedeemReward($pointsNeeded)
    {
        return $this->total_points >= $pointsNeeded;
    }

    /**
     * Get user's rank based on total points
     */
    public function getRank()
    {
        return \App\Helpers\RewardHelper::getRank($this->total_points);
    }

    /**
     * Get next milestone
     */
    public function getNextMilestone()
    {
        return \App\Helpers\RewardHelper::getNextMilestone($this->total_points);
    }

    /**
     * Get user's redemptions
     */
    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Get user's completed redemptions
     */
    public function completedRedemptions()
    {
        return $this->rewardRedemptions()->where('status', 'completed');
    }

    //* -------- Chat System -------- *//
    /**
     * Conversations where user is participant one
     */
    public function conversationsAsParticipantOne()
    {
        return $this->hasMany(Conversation::class, 'participant_one');
    }

    /**
     * Conversations where user is participant two
     */
    public function conversationsAsParticipantTwo()
    {
        return $this->hasMany(Conversation::class, 'participant_two');
    }

    /**
     * All conversations of this user
     */
    public function conversations()
    {
        return Conversation::where('participant_one', $this->id)
            ->orWhere('participant_two', $this->id)
            ->orderBy('last_message_at', 'desc');
    }

    /**
     * Get unread conversations count
     */
    public function getUnreadConversationsCount()
    {
        return Conversation::where(function ($q) {
            $q->where('participant_one', $this->id)
                ->where('unread_count_p_one', '>', 0);
        })->orWhere(function ($q) {
            $q->where('participant_two', $this->id)
                ->where('unread_count_p_two', '>', 0);
        })->count();
    }

    /**
     * Get total unread messages count
     */
    public function getTotalUnreadMessagesCount()
    {
        $unreadCount = 0;
        $conversations = $this->conversations()->get();

        foreach ($conversations as $conversation) {
            $unreadCount += $conversation->getUnreadCountForUser($this->id);
        }

        return $unreadCount;
    }

    /**
     * Update last activity timestamp (for online status)
     */
    public function updateLastActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Check if user is online
     */
    public function isOnline(): bool
    {
        return $this->is_online && $this->last_activity_at && $this->last_activity_at->gt(now()->subMinutes(2));
    }
}
