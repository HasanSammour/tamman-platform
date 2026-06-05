<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\MoodLog;
use App\Models\TestResult;
use App\Models\PointTransaction;
use App\Models\SpecialistProfile;
use App\Models\CreditTransaction;
use App\Models\Content;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        
        // Platform Statistics
        $stats = [
            'total_users' => User::whereHas('roles', fn($q) => $q->where('name', 'patient'))->count(),
            'total_specialists' => User::whereHas('roles', fn($q) => $q->where('name', 'specialist'))->whereHas('specialistProfile', fn($q) => $q->where('is_verified', true)->where('application_status', 'approved'))->count(),
            'pending_specialists' => SpecialistProfile::where('application_status', 'pending')->count(),
            'total_sessions' => TherapySession::count(),
            'completed_sessions' => TherapySession::where('status', 'completed')->count(),
            'total_points_awarded' => PointTransaction::where('type', 'earned')->sum('points'),
        ];
        
        // Monthly statistics for charts
        $monthlyStats = $this->getMonthlyStats();
        
        // Recent Users - Limit to 6 for dashboard
        $recentUsers = User::whereHas('roles', fn($q) => $q->where('name', 'patient'))
            ->orderBy('created_at', 'desc')->take(6)->get();
        
        // Pending Specialists - Limit to 5 for dashboard
        $pendingSpecialists = SpecialistProfile::where('application_status', 'pending')
            ->with('user')->orderBy('created_at', 'desc')->take(5)->get();
        
        // Today's Sessions
        $todaySessions = TherapySession::whereDate('session_datetime', Carbon::today())
            ->with(['patient', 'specialist'])->orderBy('session_datetime', 'asc')->take(9)->get();
        
        // Upcoming Sessions (next 7 days) - Take 8 for dashboard
        $upcomingSessions = TherapySession::whereBetween('session_datetime', [Carbon::now(), Carbon::now()->addDays(7)])
            ->with(['patient', 'specialist'])->orderBy('session_datetime', 'asc')->take(8)->get();
        
        // 6 Tests Distribution
        $testTypes = ['phq9', 'gad7', 'pcl5', 'isi', 'pss', 'cis'];
        $testDistribution = [];
        foreach ($testTypes as $type) {
            $testDistribution[$type] = TestResult::where('test_type', $type)->count();
        }
        
        // Content Distribution (4 types)
        $contentTypes = ['article', 'video', 'tip', 'guide'];
        $contentDistribution = [];
        foreach ($contentTypes as $type) {
            $contentDistribution[$type] = [
                'total' => Content::where('type', $type)->count(),
                'published' => Content::where('type', $type)->where('is_published', true)->count(),
            ];
        }
        
        // Recent System Logs - Limit to 6 for dashboard
        $recentLogs = SystemLog::with('admin')->orderBy('created_at', 'desc')->take(6)->get();
        
        // Donation Statistics
        $donationStats = [
            'total_donated' => CreditTransaction::where('status', 'allocated')->sum('amount') ?? 0,
            'total_donors' => User::whereHas('roles', fn($q) => $q->where('name', 'donor'))->count(),
            'users_supported' => CreditTransaction::where('status', 'allocated')->distinct('recipient_id')->count('recipient_id'),
        ];
        
        // Activity Timeline (last 7 days)
        $activityTimeline = $this->getActivityTimeline();
        
        return view('admin.dashboard', compact(
            'admin', 'stats', 'monthlyStats', 'recentUsers', 'pendingSpecialists',
            'todaySessions', 'upcomingSessions', 'testDistribution',
            'contentDistribution', 'recentLogs', 'donationStats', 'activityTimeline'
        ));
    }
    
    private function getMonthlyStats()
    {
        $months = [];
        $usersData = [];
        $sessionsData = [];
        $pointsData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $months[] = $month->translatedFormat('M Y');
            
            $usersData[] = User::whereHas('roles', fn($q) => $q->where('name', 'patient'))
                ->whereBetween('created_at', [$monthStart, $monthEnd])->count();
            
            $sessionsData[] = TherapySession::whereBetween('session_datetime', [$monthStart, $monthEnd])->count();
            
            $pointsData[] = PointTransaction::where('type', 'earned')
                ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('points');
        }
        
        return [
            'months' => $months,
            'users' => $usersData,
            'sessions' => $sessionsData,
            'points' => $pointsData,
        ];
    }
    
    private function getActivityTimeline()
    {
        $timeline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStart = $date->copy()->startOfDay();
            $dateEnd = $date->copy()->endOfDay();
            
            $timeline[] = [
                'date' => $date->translatedFormat('D, M d'),
                'sessions' => TherapySession::whereBetween('session_datetime', [$dateStart, $dateEnd])->count(),
                'new_users' => User::whereHas('roles', fn($q) => $q->where('name', 'patient'))
                    ->whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'mood_entries' => MoodLog::whereBetween('log_date', [$dateStart, $dateEnd])->count(),
            ];
        }
        return $timeline;
    }
}
