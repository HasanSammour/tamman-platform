<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DonorProfile;
use App\Models\CreditTransaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    /**
     * Show donation page
     */
    public function index()
    {
        $user = Auth::user();
        $isDonor = $user->hasRole('donor');
        $donorProfile = $user->donorProfile;

        // Get user's donation stats
        $stats = [
            'total_donated' => $donorProfile?->total_donated ?? 0,
            'users_supported' => CreditTransaction::where('donor_id', $user->id)
                ->where('type', 'donation_allocation')
                ->distinct('recipient_id')
                ->count('recipient_id'),
            'total_transactions' => CreditTransaction::where('donor_id', $user->id)->count(),
        ];

        // Get donation history
        $donationHistory = $this->getDonationHistory($user);

        return view('donate', compact('user', 'isDonor', 'donorProfile', 'stats', 'donationHistory'));
    }

    /**
     * Process donation request
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000',
            'payment_method' => 'required|in:credit_card,bank_transfer',
        ]);

        $user = Auth::user();

        // Check if user already has a pending donation
        $existingPending = CreditTransaction::where('donor_id', $user->id)
            ->where('type', 'donation')
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a pending donation request. Please wait for it to be processed.')
            ], 422);
        }

        // Assign donor role if not already
        if (!$user->hasRole('donor')) {
            $user->assignRole('donor');
        }

        // Create or update donor profile
        DonorProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['total_donated' => $user->donorProfile?->total_donated ?? 0]
        );

        // Create pending credit transaction with type 'donation'
        $transaction = CreditTransaction::create([
            'donor_id' => $user->id,
            'recipient_id' => null,
            'amount' => $request->amount,
            'status' => 'pending',
            'type' => 'donation',  // IMPORTANT: Set type correctly
            'description' => 'تبرع من المستخدم: ' . $user->name,
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => $user->id,
            'title' => __('Donation Request Submitted'),
            'message' => __('Your donation request of $:amount has been submitted. Our team will contact you within 24 hours.', ['amount' => $request->amount]),
            'type' => 'donation',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        // Create admin notification
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => __('New Donation Request'),
                'message' => __('User :name has requested to donate $:amount.', [
                    'name' => $user->name,
                    'amount' => $request->amount
                ]),
                'type' => 'donation',
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Thank you for your generosity! Our team will contact you within 24 hours to complete the donation process.'),
            'transaction_id' => $transaction->id
        ]);
    }

       /**
     * Get donation history for user
     */
    private function getDonationHistory($user)
    {
        $isAdmin = $user->hasRole('admin');

        // Donations GIVEN by this user (as donor) - Simplified view
        $donationsGiven = CreditTransaction::where('donor_id', $user->id)
            ->where('type', 'donation')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('id')
            ->map(function ($transaction) {
                // Get allocations for this donation - فقط للإحصائيات بدون أسماء
                $allocations = CreditTransaction::where('parent_transaction_id', $transaction->id)
                ->where('type', 'donation_allocation')
                ->get();
                
                $totalAllocated = $allocations->sum('amount');
                $remaining = $transaction->amount - $totalAllocated;

                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'status_text' => $this->getStatusText($transaction->status),
                    'date' => $transaction->created_at->translatedFormat('M d, Y'),
                    'total_allocated' => $totalAllocated,
                    'remaining' => $remaining,
                    'is_fully_allocated' => $remaining <= 0,
                    'recipients_count' => $allocations->groupBy('recipient_id')->count(),
                ];
            });

        // Donations RECEIVED by this user (as recipient) - يرى المتبرع فقط
        $donationsReceived = CreditTransaction::where('recipient_id', $user->id)
            ->where('type', 'donation_allocation')
            ->with(['donor'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'donor_name' => $transaction->donor?->name ?? __('Anonymous Donor'),
                    'date' => $transaction->created_at->translatedFormat('M d, Y'),
                ];
            });

        return [
            'given' => $donationsGiven,
            'received' => $donationsReceived,
        ];
    }

    /**
     * Get status text
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => __('Pending'),
            'allocated' => __('Allocated'),
            'used' => __('Used'),
            'expired' => __('Expired'),
        ];
        return $statuses[$status] ?? $status;
    }

    /**
     * Get user's donation allocations (for AJAX)
     */
    public function getAllocations()
    {
        $user = Auth::user();

        $allocations = CreditTransaction::where('donor_id', $user->id)
            ->where('type', 'donation_allocation')
            ->with('recipient')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($alloc) {
                return [
                    'id' => $alloc->id,
                    'amount' => $alloc->amount,
                    'recipient_name' => $alloc->recipient?->name ?? __('Deleted User'),
                    'date' => $alloc->created_at->translatedFormat('M d, Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'allocations' => $allocations,
        ]);
    }
}