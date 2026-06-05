<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\MoodLog;
use App\Models\TestResult;
use App\Models\TreatmentPlan;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * Display clients dashboard with stats
     */
    public function index()
    {
        $specialistId = Auth::id();

        $clientIds = TherapySession::where('specialist_id', $specialistId)
            ->distinct('patient_id')
            ->pluck('patient_id');

        $clientsCount = $clientIds->count();
        $activeClients = TherapySession::where('specialist_id', $specialistId)
            ->where('session_datetime', '>', now()->subDays(30))
            ->distinct('patient_id')
            ->count('patient_id');

        $completedSessions = TherapySession::where('specialist_id', $specialistId)
            ->where('status', 'completed')
            ->count();

        $ratedClients = Review::where('specialist_id', $specialistId)
            ->distinct('reviewer_id')
            ->count('reviewer_id');

        $upcomingSessions = TherapySession::where('specialist_id', $specialistId)
            ->where('session_datetime', '>', now())
            ->where('status', 'scheduled')
            ->with('patient')
            ->orderBy('session_datetime', 'asc')
            ->take(5)
            ->get();

        $recentClients = User::whereIn('id', $clientIds)
            ->with([
                'therapySessionsAsPatient' => function ($q) use ($specialistId) {
                    $q->where('specialist_id', $specialistId)->latest()->limit(1);
                }
            ])
            ->take(5)
            ->get();

        $stats = [
            'total_clients' => $clientsCount,
            'active_clients' => $activeClients,
            'completed_sessions' => $completedSessions,
            'rated_clients' => $ratedClients,
        ];

        return view('specialist.clients.index', compact('stats', 'upcomingSessions', 'recentClients'));
    }

    /**
     * Get clients data for DataTable (AJAX)
     */
    public function getClientsData(Request $request)
    {
        $specialistId = Auth::id();

        $clientIds = TherapySession::where('specialist_id', $specialistId)
            ->distinct('patient_id')
            ->pluck('patient_id');

        $query = User::whereIn('id', $clientIds)
            ->with([
                'therapySessionsAsPatient' => function ($q) use ($specialistId) {
                    $q->where('specialist_id', $specialistId);
                },
                'reviewsGiven' => function ($q) use ($specialistId) {
                    $q->where('specialist_id', $specialistId);
                }
            ]);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->get('per_page', 15);
        $clients = $query->paginate($perPage);

        $clients->getCollection()->transform(function ($client) use ($specialistId) {
            $sessions = $client->therapySessionsAsPatient->where('specialist_id', $specialistId);

            $client->total_sessions = $sessions->count();
            $client->completed_sessions = $sessions->where('status', 'completed')->count();
            $client->last_session = $sessions->sortByDesc('session_datetime')->first()?->session_datetime;
            $client->rating = $client->reviewsGiven->where('specialist_id', $specialistId)->first()?->rating;
            $client->profile_image_url = $client->getProfileImageUrl();

            return $client;
        });

        return response()->json([
            'success' => true,
            'data' => $clients->items(),
            'total' => $clients->total(),
            'per_page' => $clients->perPage(),
            'current_page' => $clients->currentPage(),
            'last_page' => $clients->lastPage(),
        ]);
    }

    /**
     * Show client full profile
     */
    public function show($clientId)
    {
        $specialistId = Auth::id();

        $hasSession = TherapySession::where('specialist_id', $specialistId)
            ->where('patient_id', $clientId)
            ->exists();

        if (!$hasSession) {
            abort(403, 'Unauthorized access to this client profile.');
        }

        $client = User::findOrFail($clientId);

        $sessions = TherapySession::where('specialist_id', $specialistId)
            ->where('patient_id', $clientId)
            ->get();

        $stats = [
            'total_sessions' => $sessions->count(),
            'completed_sessions' => $sessions->where('status', 'completed')->count(),
            'cancelled_sessions' => $sessions->where('status', 'cancelled')->count(),
            'total_points' => $client->total_points,
            'average_mood' => round(MoodLog::where('user_id', $clientId)->avg('mood_value') ?? 0, 1),
            'total_tests' => TestResult::where('user_id', $clientId)->count(),
            'active_treatment_plans' => TreatmentPlan::where('specialist_id', $specialistId)
                ->where('patient_id', $clientId)
                ->where('status', 'active')
                ->count(),
        ];

        $rating = Review::where('specialist_id', $specialistId)
            ->where('reviewer_id', $clientId)
            ->first();

        return view('specialist.clients.show', compact('client', 'stats', 'rating'));
    }

    /**
     * Get client sessions (AJAX)
     */
    public function getSessions($clientId)
    {
        $specialistId = Auth::id();

        $sessions = TherapySession::where('specialist_id', $specialistId)
            ->where('patient_id', $clientId)
            ->with(['review'])
            ->orderBy('session_datetime', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'datetime' => $session->session_datetime->format('Y-m-d H:i:s'),
                    'date_formatted' => $session->session_datetime->translatedFormat('M d, Y'),
                    'time_formatted' => $session->session_datetime->format('h:i A'),
                    'duration' => $session->duration_minutes,
                    'type' => $session->session_type,
                    'type_icon' => $session->session_type === 'video' ? 'fa-video' : ($session->session_type === 'audio' ? 'fa-phone-alt' : 'fa-comment-dots'),
                    'status' => $session->status,
                    'status_badge' => $this->getStatusBadge($session->status),
                    'has_notes' => !empty($session->notes),
                    'rating' => $session->review?->rating,
                    'can_join' => $session->status === 'scheduled' &&
                        $session->session_datetime->diffInMinutes(now()) <= 15 &&
                        $session->session_datetime->diffInMinutes(now()) >= -60,
                ];
            });

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Get client mood data for chart (AJAX)
     */
    public function getMoodData($clientId)
    {
        $moodLogs = MoodLog::where('user_id', $clientId)
            ->where('log_date', '>=', now()->subDays(30))
            ->orderBy('log_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'labels' => $moodLogs->pluck('log_date')->map(fn($d) => Carbon::parse($d)->translatedFormat('M d')),
            'values' => $moodLogs->pluck('mood_value'),
            'average' => round($moodLogs->avg('mood_value') ?? 0, 1),
        ]);
    }

    /**
     * Get client test results (AJAX)
     */
    public function getTests($clientId)
    {
        $tests = TestResult::where('user_id', $clientId)
            ->orderBy('test_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($test) {
                return [
                    'id' => $test->id,
                    'type' => strtoupper($test->test_type),
                    'type_name' => $this->getTestName($test->test_type),
                    'score' => $test->score,
                    'level' => $test->result_level,
                    'level_ar' => $test->getResultLevelArAttribute(),
                    'date' => $test->test_date->translatedFormat('M d, Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'tests' => $tests,
        ]);
    }

    /**
     * Get client treatment plans (AJAX)
     */
    public function getTreatmentPlans($clientId)
    {
        $specialistId = Auth::id();

        $plans = TreatmentPlan::where('specialist_id', $specialistId)
            ->where('patient_id', $clientId)
            ->with('tasks')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($plan) {
                $completedTasks = $plan->tasks->where('is_completed', true)->count();
                $totalTasks = $plan->tasks->count();

                return [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'description' => $plan->description,
                    'status' => $plan->status,
                    'status_badge' => $this->getPlanStatusBadge($plan->status),
                    'progress' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
                    'tasks' => [
                        'completed' => $completedTasks,
                        'total' => $totalTasks,
                    ],
                    'start_date' => $plan->start_date->translatedFormat('M d, Y'),
                    'end_date' => $plan->end_date?->translatedFormat('M d, Y'),
                    'created_at' => $plan->created_at->translatedFormat('M d, Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'plans' => $plans,
        ]);
    }

    /**
     * Get recent activity (AJAX)
     */
    public function getRecentActivity($clientId)
    {
        $activity = [];

        // Mood logs
        $moods = MoodLog::where('user_id', $clientId)->latest()->take(3)->get();
        foreach ($moods as $mood) {
            $activity[] = [
                'type' => 'mood',
                'title' => __('Logged mood'),
                'value' => $mood->mood_value . '/10',
                'date' => $mood->created_at
            ];
        }

        // Sessions
        $sessions = TherapySession::where('patient_id', $clientId)->latest()->take(3)->get();
        foreach ($sessions as $session) {
            $statusText = $session->status === 'completed' ? __('Completed') : ($session->status === 'scheduled' ? __('Scheduled') : __('Cancelled'));
            $activity[] = [
                'type' => 'session',
                'title' => __('Session'),
                'value' => $statusText,
                'date' => $session->session_datetime
            ];
        }

        // Tests
        $tests = TestResult::where('user_id', $clientId)->latest()->take(3)->get();
        foreach ($tests as $test) {
            $activity[] = [
                'type' => 'test',
                'title' => __('Completed test'),
                'value' => strtoupper($test->test_type),
                'date' => $test->created_at
            ];
        }

        $activity = collect($activity)->sortByDesc('date')->values()->take(10);

        return response()->json(['success' => true, 'activity' => $activity]);
    }

    // ==================== HELPER METHODS ====================

    private function getStatusBadge($status)
    {
        $badges = [
            'scheduled' => '<span class="status-badge scheduled"><i class="fas fa-clock"></i> ' . __('Scheduled') . '</span>',
            'ongoing' => '<span class="status-badge ongoing"><i class="fas fa-spinner fa-pulse"></i> ' . __('Ongoing') . '</span>',
            'completed' => '<span class="status-badge completed"><i class="fas fa-check-circle"></i> ' . __('Completed') . '</span>',
            'cancelled' => '<span class="status-badge cancelled"><i class="fas fa-times-circle"></i> ' . __('Cancelled') . '</span>',
            'no_show' => '<span class="status-badge no-show"><i class="fas fa-user-slash"></i> ' . __('No Show') . '</span>',
        ];
        return $badges[$status] ?? '<span class="status-badge">' . ucfirst($status) . '</span>';
    }

    private function getPlanStatusBadge($status)
    {
        $badges = [
            'active' => '<span class="status-badge active"><i class="fas fa-play-circle"></i> ' . __('Active') . '</span>',
            'completed' => '<span class="status-badge completed"><i class="fas fa-check-circle"></i> ' . __('Completed') . '</span>',
            'cancelled' => '<span class="status-badge cancelled"><i class="fas fa-times-circle"></i> ' . __('Cancelled') . '</span>',
        ];
        return $badges[$status] ?? '<span class="status-badge">' . ucfirst($status) . '</span>';
    }

    private function getTestName($type)
    {
        $names = [
            'phq9' => 'PHQ-9 (Depression)',
            'gad7' => 'GAD-7 (Anxiety)',
            'pcl5' => 'PCL-5 (PTSD)',
            'isi' => 'ISI (Insomnia)',
            'pss' => 'PSS (Stress)',
            'cis' => 'CIS (Functioning)',
        ];
        return $names[$type] ?? strtoupper($type);
    }
}