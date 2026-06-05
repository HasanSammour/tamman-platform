<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\CreditTransaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    /**
     * Show add credits page
     */
    public function index()
    {
        $user = Auth::user();
        $transactions = CreditTransaction::where('recipient_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_credits' => $user->credit_balance,
            'total_received' => CreditTransaction::where('recipient_id', $user->id)
                ->where('status', 'allocated')
                ->sum('amount'),
            'total_used' => CreditTransaction::where('recipient_id', $user->id)
                ->where('status', 'used')
                ->sum('amount'),
        ];

        return view('patient.add-credits', compact('user', 'transactions', 'stats'));
    }

    /**
     * Request credit addition
     */
    public function request(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:5000',
            'payment_method' => 'required|in:bank_transfer,cash',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check if user already has a pending credit request
        $existingPending = CreditTransaction::where('recipient_id', $user->id)
            ->where('type', 'credit_request')
            ->where('status', 'pending')
            ->exists();
        
        if ($existingPending) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a pending credit request. Please wait for it to be processed.')
            ], 422);
        }

        // Create pending credit request
        $creditRequest = CreditTransaction::create([
            'donor_id' => null, // Will be filled by admin when processing
            'recipient_id' => $user->id,
            'amount' => $request->amount,
            'status' => 'pending',
            // 'type' is MISSING - will use default 'credit_request' which is correct but not explicit
            'type' => 'credit_request',
            'description' => 'طلب شحن رصيد من المستخدم: ' . $user->name . '. ملاحظات: ' . ($request->notes ?? 'لا توجد ملاحظات'),
        ]);

        // Create user notification
        Notification::create([
            'user_id' => $user->id,
            'title' => __('Credit Request Submitted'),
            'message' => __('Your request to add $:amount credits has been submitted. Our team will process it within 24 hours.', ['amount' => $request->amount]),
            'type' => 'credit',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        // Create admin notification
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => __('New Credit Request'),
                'message' => __('User :name has requested to add $:amount credits.', [
                    'name' => $user->name,
                    'amount' => $request->amount
                ]),
                'type' => 'credit',
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Your credit request has been submitted successfully! Our team will process it within 24 hours.'),
            'request_id' => $creditRequest->id
        ]);
    }

    /**
     * Get credit transactions history (AJAX)
     */
    public function history()
    {
        $user = Auth::user();

        $transactions = CreditTransaction::where('recipient_id', $user->id)
            ->with('donor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'transactions' => $transactions->map(function ($t) {
                return [
                    'id' => $t->id,
                    'amount' => $t->amount,
                    'status' => $t->status,
                    'status_text' => __(ucfirst($t->status)),
                    'date' => $t->created_at->translatedFormat('M d, Y'),
                    'donor_name' => $t->donor?->name ?? __('System'),
                ];
            }),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }
}