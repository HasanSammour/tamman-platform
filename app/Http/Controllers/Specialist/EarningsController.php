<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\TherapySession;
use App\Models\SpecialistPayment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EarningsController extends Controller
{
    /**
     * Display earnings dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $profile = $user->specialistProfile;

        // Get completed sessions
        $completedSessions = TherapySession::where('specialist_id', $user->id)
            ->where('status', 'completed')
            ->get();

        // Calculate earnings by session type
        $fee = $profile->consultation_fee ?? 0;
        $videoEarnings = 0;
        $audioEarnings = 0;
        $textEarnings = 0;
        $videoCount = 0;
        $audioCount = 0;
        $textCount = 0;

        foreach ($completedSessions as $session) {
            if ($session->session_type === 'video') {
                $videoEarnings += $fee;
                $videoCount++;
            } elseif ($session->session_type === 'audio') {
                $audioEarnings += $fee * 0.9;
                $audioCount++;
            } elseif ($session->session_type === 'text') {
                $textEarnings += $fee * 0.8;
                $textCount++;
            }
        }

        $totalEarnings = $videoEarnings + $audioEarnings + $textEarnings;
        $totalSessions = $videoCount + $audioCount + $textCount;

        // Get unique clients count
        $totalClients = TherapySession::where('specialist_id', $user->id)
            ->where('status', 'completed')
            ->distinct('patient_id')
            ->count('patient_id');

        // Get average rating
        $averageRating = $user->reviewsReceived()->avg('rating') ?? 0;

        // Calculate current month earnings
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentMonthSessions = TherapySession::where('specialist_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('session_datetime', [$currentMonthStart, $currentMonthEnd])
            ->get();

        $currentMonthEarnings = 0;
        foreach ($currentMonthSessions as $session) {
            if ($session->session_type === 'video') {
                $currentMonthEarnings += $fee;
            } elseif ($session->session_type === 'audio') {
                $currentMonthEarnings += $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $currentMonthEarnings += $fee * 0.8;
            }
        }

        // Get payment history
        $paymentHistory = SpecialistPayment::where('specialist_id', $user->id)
            ->orderBy('month_year', 'desc')
            ->get();

        // Calculate pending payout using the model method (which handles months correctly)
        $pendingPayout = $user->getPendingPayoutAmount();

        // Get monthly earnings for chart (last 6 months)
        $monthlyEarningsData = $this->getMonthlyEarningsData($user->id);

        // Get monthly sessions for chart
        $monthlySessionsData = $this->getMonthlySessionsData($user->id);

        $stats = [
            'total_earnings' => $totalEarnings,
            'current_month_earnings' => $currentMonthEarnings,
            'total_sessions' => $totalSessions,
            'total_clients' => $totalClients,
            'average_rating' => round($averageRating, 1),
            'pending_payout' => $pendingPayout,
            'video_count' => $videoCount,
            'audio_count' => $audioCount,
            'text_count' => $textCount,
            'video_earnings' => $videoEarnings,
            'audio_earnings' => $audioEarnings,
            'text_earnings' => $textEarnings,
        ];

        return view('specialist.earnings.index', compact(
            'stats',
            'paymentHistory',
            'monthlyEarningsData',
            'monthlySessionsData'
        ));
    }

    /**
     * Get earnings data for AJAX chart
     */
    public function getEarningsData()
    {
        $user = Auth::user();
        $fee = $user->specialistProfile->consultation_fee ?? 0;

        $months = [];
        $earnings = [];
        $sessions = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $monthlySessions = TherapySession::where('specialist_id', $user->id)
                ->where('status', 'completed')
                ->whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->get();

            $monthlyEarnings = 0;
            foreach ($monthlySessions as $session) {
                if ($session->session_type === 'video') {
                    $monthlyEarnings += $fee;
                } elseif ($session->session_type === 'audio') {
                    $monthlyEarnings += $fee * 0.9;
                } elseif ($session->session_type === 'text') {
                    $monthlyEarnings += $fee * 0.8;
                }
            }

            $earnings[] = $monthlyEarnings;
            $sessions[] = $monthlySessions->count();
        }

        return response()->json([
            'success' => true,
            'months' => $months,
            'earnings' => $earnings,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Get payment history via AJAX
     */
    public function getPaymentHistory()
    {
        $user = Auth::user();

        $payments = SpecialistPayment::where('specialist_id', $user->id)
            ->orderBy('month_year', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'month_year' => $payment->month_year,
                    'amount' => $payment->amount,
                    'platform_fee' => $payment->platform_fee,
                    'final_amount' => $payment->final_amount,
                    'status' => $payment->status,
                    'status_text' => $this->getStatusText($payment->status),
                    'paid_at' => $payment->paid_at ? $payment->paid_at->translatedFormat('M d, Y') : '-',
                ];
            });

        return response()->json([
            'success' => true,
            'payments' => $payments,
        ]);
    }

    /**
     * Show invoice PDF
     */
    public function invoice($paymentId)
{
    $user = Auth::user();
    $payment = SpecialistPayment::where('id', $paymentId)
        ->where('specialist_id', $user->id)
        ->firstOrFail();

    // Only allow invoice download for paid payments
    if ($payment->status !== 'paid') {
        return response()->json([
            'success' => false,
            'message' => __('Invoice is only available for paid payments.')
        ], 403);
    }

    // Get sessions for this month
    $monthYear = $payment->month_year;
    [$month, $year] = explode('/', $monthYear);
    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = Carbon::create($year, $month, 1)->endOfMonth();

    $sessions = TherapySession::where('specialist_id', $user->id)
        ->where('status', 'completed')
        ->whereBetween('session_datetime', [$startDate, $endDate])
        ->with('patient')
        ->get();

    $fee = $user->specialistProfile->consultation_fee ?? 0;

    $sessionDetails = [];
    $totalEarnings = 0;

    foreach ($sessions as $session) {
        if ($session->session_type === 'video') {
            $earning = $fee;
        } elseif ($session->session_type === 'audio') {
            $earning = $fee * 0.9;
        } else {
            $earning = $fee * 0.8;
        }

        $totalEarnings += $earning;

        $sessionDetails[] = [
            'date' => $session->session_datetime->format('M d, Y'),
            'time' => $session->session_datetime->format('h:i A'),
            'patient_name' => $session->patient->name,
            'session_type' => $session->session_type,
            'earning' => $earning,
        ];
    }

    $data = [
        'specialist' => $user,
        'payment' => $payment,
        'sessions' => $sessionDetails,
        'total_earnings' => $totalEarnings,
        'month_name' => Carbon::create($year, $month, 1)->format('F Y'),
        'generated_at' => now(),
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('specialist.earnings.invoice', $data);
    $pdf->setPaper('A4', 'portrait');

    $safeFilename = str_replace('/', '-', $monthYear);
    
    // Add headers to prevent IDM from intercepting
    return response($pdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="invoice-' . $safeFilename . '.pdf"',
        'X-Content-Type-Options' => 'nosniff',
        'X-Download-Options' => 'noopen',
    ]);
}

    /**
     * Request payout (optional feature)
     */
    public function requestPayout(Request $request)
    {
        $user = Auth::user();
        $pendingPayout = $user->getPendingPayoutAmount();
        $currentMonth = SpecialistPayment::getMonthYear();

        // Check if already requested this month
        $existingRequest = SpecialistPayment::where('specialist_id', $user->id)
            ->where('month_year', $currentMonth)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a pending payout request for this month. Please wait for it to be processed.')
            ], 422);
        }

        // Check if already paid this month
        $existingPayment = SpecialistPayment::where('specialist_id', $user->id)
            ->where('month_year', $currentMonth)
            ->where('status', 'paid')
            ->exists();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => __('You have already received payment for this month. Next payout will be available next month.')
            ], 422);
        }

        if ($pendingPayout < 50) {
            return response()->json([
                'success' => false,
                'message' => __('Minimum payout amount is $50. You currently have $:amount pending.', [
                    'amount' => number_format($pendingPayout, 2)
                ]),
            ], 422);
        }

        // Create pending payment request
        $payment = SpecialistPayment::create([
            'specialist_id' => $user->id,
            'amount' => $pendingPayout,
            'month_year' => $currentMonth,
            'platform_fee' => 0,
            'final_amount' => $pendingPayout,
            'status' => 'pending',
            'notes' => __('Payout request submitted by specialist'),
        ]);

        // Notify admin
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => __('New Payout Request'),
                'message' => __('Specialist :name has requested a payout of $:amount.', [
                    'name' => $user->name,
                    'amount' => number_format($pendingPayout, 2)
                ]),
                'type' => 'payment',
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Payout request submitted successfully. Our team will process it within 5-7 business days.'),
            'request_id' => $payment->id,
        ]);
    }

    /**
     * Get session breakdown by type
     */
    public function getSessionBreakdown()
    {
        $user = Auth::user();
        $fee = $user->specialistProfile->consultation_fee ?? 0;

        $videoSessions = TherapySession::where('specialist_id', $user->id)
            ->where('status', 'completed')
            ->where('session_type', 'video')
            ->get();

        $audioSessions = TherapySession::where('specialist_id', $user->id)
            ->where('status', 'completed')
            ->where('session_type', 'audio')
            ->get();

        $textSessions = TherapySession::where('specialist_id', $user->id)
            ->where('status', 'completed')
            ->where('session_type', 'text')
            ->get();

        return response()->json([
            'success' => true,
            'breakdown' => [
                'video' => [
                    'count' => $videoSessions->count(),
                    'earnings' => $videoSessions->count() * $fee,
                ],
                'audio' => [
                    'count' => $audioSessions->count(),
                    'earnings' => $audioSessions->count() * $fee * 0.9,
                ],
                'text' => [
                    'count' => $textSessions->count(),
                    'earnings' => $textSessions->count() * $fee * 0.8,
                ],
            ],
        ]);
    }

    // ==================== PRIVATE METHODS ====================

    private function getMonthlyEarningsData($specialistId)
    {
        $fee = User::find($specialistId)->specialistProfile->consultation_fee ?? 0;
        $months = [];
        $earnings = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $monthlySessions = TherapySession::where('specialist_id', $specialistId)
                ->where('status', 'completed')
                ->whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->get();

            $monthlyEarnings = 0;
            foreach ($monthlySessions as $session) {
                if ($session->session_type === 'video') {
                    $monthlyEarnings += $fee;
                } elseif ($session->session_type === 'audio') {
                    $monthlyEarnings += $fee * 0.9;
                } elseif ($session->session_type === 'text') {
                    $monthlyEarnings += $fee * 0.8;
                }
            }

            $earnings[] = $monthlyEarnings;
        }

        return [
            'months' => $months,
            'earnings' => $earnings,
        ];
    }

    private function getMonthlySessionsData($specialistId)
    {
        $months = [];
        $sessions = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $sessionCount = TherapySession::where('specialist_id', $specialistId)
                ->where('status', 'completed')
                ->whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->count();

            $sessions[] = $sessionCount;
        }

        return [
            'months' => $months,
            'sessions' => $sessions,
        ];
    }

    private function getStatusText($status)
    {
        $statuses = [
            'pending' => __('Pending'),
            'paid' => __('Paid'),
            'failed' => __('Failed'),
        ];
        return $statuses[$status] ?? $status;
    }
}