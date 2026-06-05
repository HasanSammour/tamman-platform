<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\SpecialistProfile;
use App\Models\PointTransaction;
use App\Models\TestResult;
use App\Models\CreditTransaction;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Reports Dashboard
     */
    public function index()
    {
        $stats = [
            'total_users' => User::role('patient')->count(),
            'total_specialists' => User::role('specialist')->whereHas('specialistProfile', function ($q) {
                $q->where('is_verified', true);
            })->count(),
            'total_sessions' => TherapySession::count(),
            'completed_sessions' => TherapySession::where('status', 'completed')->count(),
            'total_revenue' => $this->getTotalRevenue(),
            'total_points_earned' => PointTransaction::where('type', 'earned')->sum('points'),
            'total_points_redeemed' => PointTransaction::where('type', 'redeemed')->sum('points'),
            'total_donations' => CreditTransaction::where('type', 'donation')->where('status', 'allocated')->sum('amount') ?? 0,
        ];

        return view('admin.reports.index', compact('stats'));
    }

    // ==================== USERS REPORT ====================

    public function users()
    {
        $globalStats = [
            'total_users' => User::role('patient')->count(),
            'active_users' => User::role('patient')->where('is_active', true)->count(),
            'donors' => User::role('patient')->whereHas('donorProfile')->count(),
            'total_sessions' => TherapySession::where('status', 'completed')->count(),
        ];

        return view('admin.reports.users', compact('globalStats'));
    }

    public function getUsersReportData(Request $request)
    {
        $query = User::role('patient')
            ->with(['donorProfile'])
            ->select('users.*');

        // Apply filters
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->has('gender') && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }
        if ($request->has('donor') && $request->donor !== 'all') {
            if ($request->donor === 'yes') {
                $query->whereHas('donorProfile');
            } else {
                $query->whereDoesntHave('donorProfile');
            }
        }

        // SEARCH FILTER
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Whitelist allowed sort fields to prevent SQL injection
        $allowedSortFields = ['id', 'name', 'email', 'created_at', 'total_sessions', 'total_points', 'credit_balance', 'is_donor', 'is_active'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        // Add additional data and profile image URL
        $users->getCollection()->transform(function ($user) {
            $user->total_sessions = TherapySession::where('patient_id', $user->id)->count();
            $user->completed_sessions = TherapySession::where('patient_id', $user->id)->where('status', 'completed')->count();
            $user->is_donor = $user->donorProfile ? true : false;
            $user->total_points_earned = PointTransaction::where('user_id', $user->id)->where('type', 'earned')->sum('points');

            // Add profile image URL using the model's method
            $user->profile_image_url = $user->getProfileImageUrl();

            return $user;
        });

        $filteredQuery = clone $query;
        $stats = [
            'total_users' => $filteredQuery->count(),
            'active_users' => $filteredQuery->where('is_active', true)->count(),
            'donors' => $filteredQuery->whereHas('donorProfile')->count(),
            'total_sessions' => $filteredQuery->withCount('therapySessionsAsPatient')->get()->sum('therapy_sessions_as_patient_count'),
        ];

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'stats' => $stats,
        ]);
    }

    public function exportUsersReport(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        $query = User::role('patient')->with(['donorProfile']);

        // Apply same filters
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->gender && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        if ($request->has('donor') && $request->donor !== 'all') {
            if ($request->donor === 'yes') {
                $query->whereHas('donorProfile');
            } else {
                $query->whereDoesntHave('donorProfile');
            }
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        foreach ($users as $user) {
            $user->total_sessions = TherapySession::where('patient_id', $user->id)->count();
            $user->is_donor = $user->donorProfile ? true : false;
            $user->profile_image_url = $user->getProfileImageUrl();
        }

        $stats = [
            'total' => $users->count(),
            'active' => $users->where('is_active', true)->count(),
            'inactive' => $users->where('is_active', false)->count(),
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.reports.exports.users', compact('users', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        // Log the export
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'export_users_report',
            'details' => [
                'report_type' => 'users',
                'filters' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'status' => $request->status,
                    'gender' => $request->gender,
                    'donor' => $request->donor,
                    'search' => $request->search,
                ],
                'exported_at' => now()->toDateTimeString(),
            ]
        ]);

        return $pdf->download('users-report-' . date('Y-m-d') . '.pdf');
    }

    // ==================== SESSIONS REPORT ====================

    /**
     * Sessions Report Page
     */
    public function sessions()
    {
        $globalStats = [
            'total_sessions' => TherapySession::count(),
            'completed_sessions' => TherapySession::where('status', 'completed')->count(),
            'cancelled_sessions' => TherapySession::where('status', 'cancelled')->count(),
            'total_revenue' => $this->getTotalRevenue(),
        ];

        return view('admin.reports.sessions', compact('globalStats'));
    }

    /**
     * Get Sessions Report Data (AJAX)
     */
    public function getSessionsReportData(Request $request)
    {
        $query = TherapySession::with(['patient', 'specialist', 'specialist.specialistProfile'])
            ->select('therapy_sessions.*');

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('session_datetime', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('session_datetime', '<=', $request->date_to);
        }

        // Session type filter
        if ($request->has('session_type') && $request->session_type !== 'all') {
            $query->where('session_type', $request->session_type);
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search filter (patient name/email or specialist name/email)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('specialist', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Sort
        $sortField = $request->get('sort_field', 'session_datetime');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $sessions = $query->paginate($perPage);

        // Calculate amount for each session and add profile images
        $sessions->getCollection()->transform(function ($session) {
            $fee = $session->specialist->specialistProfile->consultation_fee ?? 0;
            if ($session->session_type === 'audio') {
                $fee = $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $fee = $fee * 0.8;
            }
            $session->amount = $fee;

            // Add patient profile image
            $session->patient_profile_image = $session->patient ? $session->patient->getProfileImageUrl() : null;
            $session->patient_name = $session->patient ? $session->patient->name : __('Deleted User');
            $session->patient_email = $session->patient ? $session->patient->email : '';

            // Add specialist profile image
            $session->specialist_profile_image = $session->specialist ? $session->specialist->getProfileImageUrl() : null;
            $session->specialist_name = $session->specialist ? $session->specialist->name : __('Deleted User');
            $session->specialist_email = $session->specialist ? $session->specialist->email : '';

            return $session;
        });

        // Calculate stats for the filtered data
        $filteredQuery = clone $query;
        $stats = [
            'total_sessions' => $filteredQuery->count(),
            'completed_sessions' => (clone $filteredQuery)->where('status', 'completed')->count(),
            'cancelled_sessions' => (clone $filteredQuery)->where('status', 'cancelled')->count(),
            'total_revenue' => $sessions->getCollection()->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $sessions->items(),
            'total' => $sessions->total(),
            'per_page' => $sessions->perPage(),
            'current_page' => $sessions->currentPage(),
            'last_page' => $sessions->lastPage(),
            'stats' => $stats,
        ]);
    }

    /**
     * Export Sessions Report to PDF
     */
    public function exportSessionsReport(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        $query = TherapySession::with(['patient', 'specialist', 'specialist.specialistProfile']);

        // Apply same filters
        if ($request->date_from) {
            $query->whereDate('session_datetime', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('session_datetime', '<=', $request->date_to);
        }
        if ($request->session_type && $request->session_type !== 'all') {
            $query->where('session_type', $request->session_type);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('specialist', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $sessions = $query->orderBy('session_datetime', 'desc')->get();

        foreach ($sessions as $session) {
            $fee = $session->specialist->specialistProfile->consultation_fee ?? 0;
            if ($session->session_type === 'audio') {
                $fee = $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $fee = $fee * 0.8;
            }
            $session->amount = $fee;
            $session->patient_name = $session->patient ? $session->patient->name : __('Deleted User');
            $session->specialist_name = $session->specialist ? $session->specialist->name : __('Deleted User');
        }

        $stats = [
            'total_sessions' => $sessions->count(),
            'completed_sessions' => $sessions->where('status', 'completed')->count(),
            'cancelled_sessions' => $sessions->where('status', 'cancelled')->count(),
            'total_revenue' => $sessions->where('status', 'completed')->sum('amount'),
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.reports.exports.sessions', compact('sessions', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        // Log the export
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'export_sessions_report',
            'details' => [
                'report_type' => 'sessions',
                'filters' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'session_type' => $request->session_type,
                    'status' => $request->status,
                    'search' => $request->search,
                ],
                'exported_at' => now()->toDateTimeString(),
            ]
        ]);

        return $pdf->download('sessions-report-' . date('Y-m-d') . '.pdf');
    }

    // ==================== FINANCIAL REPORT ====================
    /**
     * Financial Report Page
     */
    public function financial()
    {
        $globalStats = [
            'total_revenue' => $this->getTotalRevenue(),
            'total_donations' => CreditTransaction::where('type', 'donation')->where('status', 'allocated')->sum('amount') ?? 0,
            'total_credits_allocated' => CreditTransaction::where('type', 'credit_request')->where('status', 'allocated')->sum('amount') ?? 0,
            'platform_percent' => 10,
            'platform_fee' => $this->getTotalRevenue() * 0.10,
        ];

        return view('admin.reports.financial', compact('globalStats'));
    }

    /**
     * Get Financial Report Data (AJAX)
     */
    public function getFinancialReportData(Request $request)
    {
        // Validate platform_percent
        $platformPercent = (int) ($request->platform_percent ?? 10);

        if ($platformPercent < 0) {
            return response()->json([
                'success' => false,
                'message' => __('Platform fee cannot be less than 0%')
            ], 422);
        }

        if ($platformPercent > 10) {
            return response()->json([
                'success' => false,
                'message' => __('Platform fee cannot exceed 10%')
            ], 422);
        }

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;
        $sessionType = $request->session_type ?? 'all';
        $paymentMethod = $request->payment_method ?? 'all';
        $search = $request->search ?? '';
        $platformPercent = (int) ($request->platform_percent ?? 10);

        // Build query for completed sessions
        $query = TherapySession::where('status', 'completed')
            ->with(['patient', 'specialist', 'specialist.specialistProfile']);

        // Apply date filters
        if ($dateFrom) {
            $query->whereDate('session_datetime', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('session_datetime', '<=', $dateTo);
        }

        // Apply session type filter
        if ($sessionType !== 'all') {
            $query->where('session_type', $sessionType);
        }

        // Apply payment method filter
        if ($paymentMethod !== 'all') {
            if ($paymentMethod === 'credit') {
                $query->where('is_paid_by_credit', true);
            } elseif ($paymentMethod === 'cash') {
                $query->where('is_paid_by_credit', false);
            }
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('specialist', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Get all sessions for stats (no pagination for stats)
        $allSessions = clone $query;
        $allSessionsData = $allSessions->get();

        // Calculate breakdown
        $videoRevenue = 0;
        $audioRevenue = 0;
        $textRevenue = 0;
        $videoCount = 0;
        $audioCount = 0;
        $textCount = 0;
        $totalRevenue = 0;

        foreach ($allSessionsData as $session) {
            $fee = $session->specialist->specialistProfile->consultation_fee ?? 0;
            if ($session->session_type === 'audio') {
                $fee = $fee * 0.9;
                $audioRevenue += $fee;
                $audioCount++;
            } elseif ($session->session_type === 'text') {
                $fee = $fee * 0.8;
                $textRevenue += $fee;
                $textCount++;
            } else {
                $videoRevenue += $fee;
                $videoCount++;
            }
            $totalRevenue += $fee;
            $session->amount = $fee;
            $session->platform_fee = $fee * ($platformPercent / 100);
            $session->specialist_earning = $fee - $session->platform_fee;

            // Add profile images
            $session->patient_profile_image = $session->patient ? $session->patient->getProfileImageUrl() : null;
            $session->patient_name = $session->patient ? $session->patient->name : __('Deleted User');
            $session->patient_email = $session->patient ? $session->patient->email : '';
            $session->specialist_profile_image = $session->specialist ? $session->specialist->getProfileImageUrl() : null;
            $session->specialist_name = $session->specialist ? $session->specialist->name : __('Deleted User');
            $session->specialist_email = $session->specialist ? $session->specialist->email : '';
        }

        // Get donations and credits data
        $donationQuery = CreditTransaction::where('type', 'donation')->where('status', 'allocated');
        $creditQuery = CreditTransaction::where('type', 'credit_request')->where('status', 'allocated');

        if ($dateFrom) {
            $donationQuery->whereDate('created_at', '>=', $dateFrom);
            $creditQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $donationQuery->whereDate('created_at', '<=', $dateTo);
            $creditQuery->whereDate('created_at', '<=', $dateTo);
        }

        $totalDonations = $donationQuery->sum('amount');
        $totalCredits = $creditQuery->sum('amount');
        $platformFeeTotal = $totalRevenue * ($platformPercent / 100);
        $specialistEarningsTotal = $totalRevenue - $platformFeeTotal;

        // Handle pagination for table
        $sortField = $request->get('sort_field', 'session_datetime');
        $sortDirection = $request->get('sort_direction', 'desc');
        $perPage = (int) $request->get('per_page', 15);
        $currentPage = (int) $request->get('page', 1);

        $paginatedQuery = clone $query;
        $paginatedQuery->orderBy($sortField, $sortDirection);
        $total = $paginatedQuery->count();
        $sessions = $paginatedQuery->skip(($currentPage - 1) * $perPage)->take($perPage)->get();

        // Add calculated fields to paginated sessions
        foreach ($sessions as $session) {
            $fee = $session->specialist->specialistProfile->consultation_fee ?? 0;
            if ($session->session_type === 'audio') {
                $fee = $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $fee = $fee * 0.8;
            }
            $session->amount = $fee;
            $session->platform_fee = $fee * ($platformPercent / 100);
            $session->specialist_earning = $fee - $session->platform_fee;
            $session->patient_profile_image = $session->patient ? $session->patient->getProfileImageUrl() : null;
            $session->patient_name = $session->patient ? $session->patient->name : __('Deleted User');
            $session->patient_email = $session->patient ? $session->patient->email : '';
            $session->specialist_profile_image = $session->specialist ? $session->specialist->getProfileImageUrl() : null;
            $session->specialist_name = $session->specialist ? $session->specialist->name : __('Deleted User');
            $session->specialist_email = $session->specialist ? $session->specialist->email : '';
        }

        // Prepare response data
        $stats = [
            'total_revenue' => $totalRevenue,
            'total_donations' => $totalDonations,
            'total_credits_allocated' => $totalCredits,
            'platform_fee' => $platformFeeTotal,
            'specialist_earnings' => $specialistEarningsTotal,
        ];

        $breakdown = [
            'video_revenue' => $videoRevenue,
            'audio_revenue' => $audioRevenue,
            'text_revenue' => $textRevenue,
            'video_count' => $videoCount,
            'audio_count' => $audioCount,
            'text_count' => $textCount,
        ];

        // If chart is requested, return chart data
        if ($request->has('chart') && $request->chart == 'true') {
            $chartData = $this->getFinancialChartData($dateFrom, $dateTo, $sessionType, $paymentMethod, $search);
            return response()->json([
                'success' => true,
                'chart_data' => $chartData,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $sessions,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => ceil($total / $perPage),
            'stats' => $stats,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Get Financial Chart Data (Last 6 Months)
     */
    private function getFinancialChartData($dateFrom = null, $dateTo = null, $sessionType = 'all', $paymentMethod = 'all', $search = '')
    {
        $months = [];
        $revenue = [];
        $videoRevenue = 0;
        $audioRevenue = 0;
        $textRevenue = 0;

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            // Build query for monthly revenue
            $query = TherapySession::where('status', 'completed')
                ->whereBetween('session_datetime', [$monthStart, $monthEnd]);

            // Apply filters
            if ($dateFrom && $monthStart < $dateFrom) {
                // Adjust if needed
            }
            if ($dateTo && $monthEnd > $dateTo) {
                // Adjust if needed
            }
            if ($sessionType !== 'all') {
                $query->where('session_type', $sessionType);
            }
            if ($paymentMethod !== 'all') {
                if ($paymentMethod === 'credit') {
                    $query->where('is_paid_by_credit', true);
                } elseif ($paymentMethod === 'cash') {
                    $query->where('is_paid_by_credit', false);
                }
            }
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('patient', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('specialist', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }

            $monthlySessions = $query->get();
            $monthlyTotal = 0;

            foreach ($monthlySessions as $session) {
                $fee = $session->specialist->specialistProfile->consultation_fee ?? 0;
                if ($session->session_type === 'audio') {
                    $fee = $fee * 0.9;
                    $audioRevenue += $fee;
                } elseif ($session->session_type === 'text') {
                    $fee = $fee * 0.8;
                    $textRevenue += $fee;
                } else {
                    $videoRevenue += $fee;
                }
                $monthlyTotal += $fee;
            }
            $revenue[] = $monthlyTotal;
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
            'video_revenue' => $videoRevenue,
            'audio_revenue' => $audioRevenue,
            'text_revenue' => $textRevenue,
        ];
    }

    /**
     * Export Financial Report to PDF
     */
    public function exportFinancialReport(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        // Validate platform_percent
        $platformPercent = (int) ($request->platform_percent ?? 10);

        if ($platformPercent < 0) {
            return redirect()->back()->with('error', __('Platform fee cannot be less than 0%'));
        }

        if ($platformPercent > 10) {
            return redirect()->back()->with('error', __('Platform fee cannot exceed 10%'));
        }

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;
        $sessionType = $request->session_type ?? 'all';
        $paymentMethod = $request->payment_method ?? 'all';
        $search = $request->search ?? '';
        $platformPercent = (int) ($request->platform_percent ?? 10);

        // Build query
        $query = TherapySession::where('status', 'completed')
            ->with(['patient', 'specialist', 'specialist.specialistProfile']);

        if ($dateFrom) {
            $query->whereDate('session_datetime', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('session_datetime', '<=', $dateTo);
        }
        if ($sessionType !== 'all') {
            $query->where('session_type', $sessionType);
        }
        if ($paymentMethod !== 'all') {
            if ($paymentMethod === 'credit') {
                $query->where('is_paid_by_credit', true);
            } elseif ($paymentMethod === 'cash') {
                $query->where('is_paid_by_credit', false);
            }
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('specialist', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $sessions = $query->orderBy('session_datetime', 'asc')->get();

        $videoRevenue = 0;
        $audioRevenue = 0;
        $textRevenue = 0;
        $videoCount = 0;
        $audioCount = 0;
        $textCount = 0;

        foreach ($sessions as $session) {
            $fee = $session->specialist->specialistProfile->consultation_fee ?? 0;
            if ($session->session_type === 'audio') {
                $fee = $fee * 0.9;
                $audioRevenue += $fee;
                $audioCount++;
            } elseif ($session->session_type === 'text') {
                $fee = $fee * 0.8;
                $textRevenue += $fee;
                $textCount++;
            } else {
                $videoRevenue += $fee;
                $videoCount++;
            }
            $session->amount = $fee;
            $session->platform_fee = $fee * ($platformPercent / 100);
            $session->specialist_earning = $fee - $session->platform_fee;
            $session->patient_name = $session->patient ? $session->patient->name : __('Deleted User');
            $session->specialist_name = $session->specialist ? $session->specialist->name : __('Deleted User');
            $session->payment_method_text = $session->is_paid_by_credit ? __('Credit Balance') : __('Cash / Bank Transfer');
        }

        $totalRevenue = $videoRevenue + $audioRevenue + $textRevenue;
        $totalPlatformFee = $totalRevenue * ($platformPercent / 100);
        $totalSpecialistEarnings = $totalRevenue - $totalPlatformFee;

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_platform_fee' => $totalPlatformFee,
            'total_specialist_earnings' => $totalSpecialistEarnings,
            'video_revenue' => $videoRevenue,
            'audio_revenue' => $audioRevenue,
            'text_revenue' => $textRevenue,
            'video_count' => $videoCount,
            'audio_count' => $audioCount,
            'text_count' => $textCount,
            'platform_percent' => $platformPercent,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.reports.exports.financial', compact('sessions', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        // Log the export
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'export_financial_report',
            'details' => [
                'report_type' => 'financial',
                'filters' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'session_type' => $request->session_type,
                    'payment_method' => $request->payment_method,
                    'platform_percent' => $request->platform_percent,
                    'search' => $request->search,
                ],
                'exported_at' => now()->toDateTimeString(),
            ]
        ]);

        return $pdf->download('financial-report-' . date('Y-m-d') . '.pdf');
    }

    // ==================== SPECIALISTS REPORT ====================

    /**
     * Specialists Report Page
     */
    public function specialists()
    {
        $globalStats = [
            'total_specialists' => User::role('specialist')->count(),
            'verified_specialists' => User::role('specialist')->whereHas('specialistProfile', function ($q) {
                $q->where('is_verified', true);
            })->count(),
            'avg_rating' => SpecialistProfile::avg('rating_avg') ?? 0,
            'total_earnings' => $this->getTotalSpecialistEarnings(),
        ];

        return view('admin.reports.specialists', compact('globalStats'));
    }

    /**
     * Get Specialists Report Data (AJAX)
     */
    public function getSpecialistsReportData(Request $request)
    {
        $query = User::role('specialist')
            ->with(['specialistProfile', 'donorProfile'])
            ->select('users.*');

        // Date range filter (join date)
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Specialization filter
        if ($request->has('specialization') && $request->specialization !== 'all') {
            $query->whereHas('specialistProfile', function ($q) use ($request) {
                $q->where('specialization', $request->specialization);
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('specialistProfile', function ($sub) use ($search) {
                        $sub->where('specialization', 'like', "%{$search}%");
                    });
            });
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Handle sorting by specialist profile fields
        if ($sortField === 'specialization') {
            $query->join('specialist_profiles', 'users.id', '=', 'specialist_profiles.user_id')
                ->orderBy('specialist_profiles.specialization', $sortDirection)
                ->select('users.*');
        } elseif ($sortField === 'consultation_fee') {
            $query->join('specialist_profiles', 'users.id', '=', 'specialist_profiles.user_id')
                ->orderBy('specialist_profiles.consultation_fee', $sortDirection)
                ->select('users.*');
        } elseif ($sortField === 'rating_avg') {
            $query->join('specialist_profiles', 'users.id', '=', 'specialist_profiles.user_id')
                ->orderBy('specialist_profiles.rating_avg', $sortDirection)
                ->select('users.*');
        } elseif ($sortField === 'total_sessions') {
            $query->withCount('therapySessionsAsSpecialist')
                ->orderBy('therapy_sessions_as_specialist_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $specialists = $query->paginate($perPage);

        // Calculate additional data for each specialist
        $specialists->getCollection()->transform(function ($specialist) {
            $profile = $specialist->specialistProfile;
            $specialist->specialization = $profile->specialization ?? '-';
            $specialist->consultation_fee = $profile->consultation_fee ?? 0;
            $specialist->rating_avg = $profile->rating_avg ?? 0;
            $specialist->is_verified = $profile->is_verified ?? false;
            $specialist->total_sessions = $profile->total_sessions ?? 0;
            $specialist->total_earnings = $this->getSpecialistTotalEarnings($specialist->id);
            $specialist->profile_image_url = $specialist->getProfileImageUrl();

            return $specialist;
        });

        // Calculate stats for the filtered data
        $filteredSpecialists = clone $query;
        $filteredIds = $filteredSpecialists->pluck('id');

        $stats = [
            'total_specialists' => $specialists->total(),
            'verified_specialists' => User::role('specialist')->whereIn('id', $filteredIds)
                ->whereHas('specialistProfile', fn($q) => $q->where('is_verified', true))->count(),
            'avg_rating' => SpecialistProfile::whereIn('user_id', $filteredIds)->avg('rating_avg') ?? 0,
            'total_earnings' => $this->getFilteredSpecialistEarnings($filteredIds),
        ];

        return response()->json([
            'success' => true,
            'data' => $specialists->items(),
            'total' => $specialists->total(),
            'per_page' => $specialists->perPage(),
            'current_page' => $specialists->currentPage(),
            'last_page' => $specialists->lastPage(),
            'stats' => $stats,
        ]);
    }

    /**
     * Export Specialists Report to PDF
     */
    public function exportSpecialistsReport(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        $query = User::role('specialist')->with(['specialistProfile', 'donorProfile']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->specialization && $request->specialization !== 'all') {
            $query->whereHas('specialistProfile', function ($q) use ($request) {
                $q->where('specialization', $request->specialization);
            });
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $specialists = $query->orderBy('created_at', 'desc')->get();

        foreach ($specialists as $specialist) {
            $profile = $specialist->specialistProfile;
            $specialist->specialization = $profile->specialization ?? '-';
            $specialist->consultation_fee = $profile->consultation_fee ?? 0;
            $specialist->rating_avg = $profile->rating_avg ?? 0;
            $specialist->is_verified = $profile->is_verified ?? false;
            $specialist->total_sessions = $profile->total_sessions ?? 0;
            $specialist->total_earnings = $this->getSpecialistTotalEarnings($specialist->id);
        }

        $stats = [
            'total_specialists' => $specialists->count(),
            'verified_specialists' => $specialists->filter(fn($s) => $s->is_verified)->count(),
            'avg_rating' => $specialists->avg('rating_avg') ?? 0,
            'total_earnings' => $specialists->sum('total_earnings'),
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.reports.exports.specialists', compact('specialists', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        // Log the export
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'export_specialists_report',
            'details' => [
                'report_type' => 'specialists',
                'filters' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'specialization' => $request->specialization,
                    'status' => $request->status,
                    'search' => $request->search,
                ],
                'exported_at' => now()->toDateTimeString(),
            ]
        ]);

        return $pdf->download('specialists-report-' . date('Y-m-d') . '.pdf');
    }

    // ==================== POINTS REPORT ====================

    /**
     * Points Report Page
     */
    public function points()
    {
        $globalStats = [
            'total_earned' => PointTransaction::where('type', 'earned')->sum('points'),
            'total_redeemed' => abs(PointTransaction::where('type', 'redeemed')->sum('points')),
            'net_points' => PointTransaction::where('type', 'earned')->sum('points') - abs(PointTransaction::where('type', 'redeemed')->sum('points')),
            'active_users' => PointTransaction::distinct('user_id')->count('user_id'),
        ];

        return view('admin.reports.points', compact('globalStats'));
    }

    /**
     * Get Points Report Data (AJAX)
     */
    public function getPointsReportData(Request $request)
    {
        $query = PointTransaction::with(['user'])
            ->select('point_transactions.*');

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Type filter
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Source filter
        if ($request->has('source') && $request->source !== 'all') {
            $query->where('source', $request->source);
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $transactions = $query->paginate($perPage);

        // Add user data
        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->user_name = $transaction->user ? $transaction->user->name : __('Deleted User');
            $transaction->user_email = $transaction->user ? $transaction->user->email : '';
            $transaction->profile_image_url = $transaction->user ? $transaction->user->getProfileImageUrl() : null;
            return $transaction;
        });

        // Calculate stats for filtered data
        $filteredQuery = clone $query;
        $stats = [
            'total_earned' => (clone $filteredQuery)->where('type', 'earned')->sum('points'),
            'total_redeemed' => abs((clone $filteredQuery)->where('type', 'redeemed')->sum('points')),
            'net_points' => (clone $filteredQuery)->where('type', 'earned')->sum('points') - abs((clone $filteredQuery)->where('type', 'redeemed')->sum('points')),
            'active_users' => $filteredQuery->distinct('user_id')->count('user_id'),
        ];

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'total' => $transactions->total(),
            'per_page' => $transactions->perPage(),
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'stats' => $stats,
        ]);
    }

    /**
     * Export Points Report to PDF
     */
    public function exportPointsReport(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        $query = PointTransaction::with(['user']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        if ($request->source && $request->source !== 'all') {
            $query->where('source', $request->source);
        }
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        foreach ($transactions as $transaction) {
            $transaction->user_name = $transaction->user ? $transaction->user->name : __('Deleted User');
            $transaction->user_email = $transaction->user ? $transaction->user->email : '';
        }

        $stats = [
            'total_earned' => $transactions->where('type', 'earned')->sum('points'),
            'total_redeemed' => abs($transactions->where('type', 'redeemed')->sum('points')),
            'net_points' => $transactions->where('type', 'earned')->sum('points') - abs($transactions->where('type', 'redeemed')->sum('points')),
            'total_transactions' => $transactions->count(),
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.reports.exports.points', compact('transactions', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        // Log the export
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'export_points_report',
            'details' => [
                'report_type' => 'points',
                'filters' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'type' => $request->type,
                    'source' => $request->source,
                    'search' => $request->search,
                ],
                'exported_at' => now()->toDateTimeString(),
            ]
        ]);

        return $pdf->download('points-report-' . date('Y-m-d') . '.pdf');
    }

    // ==================== TESTS REPORT ====================

    /**
     * Tests Report Page
     */
    public function tests()
    {
        $globalStats = [
            'total_tests' => TestResult::count(),
            'unique_users' => TestResult::distinct('user_id')->count('user_id'),
            'avg_score' => TestResult::avg('score') ?? 0,
            'most_common_test' => $this->getMostCommonTest(),
        ];

        $testTypeCounts = $this->getTestTypeCounts();
        $testTypeAverages = $this->getTestTypeAverages();

        return view('admin.reports.tests', compact('globalStats', 'testTypeCounts', 'testTypeAverages'));
    }

    /**
     * Get Tests Report Data (AJAX)
     */
    public function getTestsReportData(Request $request)
    {
        $query = TestResult::with(['user'])
            ->select('test_results.*');

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('test_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('test_date', '<=', $request->date_to);
        }

        // Test type filter
        if ($request->has('test_type') && $request->test_type !== 'all') {
            $query->where('test_type', $request->test_type);
        }

        // Result level filter
        if ($request->has('result_level') && $request->result_level !== 'all') {
            $query->where('result_level', $request->result_level);
        }

        // Search filter (user name/email)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortField = $request->get('sort_field', 'test_date');
        $sortDirection = $request->get('sort_direction', 'desc');

        if (in_array($sortField, ['id', 'score', 'test_date'])) {
            $query->orderBy($sortField, $sortDirection);
        } elseif ($sortField === 'user_name') {
            $query->orderBy(User::select('name')->whereColumn('users.id', 'test_results.user_id'), $sortDirection);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tests = $query->paginate($perPage);

        // Add user data to each test
        $tests->getCollection()->transform(function ($test) {
            $test->user_name = $test->user ? $test->user->name : __('Deleted User');
            $test->user_email = $test->user ? $test->user->email : '';
            $test->user_profile_image = $test->user ? $test->user->getProfileImageUrl() : null;
            return $test;
        });

        // Calculate stats for the filtered data
        $filteredQuery = clone $query;
        $stats = [
            'total_tests' => $filteredQuery->count(),
            'unique_users' => $filteredQuery->distinct('user_id')->count('user_id'),
            'avg_score' => $filteredQuery->avg('score') ?? 0,
            'most_common_test' => $this->getMostCommonTest($filteredQuery),
        ];

        // Get test type counts and averages for filtered data
        $testTypeCounts = [];
        $testTypeAverages = [];
        $testTypes = ['phq9', 'gad7', 'pcl5', 'isi', 'pss', 'cis'];

        // Create a base query for the filters (without test_type filter)
        $baseQuery = clone $query;
        // Remove test_type filter if applied for counts
        $tempQuery = clone $baseQuery;
        $tempQuery->whereNotNull('test_type'); // Reset any test_type filter

        foreach ($testTypes as $type) {
            // For counts, don't apply test_type filter yet
            $countQuery = clone $tempQuery;
            $testTypeCounts[$type] = $countQuery->where('test_type', $type)->count();

            // For averages
            $avgQuery = clone $tempQuery;
            $testTypeAverages[$type] = round($avgQuery->where('test_type', $type)->avg('score') ?? 0, 1);
        }

        return response()->json([
            'success' => true,
            'data' => $tests->items(),
            'total' => $tests->total(),
            'per_page' => $tests->perPage(),
            'current_page' => $tests->currentPage(),
            'last_page' => $tests->lastPage(),
            'stats' => $stats,
            'test_type_counts' => $testTypeCounts,
            'test_type_averages' => $testTypeAverages,
        ]);
    }

    /**
     * Export Tests Report to PDF
     */
    public function exportTestsReport(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        $query = TestResult::with(['user']);

        // Apply same filters
        if ($request->date_from) {
            $query->whereDate('test_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('test_date', '<=', $request->date_to);
        }
        if ($request->test_type && $request->test_type !== 'all') {
            $query->where('test_type', $request->test_type);
        }
        if ($request->result_level && $request->result_level !== 'all') {
            $query->where('result_level', $request->result_level);
        }
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $tests = $query->orderBy('test_date', 'desc')->get();

        foreach ($tests as $test) {
            $test->user_name = $test->user ? $test->user->name : __('Deleted User');
            $test->user_email = $test->user ? $test->user->email : '';
        }

        // ========== CALCULATE TEST TYPE COUNTS AND AVERAGES (SAME AS getTestsReportData) ==========
        $testTypes = ['phq9', 'gad7', 'pcl5', 'isi', 'pss', 'cis'];

        // Create a base query without test_type filter for counts
        $baseQuery = clone $query;
        $tempQuery = clone $baseQuery;
        $tempQuery->whereNotNull('test_type');

        $testTypeCounts = [];
        $testTypeAverages = [];

        foreach ($testTypes as $type) {
            // Count
            $countQuery = clone $tempQuery;
            $testTypeCounts[$type] = $countQuery->where('test_type', $type)->count();

            // Average
            $avgQuery = clone $tempQuery;
            $testTypeAverages[$type] = round($avgQuery->where('test_type', $type)->avg('score') ?? 0, 1);
        }

        $stats = [
            'total_tests' => $tests->count(),
            'unique_users' => $tests->unique('user_id')->count(),
            'avg_score' => $tests->avg('score') ?? 0,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
            // Add test type counts and averages
            'phq9_count' => $testTypeCounts['phq9'],
            'phq9_avg' => $testTypeAverages['phq9'],
            'gad7_count' => $testTypeCounts['gad7'],
            'gad7_avg' => $testTypeAverages['gad7'],
            'pcl5_count' => $testTypeCounts['pcl5'],
            'pcl5_avg' => $testTypeAverages['pcl5'],
            'isi_count' => $testTypeCounts['isi'],
            'isi_avg' => $testTypeAverages['isi'],
            'pss_count' => $testTypeCounts['pss'],
            'pss_avg' => $testTypeAverages['pss'],
            'cis_count' => $testTypeCounts['cis'],
            'cis_avg' => $testTypeAverages['cis'],
        ];

        $html = view('admin.reports.exports.tests', compact('tests', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        // Log the export
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'export_tests_report',
            'details' => [
                'report_type' => 'tests',
                'filters' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'test_type' => $request->test_type,
                    'result_level' => $request->result_level,
                    'search' => $request->search,
                ],
                'exported_at' => now()->toDateTimeString(),
            ]
        ]);

        return $pdf->download('tests-report-' . date('Y-m-d') . '.pdf');
    }

    // ==================== HELPER METHODS ====================
    private function getTotalRevenue()
    {
        $sessions = TherapySession::where('status', 'completed')->get();
        $total = 0;
        foreach ($sessions as $session) {
            $fee = $session->specialist->specialistProfile->consultation_fee ?? 0;
            if ($session->session_type === 'audio') {
                $fee = $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $fee = $fee * 0.8;
            }
            $total += $fee;
        }
        return $total;
    }

    /**
     * Get total earnings for all specialists
     */
    private function getTotalSpecialistEarnings()
    {
        $specialists = User::role('specialist')->get();
        $total = 0;
        foreach ($specialists as $specialist) {
            $total += $this->getSpecialistTotalEarnings($specialist->id);
        }
        return $total;
    }

    /**
     * Get total earnings for a single specialist
     */
    private function getSpecialistTotalEarnings($specialistId)
    {
        $sessions = TherapySession::where('specialist_id', $specialistId)
            ->where('status', 'completed')
            ->get();

        $profile = SpecialistProfile::where('user_id', $specialistId)->first();
        $fee = $profile->consultation_fee ?? 0;
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
     * Get filtered specialists earnings summary
     */
    private function getFilteredSpecialistEarnings($specialistIds)
    {
        $total = 0;
        foreach ($specialistIds as $id) {
            $total += $this->getSpecialistTotalEarnings($id);
        }
        return $total;
    }

    // Helper methods for tests
    private function getMostCommonTest($query = null)
    {
        if ($query) {
            $counts = [
                'phq9' => (clone $query)->where('test_type', 'phq9')->count(),
                'gad7' => (clone $query)->where('test_type', 'gad7')->count(),
                'pcl5' => (clone $query)->where('test_type', 'pcl5')->count(),
                'isi' => (clone $query)->where('test_type', 'isi')->count(),
                'pss' => (clone $query)->where('test_type', 'pss')->count(),
                'cis' => (clone $query)->where('test_type', 'cis')->count(),
            ];
        } else {
            $counts = [
                'phq9' => TestResult::where('test_type', 'phq9')->count(),
                'gad7' => TestResult::where('test_type', 'gad7')->count(),
                'pcl5' => TestResult::where('test_type', 'pcl5')->count(),
                'isi' => TestResult::where('test_type', 'isi')->count(),
                'pss' => TestResult::where('test_type', 'pss')->count(),
                'cis' => TestResult::where('test_type', 'cis')->count(),
            ];
        }

        $max = max($counts);
        if ($max == 0)
            return '-';

        $mostCommon = array_search($max, $counts);

        $testNames = [
            'phq9' => 'PHQ-9',
            'gad7' => 'GAD-7',
            'pcl5' => 'PCL-5',
            'isi' => 'ISI',
            'pss' => 'PSS',
            'cis' => 'CIS',
        ];

        return $testNames[$mostCommon] ?? '-';
    }

    public function getTestTypeCounts()
    {
        return [
            'phq9' => TestResult::where('test_type', 'phq9')->count(),
            'gad7' => TestResult::where('test_type', 'gad7')->count(),
            'pcl5' => TestResult::where('test_type', 'pcl5')->count(),
            'isi' => TestResult::where('test_type', 'isi')->count(),
            'pss' => TestResult::where('test_type', 'pss')->count(),
            'cis' => TestResult::where('test_type', 'cis')->count(),
        ];
    }

    public function getTestTypeAverages()
    {
        return [
            'phq9' => TestResult::where('test_type', 'phq9')->avg('score') ?? 0,
            'gad7' => TestResult::where('test_type', 'gad7')->avg('score') ?? 0,
            'pcl5' => TestResult::where('test_type', 'pcl5')->avg('score') ?? 0,
            'isi' => TestResult::where('test_type', 'isi')->avg('score') ?? 0,
            'pss' => TestResult::where('test_type', 'pss')->avg('score') ?? 0,
            'cis' => TestResult::where('test_type', 'cis')->avg('score') ?? 0,
        ];
    }
}