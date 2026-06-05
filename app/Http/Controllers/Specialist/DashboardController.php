<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\SpecialistProfile;
use App\Models\PointTransaction;
use App\Models\Notification;
use App\Models\Review;
use App\Models\TreatmentPlan;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $specialistProfile = $user->specialistProfile;
        
        // Statistics
        $stats = [
            'total_sessions' => TherapySession::where('specialist_id', $user->id)->count(),
            'completed_sessions' => TherapySession::where('specialist_id', $user->id)->where('status', 'completed')->count(),
            'upcoming_sessions' => TherapySession::where('specialist_id', $user->id)
                ->where('session_datetime', '>', now())
                ->where('status', 'scheduled')
                ->count(),
            'total_clients' => TherapySession::where('specialist_id', $user->id)
                ->distinct('patient_id')
                ->count('patient_id'),
            'total_points_earned' => PointTransaction::where('user_id', $user->id)->where('type', 'earned')->sum('points'),
            'total_earnings' => $this->getTotalEarnings($user->id),
            'average_rating' => Review::where('specialist_id', $user->id)->avg('rating') ?? 0,
            'total_reviews' => Review::where('specialist_id', $user->id)->count(),
        ];
        
        // Upcoming sessions
        $upcomingSessions = TherapySession::where('specialist_id', $user->id)
            ->where('session_datetime', '>', now())
            ->where('status', 'scheduled')
            ->with('patient')
            ->orderBy('session_datetime', 'asc')
            ->take(5)
            ->get();
        
        // Today's sessions
        $todaySessions = TherapySession::where('specialist_id', $user->id)
            ->whereDate('session_datetime', Carbon::today())
            ->with('patient')
            ->orderBy('session_datetime', 'asc')
            ->get();
        
        // Recent completed sessions
        $recentSessions = TherapySession::where('specialist_id', $user->id)
            ->where('status', 'completed')
            ->with(['patient', 'review'])
            ->orderBy('session_datetime', 'desc')
            ->take(5)
            ->get();
        
        // Recent clients (patients with most recent sessions)
        $recentClients = User::whereHas('therapySessionsAsPatient', function($query) use ($user) {
            $query->where('specialist_id', $user->id);
        })->with(['therapySessionsAsPatient' => function($query) use ($user) {
            $query->where('specialist_id', $user->id)->latest()->limit(1);
        }])->take(5)->get();
        
        // Weekly schedule for calendar view
        $weeklySchedule = $this->getWeeklySchedule($user->id);
        
        // Active treatment plans
        $activeTreatmentPlans = TreatmentPlan::where('specialist_id', $user->id)
            ->where('status', 'active')
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Recent notifications
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Monthly sessions chart data (last 6 months)
        $monthlySessions = $this->getMonthlySessions($user->id);
        
        // Monthly earnings chart data (last 6 months)
        $monthlyEarnings = $this->getMonthlyEarnings($user->id);
        
        // Popular session types
        $sessionTypes = [
            'video' => TherapySession::where('specialist_id', $user->id)->where('session_type', 'video')->count(),
            'audio' => TherapySession::where('specialist_id', $user->id)->where('session_type', 'audio')->count(),
            'text' => TherapySession::where('specialist_id', $user->id)->where('session_type', 'text')->count(),
        ];
        
        // Verification status
        $isVerified = $specialistProfile->is_verified ?? false;
        
        // Profile completion percentage
        $profileCompletion = $this->calculateProfileCompletion($specialistProfile);
        
        return view('specialist.dashboard', compact(
            'user', 'stats', 'upcomingSessions', 'todaySessions',
            'recentSessions', 'recentClients', 'weeklySchedule',
            'activeTreatmentPlans', 'notifications', 'monthlySessions', 'monthlyEarnings',
            'sessionTypes', 'isVerified', 'profileCompletion'
        ));
    }
    
    /**
     * Get total earnings using DB query with join
     */
    private function getTotalEarnings($specialistId)
    {
        $result = TherapySession::where('therapy_sessions.specialist_id', $specialistId)
            ->where('therapy_sessions.status', 'completed')
            ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
            ->select(DB::raw('SUM(specialist_profiles.consultation_fee) as total'))
            ->first();
        
        return $result->total ?? 0;
    }
    
    /**
     * Get weekly schedule for calendar
     */
    private function getWeeklySchedule($specialistId)
    {
        $schedule = [];
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        // Get availability for each day
        $availabilities = Availability::where('specialist_id', $specialistId)
            ->where('is_available', true)
            ->get();
        
        foreach ($days as $index => $day) {
            $dayAvailabilities = $availabilities->filter(function($avail) use ($index) {
                return $avail->day_of_week == $index;
            });
            
            $schedule[] = [
                'day' => $day,
                'day_index' => $index,
                'has_availability' => $dayAvailabilities->count() > 0,
                'slots' => $dayAvailabilities->map(function($avail) {
                    return [
                        'start' => Carbon::parse($avail->start_time)->format('h:i A'),
                        'end' => Carbon::parse($avail->end_time)->format('h:i A'),
                    ];
                }),
            ];
        }
        
        return $schedule;
    }
    
    /**
     * Get monthly sessions data for chart
     */
    private function getMonthlySessions($specialistId)
    {
        $months = [];
        $sessionsCount = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $months[] = $month->format('M Y');
            
            $sessionsCount[] = TherapySession::where('specialist_id', $specialistId)
                ->whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->count();
        }
        
        return [
            'months' => $months,
            'counts' => $sessionsCount,
        ];
    }
    
    /**
     * Get monthly earnings data for chart
     */
    private function getMonthlyEarnings($specialistId)
    {
        $months = [];
        $earnings = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $months[] = $month->format('M Y');
            
            $monthlyEarnings = TherapySession::where('therapy_sessions.specialist_id', $specialistId)
                ->where('therapy_sessions.status', 'completed')
                ->whereBetween('therapy_sessions.session_datetime', [$monthStart, $monthEnd])
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee');
            
            $earnings[] = $monthlyEarnings ?? 0;
        }
        
        return [
            'months' => $months,
            'earnings' => $earnings,
        ];
    }
    
    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($profile)
    {
        $fields = [
            'license_number',
            'specialization',
            'qualifications',
            'bio',
            'consultation_fee',
            'languages',
        ];
        
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($profile->$field)) {
                $filled++;
            }
        }
        
        // Also check if user has profile image
        if (!empty($profile->user->profile_image)) {
            $filled++;
            $fields[] = 'profile_image';
        }
        
        $totalFields = count($fields);
        if ($totalFields == 0) return 0;
        
        return round(($filled / $totalFields) * 100);
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