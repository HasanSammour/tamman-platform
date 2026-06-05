<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\TherapySession;
use App\Models\MoodLog;
use App\Models\TestResult;
use App\Models\PointTransaction;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Upcoming sessions
        $upcomingSessions = TherapySession::where('patient_id', $user->id)
            ->where('session_datetime', '>', now())
            ->where('status', 'scheduled')
            ->with('specialist')
            ->orderBy('session_datetime', 'asc')
            ->take(5)
            ->get();
        
        // Recent completed sessions
        $recentSessions = TherapySession::where('patient_id', $user->id)
            ->where('status', 'completed')
            ->with(['specialist', 'review'])
            ->orderBy('session_datetime', 'desc')
            ->take(5)
            ->get();
        
        // Mood data for chart (last 30 days)
        $moodData = MoodLog::where('user_id', $user->id)
            ->where('log_date', '>=', now()->subDays(30))
            ->orderBy('log_date', 'asc')
            ->get();
        
        $moodLabels = $moodData->pluck('log_date')->map(function($date) {
            return $date->translatedFormat('M d');
        });
        $moodValues = $moodData->pluck('mood_value');
        
        // 6 TESTS WITH AVAILABILITY CHECK
        $testTypes = ['phq9', 'gad7', 'pcl5', 'isi', 'pss', 'cis'];
        $testsData = [];
        
        foreach ($testTypes as $type) {
            $lastTest = TestResult::where('user_id', $user->id)
                ->where('test_type', $type)
                ->latest('test_date')
                ->first();
            
            $testsData[$type] = [
                'last_test' => $lastTest,
                'can_take' => $user->canTakeTest($type),
                'next_available_date' => $user->getNextTestAvailableDate($type),
                'has_taken_before' => !is_null($lastTest),
                'last_score' => $lastTest?->score,
                'last_level' => $lastTest?->result_level,
                'last_date' => $lastTest?->test_date,
            ];
        }
        
        // Points summary
        $totalPoints = $user->total_points;
        $recentPoints = PointTransaction::where('user_id', $user->id)
            ->where('type', 'earned')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($point) {
                // Translate the description based on source - FIXED for streak bonus
                $point->translated_description = $this->translatePointSource($point->source, $point->description);
                return $point;
            });
        
        // Statistics
        $stats = [
            'total_sessions' => TherapySession::where('patient_id', $user->id)->count(),
            'completed_sessions' => TherapySession::where('patient_id', $user->id)->where('status', 'completed')->count(),
            'total_mood_entries' => MoodLog::where('user_id', $user->id)->count(),
            'total_tests' => TestResult::where('user_id', $user->id)->count(),
            'total_points_earned' => PointTransaction::where('user_id', $user->id)->where('type', 'earned')->sum('points'),
            'streak_days' => $this->getCurrentStreak($user->id),
        ];
        
        // Recent notifications
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Referral info
        $referralCode = $user->referral_code;
        $referralCount = $user->referrals()->count();
        
        return view('patient.dashboard', compact(
            'user', 'upcomingSessions', 'recentSessions',
            'moodLabels', 'moodValues', 'testsData',
            'totalPoints', 'recentPoints', 'stats', 'notifications',
            'referralCode', 'referralCount'
        ));
    }
    
    /**
     * Translate point source to Arabic with proper streak bonus handling
     */
    private function translatePointSource($source, $description = null)
    {
        // If there's a description already (like streak bonus with dynamic message), use it as is
        if ($description && strpos($description, 'Streak milestone') !== false) {
            // Description already has the translated version from the database
            return $description;
        }
        
        // Handle streak bonus separately
        if ($source === 'streak_bonus') {
            return __('Streak milestone!');
        }
        
        $translations = [
            'mood_tracking' => __('Daily mood check-in'),
            'session_attendance' => __('Attend a session'),
            'test_completed' => __('Complete assessment'),
            'task_completed' => __('Complete treatment task'),
            'specialist_rating' => __('Rate specialist'),
            'referral' => __('Bonus for referring a new user'),
            'Booking' => __('Booking'),
        ];
        
        if (isset($translations[$source])) {
            return $translations[$source];
        }
        
        return ucfirst(str_replace('_', ' ', $source));
    }
    
    private function getCurrentStreak($userId)
    {
        $moods = MoodLog::where('user_id', $userId)
            ->orderBy('log_date', 'desc')
            ->pluck('log_date');
        
        $streak = 0;
        $currentDate = Carbon::today();
        
        foreach ($moods as $moodDate) {
            if ($moodDate->eq($currentDate) || $moodDate->eq($currentDate->copy()->subDays($streak))) {
                $streak++;
            } else {
                break;
            }
        }
        
        return $streak;
    }
    
    /**
     * Store donation (become a donor)
     */
    public function storeDonation()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('donor')) {
            $user->assignRole('donor');
            
            if (!$user->donorProfile) {
                \App\Models\DonorProfile::create([
                    'user_id' => $user->id,
                    'total_donated' => 0,
                ]);
            }
        }
        
        return redirect()->back()->with('success', __('Thank you for becoming a donor!'));
    }
}