<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\CreditTransaction;
use App\Models\DonorProfile;
use App\Models\RewardRedemption;
use App\Models\SpecialistPayment;
use App\Models\Notification;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PaymentController extends Controller
{
    // ==================== DASHBOARD ====================

    public function index()
    {
        $stats = [
            'total_revenue' => TherapySession::where('status', 'completed')
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee') ?? 0,
            'total_donated' => CreditTransaction::where('type', 'donation')->where('status', 'allocated')->sum('amount') ?? 0,
            'pending_credit_requests' => CreditTransaction::where('type', 'credit_request')->where('status', 'pending')->count(),
            'total_points_redeemed' => RewardRedemption::where('status', 'completed')->sum('points_spent') ?? 0,
            'pending_donations' => CreditTransaction::where('type', 'donation')->where('status', 'pending')->count(),
        ];

        return view('admin.payments.index', compact('stats'));
    }

    // ==================== CREDIT REQUESTS ====================

    public function creditRequests()
    {
        return view('admin.payments.index');
    }

    public function getCreditRequestsData(Request $request)
    {
        $query = CreditTransaction::where('type', 'credit_request')
            ->with(['donor', 'recipient'])
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('recipient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $requests = $query->paginate($perPage);

        $requests->getCollection()->transform(function ($req) {
            $req->user_name = $req->recipient ? $req->recipient->name : __('Deleted User');
            $req->user_email = $req->recipient ? $req->recipient->email : '';
            return $req;
        });

        return response()->json(['success' => true, 'data' => $requests->items(), 'total' => $requests->total(), 'per_page' => $requests->perPage(), 'current_page' => $requests->currentPage(), 'last_page' => $requests->lastPage()]);
    }

    public function approveCreditRequest($id)
    {
        $transaction = CreditTransaction::where('type', 'credit_request')->where('status', 'pending')->findOrFail($id);
        $user = $transaction->recipient;

        $transaction->update([
            'status' => 'allocated',
            'description' => $transaction->description . "\n" . __('Approved by admin on :date', ['date' => now()->format('Y-m-d H:i')])
        ]);
        $user->increment('credit_balance', $transaction->amount);

        Notification::create([
            'user_id' => $user->id,
            'title' => __('Credits Added Successfully'),
            'message' => __('Credit request approved. $:amount added to user balance.', ['amount' => number_format($transaction->amount, 2)]),
            'type' => 'payment',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'approve_credit_request',
            'details' => [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]
        ]);

        return response()->json(['success' => true, 'message' => __('Credit request approved. $:amount added to user balance.', ['amount' => number_format($transaction->amount, 2)])]);
    }

    public function rejectCreditRequest($id)
    {
        $transaction = CreditTransaction::where('type', 'credit_request')->where('status', 'pending')->findOrFail($id);
        $user = $transaction->recipient;

        $transaction->update(['status' => 'expired']);

        Notification::create([
            'user_id' => $user->id,
            'title' => __('Credit Request Rejected'),
            'message' => __('Credit request rejected.'),
            'type' => 'payment',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'reject_credit_request',
            'details' => [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'reason' => 'rejected_by_admin',
            ]
        ]);

        return response()->json(['success' => true, 'message' => __('Credit request rejected.')]);
    }

    // ==================== DONATIONS ====================

    public function donations()
    {
        return view('admin.payments.index');
    }

    public function getDonationsData(Request $request)
    {
        $query = CreditTransaction::where('type', 'donation')
            ->with(['donor', 'recipient'])
            ->orderBy('created_at', 'desc');
    
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('donor', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }
    
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
    
        $perPage = $request->get('per_page', 15);
        $donations = $query->paginate($perPage);
    
        $donations->getCollection()->transform(function ($donation) {
            $donation->donor_name = $donation->donor ? $donation->donor->name : __('Deleted User');
            $donation->donor_email = $donation->donor ? $donation->donor->email : '';
            $donation->recipient_name = $donation->recipient ? $donation->recipient->name : __('Not allocated yet');
    
            if ($donation->status === 'allocated') {
                // التعديل هنا: استخدم parent_transaction_id لربط التوزيعات بهذا التبرع فقط
                $allocated = CreditTransaction::where('parent_transaction_id', $donation->id)
                    ->where('type', 'donation_allocation')
                    ->sum('amount');
                $donation->remaining_amount = $donation->amount - $allocated;
            } else {
                $donation->remaining_amount = $donation->amount;
            }
            return $donation;
        });
    
        return response()->json(['success' => true, 'data' => $donations->items(), 'total' => $donations->total(), 'per_page' => $donations->perPage(), 'current_page' => $donations->currentPage(), 'last_page' => $donations->lastPage()]);
    }

    public function approveDonation($id)
    {
        $transaction = CreditTransaction::where('type', 'donation')->where('status', 'pending')->findOrFail($id);
        $user = $transaction->donor;

        if (!$user->hasRole('donor')) {
            $user->assignRole('donor');
        }

        $donorProfile = DonorProfile::firstOrCreate(['user_id' => $user->id]);
        $donorProfile->total_donated = $donorProfile->total_donated + $transaction->amount;
        $donorProfile->save();

        $transaction->update([
            'status' => 'allocated',
            'description' => $transaction->description . "\n" . __('Approved by admin on :date', ['date' => now()->format('Y-m-d H:i')])
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => __('Donation Confirmed'),
            'message' => __('Your donation has been confirmed. Thank you for your generosity!'),
            'type' => 'payment',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'approve_donation',
            'details' => [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'donor_id' => $user->id,
                'donor_name' => $user->name,
                'donor_email' => $user->email,
            ]
        ]);

        return response()->json(['success' => true, 'message' => __('Donation approved successfully.')]);
    }

    public function rejectDonation($id)
    {
        try {
            $transaction = CreditTransaction::where('type', 'donation')->where('status', 'pending')->findOrFail($id);
            $user = $transaction->donor;
    
            // تحقق من وجود توزيعات على هذا التبرع
            $hasAllocations = CreditTransaction::where('parent_transaction_id', $transaction->id)
                ->where('type', 'donation_allocation')
                ->exists();
    
            if ($hasAllocations) {
                return response()->json([
                    'success' => false,
                    'message' => __('Cannot reject donation that has already been allocated to patients.')
                ], 422);
            }
    
            $transaction->update(['status' => 'expired']);
    
            Notification::create([
                'user_id' => $user->id,
                'title' => __('Donation Not Processed'),
                'message' => __('Your donation request could not be processed. Please contact support.'),
                'type' => 'payment',
                'is_read' => false,
                'sent_at' => now(),
            ]);
    
            SystemLog::create([
                'admin_id' => Auth::id(),
                'action' => 'reject_donation',
                'details' => [
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'donor_id' => $user->id,
                    'donor_name' => $user->name,
                    'donor_email' => $user->email,
                    'reason' => 'rejected_by_admin',
                ]
            ]);
    
            return response()->json([
                'success' => true,
                'message' => __('Donation request rejected.')
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Reject donation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while rejecting the donation.')
            ], 500);
        }
    }

    public function allocateDonation(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:credit_transactions,id',
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);
    
        $transaction = CreditTransaction::where('type', 'donation')->where('status', 'allocated')->findOrFail($request->transaction_id);
        $donor = $transaction->donor;
        $recipient = User::findOrFail($request->recipient_id);
    
        // التعديل هنا: حساب التوزيعات الخاصة بهذا التبرع فقط
        $allocatedSoFar = CreditTransaction::where('parent_transaction_id', $transaction->id)
            ->where('type', 'donation_allocation')
            ->sum('amount');
        
        $remaining = $transaction->amount - $allocatedSoFar;
    
        if ($request->amount > $remaining) {
            return response()->json([
                'success' => false,
                'message' => __('Amount exceeds remaining donation balance. Remaining: $:amount', ['amount' => number_format($remaining, 2)])
            ], 422);
        }

        CreditTransaction::create([
            'donor_id' => $donor->id,
            'recipient_id' => $recipient->id,
            'amount' => $request->amount,
            'status' => 'allocated',
            'type' => 'donation_allocation',
            'parent_transaction_id' => $transaction->id,  // ← ربط بالتبرع الأصلي
            'description' => __('Allocated from donation #:id to :recipient on :date', [
                'id' => $transaction->id,
                'recipient' => $recipient->name,
                'date' => now()->format('Y-m-d H:i')
            ]),
        ]);

        $recipient->increment('credit_balance', $request->amount);

        Notification::create([
            'user_id' => $recipient->id,
            'title' => __('Donation Received'),
            'message' => __('Donation allocated successfully. $:amount added to :recipient\'s balance.', [
                'amount' => number_format($request->amount, 2),
                'recipient' => $recipient->name
            ]),
            'type' => 'payment',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        Notification::create([
            'user_id' => $donor->id,
            'title' => __('Donation Allocated'),
            'message' => __('Your donation has been allocated to help :recipient with their therapy sessions.', ['recipient' => $recipient->name]),
            'type' => 'payment',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'allocate_donation',
            'details' => [
                'donation_transaction_id' => $transaction->id,
                'amount' => $request->amount,
                'donor_id' => $donor->id,
                'donor_name' => $donor->name,
                'recipient_id' => $recipient->id,
                'recipient_name' => $recipient->name,
                'remaining_balance' => $remaining - $request->amount,
            ] 
       ]);

        return response()->json([
            'success' => true,
            'message' => __('Donation allocated successfully. $:amount added to :recipient\'s balance.', [
                'amount' => number_format($request->amount, 2),
                'recipient' => $recipient->name
            ])
        ]);
    }

    // ==================== POINTS REDEMPTION ====================

    public function redemptions()
    {
        return view('admin.payments.index');
    }

    public function getRedemptionsData(Request $request)
    {
        $query = RewardRedemption::with(['user', 'reward'])
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $redemptions = $query->paginate($perPage);

        $redemptions->getCollection()->transform(function ($redemption) {
            $redemption->user_name = $redemption->user->name;
            $redemption->user_email = $redemption->user->email;

            // Decode Json Info
            $reward_name = $redemption->reward->name;
            if ($reward_name && $this->isJson($reward_name)) {
                $decoded = json_decode($reward_name, true);
                $locale = app()->getLocale();
                $reward_name = $decoded[$locale] ?? $decoded['en'] ?? $reward_name;
            }
            $redemption->reward_name = $reward_name;
            $redemption->reward_type = $redemption->reward->type;

            $statusTexts = [
                'pending' => __('Pending'),
                'completed' => __('Completed'),
                'cancelled' => __('Cancelled'),
                'failed' => __('Failed'),
            ];
            $redemption->status_text = $statusTexts[$redemption->status] ?? $redemption->status;

            return $redemption;
        });

        return response()->json([
            'success' => true,
            'data' => $redemptions->items(),
            'total' => $redemptions->total(),
            'per_page' => $redemptions->perPage(),
            'current_page' => $redemptions->currentPage(),
            'last_page' => $redemptions->lastPage(),
        ]);
    }

    // ==================== SPECIALISTS ====================

    public function specialists()
    {
        return view('admin.payments.index');
    }

    /**
 * Get specialists data for DataTable (AJAX)
 */
public function getSpecialistsData(Request $request)
{
    $query = User::role('specialist')
        ->whereHas('specialistProfile', function ($q) {
            $q->where('is_verified', true);
        })
        ->with(['specialistProfile'])
        ->select('users.*');

    // Search
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Filter by status (if needed)
    if ($request->has('status') && $request->status !== 'all') {
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'suspended') {
            $query->where('is_active', false);
        }
    }

    // Pagination
    $perPage = $request->get('per_page', 15);
    $specialists = $query->paginate($perPage);

    // Platform fee percentage (default 10%, can be changed in settings)
    $platformPercent = (int) (app()->has('platform_fee_percent') 
        ? app('platform_fee_percent') 
        : 10);

    $specialists->getCollection()->transform(function ($specialist) use ($platformPercent) {
        $profile = $specialist->specialistProfile;
        $fee = $profile->consultation_fee ?? 0;

        // Get all completed sessions grouped by month
        $completedSessions = TherapySession::where('specialist_id', $specialist->id)
            ->where('status', 'completed')
            ->orderBy('session_datetime', 'asc')
            ->get();

        // Calculate session counts
        $videoSessions = $completedSessions->where('session_type', 'video')->count();
        $audioSessions = $completedSessions->where('session_type', 'audio')->count();
        $textSessions = $completedSessions->where('session_type', 'text')->count();

        // Calculate GROSS earnings (with session type discounts, NO platform fee)
        $videoEarnings = $videoSessions * $fee;
        $audioEarnings = $audioSessions * $fee * 0.9;
        $textEarnings = $textSessions * $fee * 0.8;
        $grossEarnings = $videoEarnings + $audioEarnings + $textEarnings;

        // Get paid records
        $paidRecords = SpecialistPayment::where('specialist_id', $specialist->id)
            ->where('status', 'paid')
            ->get();
        
        $totalPaid = $paidRecords->sum('final_amount');
        
        // Get paid months to avoid double-counting
        $paidMonths = $paidRecords->pluck('month_year')->toArray();
        
        // Calculate pending earnings by month
        $pendingEarnings = 0;
        $currentMonth = null;
        $monthEarnings = 0;
        
        foreach ($completedSessions as $session) {
            $monthYear = $session->session_datetime->format('m/Y');
            
            if ($currentMonth && $monthYear != $currentMonth) {
                // Month changed, check if this month was paid
                if (!in_array($currentMonth, $paidMonths)) {
                    // Apply platform fee for unpaid months
                    $platformFee = ($monthEarnings * $platformPercent) / 100;
                    $pendingEarnings += ($monthEarnings - $platformFee);
                }
                $monthEarnings = 0;
            }
            
            $currentMonth = $monthYear;
            
            // Calculate session earning (gross, without platform fee)
            if ($session->session_type === 'video') {
                $monthEarnings += $fee;
            } elseif ($session->session_type === 'audio') {
                $monthEarnings += $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $monthEarnings += $fee * 0.8;
            }
        }
        
        // Add last month if not paid
        if ($currentMonth && !in_array($currentMonth, $paidMonths) && $monthEarnings > 0) {
            $platformFee = ($monthEarnings * $platformPercent) / 100;
            $pendingEarnings += ($monthEarnings - $platformFee);
        }
        
        // Set all values for the response
        $specialist->video_sessions = $videoSessions;
        $specialist->audio_sessions = $audioSessions;
        $specialist->text_sessions = $textSessions;
        $specialist->total_sessions = $videoSessions + $audioSessions + $textSessions;
        $specialist->consultation_fee = $fee;
        $specialist->total_earnings = $grossEarnings;
        $specialist->total_paid = $totalPaid;
        $specialist->pending_payment = max(0, $pendingEarnings); // Ensure not negative
        $specialist->profile_image_url = $specialist->getProfileImageUrl();

        return $specialist;
    });

    return response()->json([
        'success' => true,
        'data' => $specialists->items(),
        'total' => $specialists->total(),
        'per_page' => $specialists->perPage(),
        'current_page' => $specialists->currentPage(),
        'last_page' => $specialists->lastPage(),
    ]);
}

    // ==================== PAYOUTS HISTORY ====================

    public function specialistPayouts()
    {
        return view('admin.payments.specialist-payouts');
    }

    public function getPayoutsData(Request $request)
    {
        $query = SpecialistPayment::with(['specialist'])
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('specialist', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $payouts = $query->paginate($perPage);

        $payouts->getCollection()->transform(function ($payout) {
            $payout->specialist_name = $payout->specialist->name;
            $payout->specialist_email = $payout->specialist->email;
            $payout->profile_image_url = $payout->specialist->getProfileImageUrl();
            return $payout;
        });

        return response()->json([
            'success' => true,
            'data' => $payouts->items(),
            'total' => $payouts->total(),
            'per_page' => $payouts->perPage(),
            'current_page' => $payouts->currentPage(),
            'last_page' => $payouts->lastPage(),
        ]);
    }

    /**
     * Pay selected specialists (bulk pay for selected only)
     */
    public function paySelectedSpecialists(Request $request)
    {
        $request->validate([
            'specialist_ids' => 'required|array',
            'specialist_ids.*' => 'exists:users,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'platform_percent' => 'nullable|integer|min:0|max:10',
        ]);
    
        $month = $request->month;
        $year = $request->year;
        $platformPercent = (int)($request->platform_percent ?? 0);
        $monthYear = sprintf('%02d/%d', $month, $year);
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
    
        $paidCount = 0;
        $errors = [];
    
        foreach ($request->specialist_ids as $specialistId) {
            $specialist = User::find($specialistId);
            if (!$specialist) continue;
    
            $existingPayment = SpecialistPayment::where('specialist_id', $specialistId)
                ->where('month_year', $monthYear)
                ->where('status', 'paid')
                ->first();
    
            if ($existingPayment) {
                $errors[] = "{$specialist->name} " . __('already paid for this month');
                continue;
            }
    
            $profile = $specialist->specialistProfile;
            $fee = $profile->consultation_fee ?? 0;
    
            $videoSessions = TherapySession::where('specialist_id', $specialistId)
                ->where('status', 'completed')
                ->where('session_type', 'video')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();
    
            $audioSessions = TherapySession::where('specialist_id', $specialistId)
                ->where('status', 'completed')
                ->where('session_type', 'audio')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();
    
            $textSessions = TherapySession::where('specialist_id', $specialistId)
                ->where('status', 'completed')
                ->where('session_type', 'text')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();
    
            $totalSessions = $videoSessions + $audioSessions + $textSessions;
            if ($totalSessions == 0) continue;
    
            $videoEarnings = $videoSessions * $fee;
            $audioEarnings = $audioSessions * $fee * 0.9;
            $textEarnings = $textSessions * $fee * 0.8;
            $earnings = $videoEarnings + $audioEarnings + $textEarnings;
    
            $platformFee = ($earnings * $platformPercent) / 100;
            $finalAmount = $earnings - $platformFee;
    
            SpecialistPayment::updateOrCreate(
                ['specialist_id' => $specialistId, 'month_year' => $monthYear],
                [
                    'amount' => $earnings,
                    'platform_fee' => $platformFee,
                    'final_amount' => $finalAmount,
                    'status' => 'paid',
                    'notes' => __('Paid by admin via bulk pay on :date', ['date' => now()->format('Y-m-d H:i')]),
                    'paid_at' => now(),
                ]
            );
    
            Notification::create([
                'user_id' => $specialistId,
                'title' => __('Payout Processed - :month', ['month' => $monthYear]),
                'message' => __('Your earnings for :month ($:amount) have been transferred to your account.', [
                    'month' => $monthYear,
                    'amount' => number_format($finalAmount, 2)
                ]),
                'type' => 'payment',
                'is_read' => false,
                'sent_at' => now(),
            ]);

            // After creating paid record, delete pending requests for this specialist
            $this->deletePendingRequests($specialistId);
    
            $paidCount++;
        }
    
        return response()->json([
            'success' => true,
            'message' => __('Selected payouts processed successfully for :count specialists', ['count' => $paidCount]),
            'paid_count' => $paidCount,
            'errors' => $errors
        ]);
    }

    public function generatePayoutReport(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'platform_percent' => 'nullable|integer|min:0|max:10',
        ]);
    
        $month = $request->month;
        $year = $request->year;
        $platformPercent = (int)($request->platform_percent ?? 0); // ← Force integer
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
    
        $specialists = User::role('specialist')
            ->whereHas('specialistProfile', function ($q) {
                $q->where('is_verified', true);
            })
            ->with('specialistProfile')
            ->get();
    
        $report = [];
        foreach ($specialists as $specialist) {
            $profile = $specialist->specialistProfile;
            $fee = $profile->consultation_fee ?? 0;
    
            $videoSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'video')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();
    
            $audioSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'audio')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();
    
            $textSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'text')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();
    
            $totalSessions = $videoSessions + $audioSessions + $textSessions;
            if ($totalSessions == 0) continue;
    
            $videoEarnings = $videoSessions * $fee;
            $audioEarnings = $audioSessions * $fee * 0.9;
            $textEarnings = $textSessions * $fee * 0.8;
            $earnings = $videoEarnings + $audioEarnings + $textEarnings;
    
            // ← CALCULATE PLATFORM FEE HERE
            $platformFee = ($earnings * $platformPercent) / 100;
            $finalAmount = $earnings - $platformFee;
    
            $existingPayment = SpecialistPayment::where('specialist_id', $specialist->id)
                ->where('month_year', sprintf('%02d/%d', $month, $year))
                ->first();
    
            $isPaid = $existingPayment && $existingPayment->status === 'paid';
    
            $report[] = [
                'specialist_id' => $specialist->id,
                'specialist_name' => $specialist->name,
                'specialist_email' => $specialist->email,
                'profile_image_url' => $specialist->getProfileImageUrl(),
                'consultation_fee' => $fee,
                'video_sessions' => $videoSessions,
                'audio_sessions' => $audioSessions,
                'text_sessions' => $textSessions,
                'total_sessions' => $totalSessions,
                'earnings' => $earnings,
                'platform_percent' => $platformPercent,
                'platform_fee' => $platformFee,
                'final_amount' => $finalAmount,
                'is_paid' => $isPaid,
            ];
        }
    
        return response()->json([
            'success' => true,
            'report' => $report,
            'month' => $month,
            'year' => $year,
            'platform_percent' => $platformPercent,
            'month_name' => Carbon::create($year, $month, 1)->translatedFormat('F Y'),
        ]);
    }

    public function paySpecialist(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required|exists:users,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'platform_fee' => 'nullable|numeric|min:0',
        ]);

        $specialist = User::findOrFail($request->specialist_id);
        $monthYear = sprintf('%02d/%d', $request->month, $request->year);

        $existingPayment = SpecialistPayment::where('specialist_id', $specialist->id)
            ->where('month_year', $monthYear)
            ->first();

        if ($existingPayment && $existingPayment->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => __('This specialist has already been paid for this month.')
            ], 422);
        }

        if ($existingPayment) {
            $existingPayment->update([
                'status' => 'paid',
                'final_amount' => $request->amount,
                'platform_fee' => $request->platform_fee ?? 0,
                'paid_at' => now(),
                'notes' => __('Paid by admin on :date', ['date' => now()->format('Y-m-d H:i')])
            ]);
        } else {
            SpecialistPayment::create([
                'specialist_id' => $specialist->id,
                'amount' => $request->amount,
                'month_year' => $monthYear,
                'platform_fee' => $request->platform_fee ?? 0,
                'final_amount' => $request->amount - ($request->platform_fee ?? 0),
                'status' => 'paid',
                'notes' => __('Paid by admin on :date', ['date' => now()->format('Y-m-d H:i')]),
                'paid_at' => now(),
            ]);
        }

        Notification::create([
            'user_id' => $specialist->id,
            'title' => __('Payout Processed - :month', ['month' => $monthYear]),
            'message' => __('Your earnings for :month ($:amount) have been transferred to your account.', [
                'month' => $monthYear,
                'amount' => number_format($request->amount, 2)
            ]),
            'type' => 'payment',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'process_payout',
            'details' => [
                'specialist_id' => $specialist->id,
                'specialist_name' => $specialist->name,
                'specialist_email' => $specialist->email,
                'amount' => $request->amount,
                'platform_fee' => $request->platform_fee ?? 0,
                'month' => $request->month,
                'year' => $request->year,
                'month_year' => sprintf('%02d/%d', $request->month, $request->year),
            ]
        ]);

        // After successfully creating/updating payment as 'paid'
        // Delete any pending payout requests for this specialist
        $this->deletePendingRequests($$specialist->id);

        return response()->json([
            'success' => true,
            'message' => __('Payout of $:amount processed successfully for :name', [
                'amount' => number_format($request->amount, 2),
                'name' => $specialist->name
            ])
        ]);
    }

    /**
     * Pay ALL specialists for a given month (with confirmation)
     */
    public function payAllSpecialists(Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
            'platform_percent' => 'nullable|integer|min:0|max:10',
        ]);

        $month = $request->month;
        $year = $request->year;
        $platformPercent = $request->platform_percent ?? 0;
        $monthYear = sprintf('%02d/%d', $month, $year);
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $specialists = User::role('specialist')
            ->whereHas('specialistProfile', function ($q) { $q->where('is_verified', true); })
            ->with('specialistProfile')
            ->get();

        $paidCount = 0;
        $processedCount = 0;

        foreach ($specialists as $specialist) {
            $existingPayment = SpecialistPayment::where('specialist_id', $specialist->id)
                ->where('month_year', $monthYear)
                ->where('status', 'paid')
                ->first();

            if ($existingPayment) continue;

            $profile = $specialist->specialistProfile;
            $fee = $profile->consultation_fee ?? 0;

            $videoSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'video')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();

            $audioSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'audio')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();

            $textSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'text')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();

            $totalSessions = $videoSessions + $audioSessions + $textSessions;
            if ($totalSessions == 0) continue;

            $videoEarnings = $videoSessions * $fee;
            $audioEarnings = $audioSessions * $fee * 0.9;
            $textEarnings = $textSessions * $fee * 0.8;
            $earnings = $videoEarnings + $audioEarnings + $textEarnings;

            $platformFee = ($earnings * $platformPercent) / 100;
            $finalAmount = $earnings - $platformFee;

            SpecialistPayment::updateOrCreate(
                ['specialist_id' => $specialist->id, 'month_year' => $monthYear],
                [
                    'amount' => $earnings,
                    'platform_fee' => $platformFee,
                    'final_amount' => $finalAmount,
                    'status' => 'paid',
                    'notes' => __('Paid by admin via bulk pay all on :date', ['date' => now()->format('Y-m-d H:i')]),
                    'paid_at' => now(),
                ]
            );

            Notification::create([
                'user_id' => $specialist->id,
                'title' => __('Payout Processed - :month', ['month' => $monthYear]),
                'message' => __('Your earnings for :month ($:amount) have been transferred to your account.', [
                    'month' => $monthYear,
                    'amount' => number_format($finalAmount, 2)
                ]),
                'type' => 'payment',
                'is_read' => false,
                'sent_at' => now(),
            ]);

            // After creating paid record, delete pending requests for this specialist
            $this->deletePendingRequests($specialist->id);

            $paidCount++;
            $processedCount++;
        }

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'process_payout_all',
            'details' => [
                'month' => $month,
                'year' => $year,
                'month_year' => $monthYear,
                'platform_percent' => $platformPercent,
                'specialists_count' => $paidCount,
                'processed_count' => $processedCount,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('All eligible specialists have been paid for :month. :count specialists processed.', [
                'month' => Carbon::create($year, $month, 1)->translatedFormat('F Y'),
                'count' => $paidCount
            ]),
            'paid_count' => $paidCount
        ]);
    }

    // Export Reports Functions
    public function exportPayoutPdf(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $platformPercent = $request->platform_percent ?? 0;
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        $monthName = Carbon::create($year, $month, 1)->translatedFormat('F Y');

        $specialists = User::role('specialist')
            ->whereHas('specialistProfile', function ($q) {
                $q->where('is_verified', true);
            })
            ->with('specialistProfile')
            ->get();

        $report = [];
        $totalEarnings = $totalPlatformFees = $totalFinalAmount = 0;

        foreach ($specialists as $specialist) {
            $profile = $specialist->specialistProfile;
            $fee = $profile->consultation_fee ?? 0;

            $videoSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'video')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();

            $audioSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'audio')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();

            $textSessions = TherapySession::where('specialist_id', $specialist->id)
                ->where('status', 'completed')
                ->where('session_type', 'text')
                ->whereBetween('session_datetime', [$startDate, $endDate])
                ->count();

            $totalSessions = $videoSessions + $audioSessions + $textSessions;
            if ($totalSessions == 0)
                continue;

            $videoEarnings = $videoSessions * $fee;
            $audioEarnings = $audioSessions * $fee * 0.9;
            $textEarnings = $textSessions * $fee * 0.8;
            $earnings = $videoEarnings + $audioEarnings + $textEarnings;

            $platformFee = ($earnings * $platformPercent) / 100;
            $finalAmount = $earnings - $platformFee;

            $report[] = [
                'specialist_name' => $specialist->name,
                'specialist_email' => $specialist->email,
                'consultation_fee' => $fee,
                'video_sessions' => $videoSessions,
                'audio_sessions' => $audioSessions,
                'text_sessions' => $textSessions,
                'total_sessions' => $totalSessions,
                'earnings' => $earnings,
                'platform_fee' => $platformFee,
                'final_amount' => $finalAmount,
            ];

            $totalEarnings += $earnings;
            $totalPlatformFees += $platformFee;
            $totalFinalAmount += $finalAmount;
        }

        $stats = [
            'month_name' => $monthName,
            'platform_percent' => $platformPercent,
            'total_earnings' => $totalEarnings,
            'total_platform_fees' => $totalPlatformFees,
            'total_final_amount' => $totalFinalAmount,
            'total_specialists' => count($report),
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.payments.reports.export-payout-pdf', compact('report', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('payout-report-' . $monthName . '.pdf');
    }

    /**
     * Export Payouts History (from specialist_payments table)
     */
    public function exportPayoutsHistoryPdf(Request $request)
    {
        $query = SpecialistPayment::with(['specialist'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $payouts = $query->get();

        $payouts->transform(function ($payout) {
            $payout->specialist_name = $payout->specialist?->name ?? __('Deleted User');
            $payout->specialist_email = $payout->specialist?->email ?? '';
            return $payout;
        });

        $stats = [
            'total_amount' => $payouts->sum('final_amount'),
            'total_payouts' => $payouts->count(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.payments.reports.export-payouts-history-pdf', compact('payouts', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('payouts-history-' . date('Y-m-d') . '.pdf');
    }

    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);

        $query = TherapySession::where('status', 'completed')
            ->with(['patient', 'specialist', 'specialist.specialistProfile']);

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('session_datetime', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('session_datetime', '<=', $request->date_to);
        }

        $payments = $query->orderBy('session_datetime', 'desc')->get();

        foreach ($payments as $payment) {
            $fee = $payment->specialist->specialistProfile->consultation_fee ?? 0;
            if ($payment->session_type === 'audio')
                $fee = $fee * 0.9;
            elseif ($payment->session_type === 'text')
                $fee = $fee * 0.8;
            $payment->amount = $fee;
            $payment->payment_method = $payment->is_paid_by_credit ? 'Credit' : 'Cash';
        }

        $stats = [
            'total_amount' => $payments->sum('amount'),
            'total_sessions' => $payments->count(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.payments.reports.export-pdf', compact('payments', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('payments-report-' . date('Y-m-d') . '.pdf');
    }

    public function exportCreditRequestsPdf(Request $request)
    {
        $query = CreditTransaction::where('type', 'credit_request')->with(['recipient']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        $requests->transform(function ($req) {
            $req->user_name = $req->recipient ? $req->recipient->name : __('Deleted User');
            $req->user_email = $req->recipient ? $req->recipient->email : '';
            return $req;
        });

        $html = view('admin.payments.reports.export-credit-requests-pdf', compact('requests'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('credit-requests-report-' . date('Y-m-d') . '.pdf');
    }

    public function exportDonationsPdf(Request $request)
    {
        $query = CreditTransaction::where('type', 'donation')->with(['donor', 'recipient']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $donations = $query->orderBy('created_at', 'desc')->get();

        $donations->transform(function ($donation) {
            $donation->donor_name = $donation->donor ? $donation->donor->name : __('Deleted User');
            $donation->recipient_name = $donation->recipient ? $donation->recipient->name : __('Not allocated');
            return $donation;
        });

        $html = view('admin.payments.reports.export-donations-pdf', compact('donations'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('donations-report-' . date('Y-m-d') . '.pdf');
    }

    public function exportRedemptionsPdf(Request $request)
    {
        $query = RewardRedemption::with(['user', 'reward'])->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $redemptions = $query->get();

        $redemptions->transform(function ($redemption) {
            $redemption->user_name = $redemption->user->name;
            $reward_name = $redemption->reward->name;
            if ($reward_name && $this->isJson($reward_name)) {
                $decoded = json_decode($reward_name, true);
                $locale = app()->getLocale();
                $reward_name = $decoded[$locale] ?? $decoded['en'] ?? $reward_name;
            }
            $redemption->reward_name = $reward_name;

            $statusTexts = [
                'pending' => __('Pending'),
                'completed' => __('Completed'),
                'cancelled' => __('Cancelled'),
                'failed' => __('Failed'),
            ];
            $redemption->status_text = $statusTexts[$redemption->status] ?? $redemption->status;
            return $redemption;
        });

        $html = view('admin.payments.reports.export-redemptions-pdf', compact('redemptions'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('redemptions-report-' . date('Y-m-d') . '.pdf');
    }

    public function exportSpecialistsPdf(Request $request)
    {
        $query = User::role('specialist')
            ->whereHas('specialistProfile', function ($q) {
                $q->where('is_verified', true);
            })
            ->with(['specialistProfile'])
            ->select('users.*');

        $specialists = $query->get();

        foreach ($specialists as $specialist) {
            $profile = $specialist->specialistProfile;
            $fee = $profile->consultation_fee ?? 0;

            $videoSessions = TherapySession::where('specialist_id', $specialist->id)->where('status', 'completed')->where('session_type', 'video')->count();
            $audioSessions = TherapySession::where('specialist_id', $specialist->id)->where('status', 'completed')->where('session_type', 'audio')->count();
            $textSessions = TherapySession::where('specialist_id', $specialist->id)->where('status', 'completed')->where('session_type', 'text')->count();

            $specialist->video_sessions = $videoSessions;
            $specialist->audio_sessions = $audioSessions;
            $specialist->text_sessions = $textSessions;
            $specialist->total_sessions = $videoSessions + $audioSessions + $textSessions;
            $specialist->consultation_fee = $fee;
            $specialist->total_earnings = ($videoSessions * $fee) + ($audioSessions * $fee * 0.9) + ($textSessions * $fee * 0.8);
            $specialist->total_paid = SpecialistPayment::where('specialist_id', $specialist->id)->where('status', 'paid')->sum('final_amount');
            $specialist->pending_payment = $specialist->total_earnings - $specialist->total_paid;
            $specialist->profile_image_url = $specialist->getProfileImageUrl();
        }

        $html = view('admin.payments.reports.export-specialists-pdf', compact('specialists'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('specialists-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Check if string is JSON
     */
    private function isJson($string)
    {
        if (!is_string($string))
            return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Delete pending payout requests for a specialist
     */
    private function deletePendingRequests($specialistId)
    {
        $deleted = SpecialistPayment::where('specialist_id', $specialistId)
            ->where('status', 'pending')
            ->delete();

        if ($deleted > 0) {
            \Log::info("Deleted {$deleted} pending payout request(s) for specialist ID: {$specialistId}");
        }
    }
}