<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\TestResult;
use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\CreditTransaction;
use App\Models\SpecialistPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Main Analytics Dashboard
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonths(12)->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        return view('admin.analytics.index', compact('startDate', 'endDate'));
    }

    /**
     * Get Overview Stats for Dashboard Cards (AJAX)
     */
    public function getOverviewStats(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subMonths(12)->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            $previousPeriodStart = $startDate->copy()->subMonths(12);
            $previousPeriodEnd = $endDate->copy()->subMonths(12);

            // Current period counts
            $totalUsers = User::role('patient')->count();
            $newUsers = User::role('patient')->whereBetween('created_at', [$startDate, $endDate])->count();
            $newUsersPrevious = User::role('patient')->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

            $totalSpecialists = User::role('specialist')->whereHas('specialistProfile', function ($q) {
                $q->where('is_verified', true);
            })->count();
            $newSpecialists = User::role('specialist')->whereBetween('created_at', [$startDate, $endDate])->count();

            $totalSessions = TherapySession::count();
            $completedSessions = TherapySession::where('status', 'completed')
                ->whereBetween('session_datetime', [$startDate, $endDate])->count();
            $completedSessionsPrevious = TherapySession::where('status', 'completed')
                ->whereBetween('session_datetime', [$previousPeriodStart, $previousPeriodEnd])->count();

            // Revenue calculation
            $revenue = TherapySession::where('status', 'completed')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee') ?? 0;

            $revenuePrevious = TherapySession::where('status', 'completed')
                ->whereBetween('session_datetime', [$previousPeriodStart, $previousPeriodEnd])
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee') ?? 0;

            // Points stats
            $totalPointsEarned = PointTransaction::where('type', 'earned')
                ->whereBetween('created_at', [$startDate, $endDate])->sum('points');
            $totalPointsRedeemed = PointTransaction::where('type', 'redeemed')
                ->whereBetween('created_at', [$startDate, $endDate])->sum('points');

            // Active users (last 30 days)
            $activeUsers = User::role('patient')
                ->whereHas('therapySessionsAsPatient', function ($q) {
                    $q->where('session_datetime', '>=', Carbon::now()->subDays(30));
                })
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $totalUsers,
                    'new_users' => $newUsers,
                    'new_users_percent' => $this->calculatePercentChange($newUsers, $newUsersPrevious),
                    'total_specialists' => $totalSpecialists,
                    'new_specialists' => $newSpecialists,
                    'completed_sessions' => $completedSessions,
                    'completed_sessions_percent' => $this->calculatePercentChange($completedSessions, $completedSessionsPrevious),
                    'total_sessions' => $totalSessions,
                    'revenue' => $revenue,
                    'revenue_percent' => $this->calculatePercentChange($revenue, $revenuePrevious),
                    'points_earned' => $totalPointsEarned,
                    'points_redeemed' => abs($totalPointsRedeemed),
                    'active_users' => $activeUsers,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Analytics Overview Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get User Analytics Data
     */
    public function getUserAnalytics(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subMonths(12)->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

            $growth = $this->getUserGrowthData();
            $retention = $this->getUserRetentionData();
            $demographics = $this->getDemographicsData();

            return response()->json([
                'success' => true,
                'growth' => $growth,
                'retention' => $retention,
                'demographics' => $demographics,
            ]);
        } catch (\Exception $e) {
            \Log::error('User Analytics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getUserGrowthData()
    {
        $months = [];
        $newUsers = [];
        $activeUsers = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $newUsers[] = User::role('patient')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $activeUsers[] = User::role('patient')
                ->whereHas('therapySessionsAsPatient', function ($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('session_datetime', [$monthStart, $monthEnd]);
                })
                ->count();
        }

        return [
            'months' => $months,
            'new_users' => $newUsers,
            'active_users' => $activeUsers,
        ];
    }

    private function getUserRetentionData()
    {
        $cohortMonths = [];
        $retentionRates = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $cohortMonths[] = $month->translatedFormat('M Y');

            $newUsers = User::role('patient')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->pluck('id');

            $returningUsers = User::whereIn('id', $newUsers)
                ->whereHas('therapySessionsAsPatient', function ($q) use ($monthEnd) {
                    $q->whereBetween('session_datetime', [
                        $monthEnd->copy()->addDay(),
                        $monthEnd->copy()->addDays(30)
                    ]);
                })
                ->count();

            $totalNew = count($newUsers);
            $retentionRates[] = $totalNew > 0 ? round(($returningUsers / $totalNew) * 100, 1) : 0;
        }

        return [
            'months' => $cohortMonths,
            'retention_rates' => $retentionRates,
        ];
    }

    private function getDemographicsData()
    {
        $genderCounts = User::role('patient')
            ->select('gender', DB::raw('count(*) as total'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get();

        $ageGroups = [
            '18-24' => 0,
            '25-34' => 0,
            '35-44' => 0,
            '45-54' => 0,
            '55+' => 0,
        ];

        $users = User::role('patient')->whereNotNull('date_of_birth')->get();
        foreach ($users as $user) {
            $age = $user->date_of_birth->age;
            if ($age >= 18 && $age <= 24)
                $ageGroups['18-24']++;
            elseif ($age >= 25 && $age <= 34)
                $ageGroups['25-34']++;
            elseif ($age >= 35 && $age <= 44)
                $ageGroups['35-44']++;
            elseif ($age >= 45 && $age <= 54)
                $ageGroups['45-54']++;
            elseif ($age >= 55)
                $ageGroups['55+']++;
        }

        return [
            'gender' => $genderCounts,
            'age_groups' => $ageGroups,
        ];
    }

    /**
     * Get Session Analytics Data
     */
    public function getSessionAnalytics(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'trend' => $this->getSessionTrendData(),
                'types' => $this->getSessionTypeData(),
                'status_distribution' => $this->getSessionStatusData(),
                'average_rating' => $this->getSessionRatingData(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Session Analytics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getSessionTrendData()
    {
        $months = [];
        $sessionsCount = [];
        $cancelledCount = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $sessionsCount[] = TherapySession::whereBetween('session_datetime', [$monthStart, $monthEnd])->count();
            $cancelledCount[] = TherapySession::where('status', 'cancelled')
                ->whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->count();
        }

        return [
            'months' => $months,
            'sessions' => $sessionsCount,
            'cancelled' => $cancelledCount,
        ];
    }

    private function getSessionTypeData()
    {
        $types = [
            'video' => TherapySession::where('session_type', 'video')->count(),
            'audio' => TherapySession::where('session_type', 'audio')->count(),
            'text' => TherapySession::where('session_type', 'text')->count(),
        ];

        $total = array_sum($types);

        return [
            'types' => $types,
            'percentages' => [
                'video' => $total > 0 ? round(($types['video'] / $total) * 100, 1) : 0,
                'audio' => $total > 0 ? round(($types['audio'] / $total) * 100, 1) : 0,
                'text' => $total > 0 ? round(($types['text'] / $total) * 100, 1) : 0,
            ],
        ];
    }

    private function getSessionStatusData()
    {
        return [
            'scheduled' => TherapySession::where('status', 'scheduled')->count(),
            'completed' => TherapySession::where('status', 'completed')->count(),
            'cancelled' => TherapySession::where('status', 'cancelled')->count(),
            'no_show' => TherapySession::where('status', 'no_show')->count(),
        ];
    }

    private function getSessionRatingData()
    {
        $months = [];
        $avgRatings = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $avgRating = TherapySession::whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->where('status', 'completed')
                ->join('reviews', 'therapy_sessions.id', '=', 'reviews.session_id')
                ->avg('reviews.rating') ?? 0;

            $avgRatings[] = round($avgRating, 1);
        }

        return [
            'months' => $months,
            'ratings' => $avgRatings,
        ];
    }

    /**
     * Get Financial Analytics Data
     */
    public function getFinancialAnalytics(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'revenue' => $this->getRevenueData(),
                'donations' => $this->getDonationData(),
                'credit_usage' => $this->getCreditUsageData(),
                'payouts' => $this->getPayoutsData(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Financial Analytics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getRevenueData()
    {
        $months = [];
        $revenueData = [];
        $platformFees = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $revenue = TherapySession::where('status', 'completed')
                ->whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee') ?? 0;

            $revenueData[] = $revenue;
            $platformFees[] = $revenue * 0.10;
        }

        return [
            'months' => $months,
            'revenue' => $revenueData,
            'platform_fees' => $platformFees,
        ];
    }

    private function getDonationData()
    {
        $months = [];
        $donationsTotal = [];
        $donorsCount = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $donationsTotal[] = CreditTransaction::where('type', 'donation')
                ->where('status', 'allocated')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount') ?? 0;

            $donorsCount[] = CreditTransaction::where('type', 'donation')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->distinct('donor_id')
                ->count('donor_id');
        }

        return [
            'months' => $months,
            'donations' => $donationsTotal,
            'donors' => $donorsCount,
        ];
    }

    private function getCreditUsageData()
    {
        $totalCreditSessions = TherapySession::where('is_paid_by_credit', true)->count();
        $totalSessions = TherapySession::count();

        return [
            'credit_sessions_count' => $totalCreditSessions,
            'credit_sessions_percent' => $totalSessions > 0 ? round(($totalCreditSessions / $totalSessions) * 100, 1) : 0,
            'total_credits_used' => CreditTransaction::where('status', 'used')->sum('amount') ?? 0,
        ];
    }

    private function getPayoutsData()
    {
        $months = [];
        $payoutsData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $payoutsData[] = SpecialistPayment::where('status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('final_amount') ?? 0;
        }

        return [
            'months' => $months,
            'payouts' => $payoutsData,
        ];
    }

    /**
     * Get Points & Rewards Analytics
     */
    public function getPointsAnalytics(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'distribution' => $this->getPointsDistributionData(),
                'earnings_by_source' => $this->getPointsBySourceData(),
                'popular_rewards' => $this->getPopularRewardsData(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Points Analytics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getPointsDistributionData()
    {
        $ranges = [
            '0-100' => 0,
            '101-500' => 0,
            '501-1000' => 0,
            '1001-5000' => 0,
            '5000+' => 0,
        ];

        $users = User::role('patient')->get();
        foreach ($users as $user) {
            $points = $user->total_points;
            if ($points <= 100)
                $ranges['0-100']++;
            elseif ($points <= 500)
                $ranges['101-500']++;
            elseif ($points <= 1000)
                $ranges['501-1000']++;
            elseif ($points <= 5000)
                $ranges['1001-5000']++;
            else
                $ranges['5000+']++;
        }

        return $ranges;
    }

    private function getPointsBySourceData()
    {
        $sources = [
            'mood_tracking' => 0,
            'session_attendance' => 0,
            'test_completed' => 0,
            'task_completed' => 0,
            'referral' => 0,
            'specialist_rating' => 0,
            'streak_bonus' => 0,
        ];

        $transactions = PointTransaction::where('type', 'earned')
            ->select('source', DB::raw('SUM(points) as total'))
            ->groupBy('source')
            ->get();

        foreach ($transactions as $transaction) {
            if (isset($sources[$transaction->source])) {
                $sources[$transaction->source] = $transaction->total;
            }
        }

        return $sources;
    }

    private function getPopularRewardsData()
    {
        $rewards = Reward::withCount([
            'redemptions as redemptions_count' => function ($q) {
                $q->where('status', 'completed');
            }
        ])->get();

        return $rewards->map(function ($reward) {
            return [
                'name' => $reward->getName(),
                'type' => $reward->type,
                'redemptions' => $reward->redemptions_count,
                'points_needed' => $reward->points_needed,
            ];
        });
    }

    /**
     * Get Test Analytics
     */
    public function getTestAnalytics(Request $request)
    {
        try {
            // Test distribution counts for all 6 tests
            $tests = ['phq9', 'gad7', 'pcl5', 'isi', 'pss', 'cis'];
            $distribution = [];

            foreach ($tests as $test) {
                $distribution[$test] = TestResult::where('test_type', $test)->count();
            }

            // Test trends for all 6 tests (last 6 months)
            $months = [];
            $phq9Trend = [];
            $gad7Trend = [];
            $pcl5Trend = [];
            $isiTrend = [];
            $pssTrend = [];
            $cisTrend = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();

                $months[] = $month->translatedFormat('M Y');

                $phq9Trend[] = TestResult::where('test_type', 'phq9')
                    ->whereBetween('test_date', [$monthStart, $monthEnd])
                    ->count();

                $gad7Trend[] = TestResult::where('test_type', 'gad7')
                    ->whereBetween('test_date', [$monthStart, $monthEnd])
                    ->count();

                $pcl5Trend[] = TestResult::where('test_type', 'pcl5')
                    ->whereBetween('test_date', [$monthStart, $monthEnd])
                    ->count();

                $isiTrend[] = TestResult::where('test_type', 'isi')
                    ->whereBetween('test_date', [$monthStart, $monthEnd])
                    ->count();

                $pssTrend[] = TestResult::where('test_type', 'pss')
                    ->whereBetween('test_date', [$monthStart, $monthEnd])
                    ->count();

                $cisTrend[] = TestResult::where('test_type', 'cis')
                    ->whereBetween('test_date', [$monthStart, $monthEnd])
                    ->count();
            }

            return response()->json([
                'success' => true,
                'distribution' => $distribution,
                'trends' => [
                    'months' => $months,
                    'phq9' => $phq9Trend,
                    'gad7' => $gad7Trend,
                    'pcl5' => $pcl5Trend,
                    'isi' => $isiTrend,
                    'pss' => $pssTrend,
                    'cis' => $cisTrend,
                    'all_tests' => [
                        'phq9' => $phq9Trend,
                        'gad7' => $gad7Trend,
                        'pcl5' => $pcl5Trend,
                        'isi' => $isiTrend,
                        'pss' => $pssTrend,
                        'cis' => $cisTrend,
                    ]
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Test Analytics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Specialists Analytics
     */
    public function getSpecialistAnalytics(Request $request)
    {
        try {
            // Top specialists
            $topSpecialists = User::role('specialist')
                ->whereHas('specialistProfile', function ($q) {
                    $q->where('is_verified', true);
                })
                ->withCount('therapySessionsAsSpecialist')
                ->orderBy('therapy_sessions_as_specialist_count', 'desc')
                ->take(10)
                ->get()
                ->map(function ($specialist) {
                    return [
                        'id' => $specialist->id,
                        'name' => $specialist->name,
                        'sessions_count' => $specialist->therapy_sessions_as_specialist_count,
                    ];
                });

            // Earnings distribution
            $ranges = [
                '0-500' => 0,
                '501-1000' => 0,
                '1001-5000' => 0,
                '5001-10000' => 0,
                '10000+' => 0,
            ];

            $specialists = User::role('specialist')->whereHas('specialistProfile')->get();
            foreach ($specialists as $specialist) {
                $earnings = TherapySession::where('specialist_id', $specialist->id)
                    ->where('status', 'completed')
                    ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                    ->sum('specialist_profiles.consultation_fee') ?? 0;

                if ($earnings <= 500)
                    $ranges['0-500']++;
                elseif ($earnings <= 1000)
                    $ranges['501-1000']++;
                elseif ($earnings <= 5000)
                    $ranges['1001-5000']++;
                elseif ($earnings <= 10000)
                    $ranges['5001-10000']++;
                else
                    $ranges['10000+']++;
            }

            return response()->json([
                'success' => true,
                'top_specialists' => $topSpecialists,
                'earnings_distribution' => $ranges,
            ]);
        } catch (\Exception $e) {
            \Log::error('Specialist Analytics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function calculatePercentChange($current, $previous)
    {
        if ($previous == 0)
            return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }
}