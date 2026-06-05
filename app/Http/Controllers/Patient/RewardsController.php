<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\PointTransaction;
use App\Models\CreditTransaction;
use App\Helpers\RewardHelper;
use App\Helpers\EmailHelper;
use App\Helpers\NotificationHelper;
use App\Mail\RewardRedeemedMail;
use App\Mail\FreeSessionRedeemedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class RewardsController extends Controller
{
    /**
     * Display rewards page
     */
    public function index()
    {
        $user = Auth::user();

        // Get all active rewards with caching
        $rewards = Cache::remember('active_rewards', 3600, function () {
            return Reward::active()->orderBy('sort_order')->get();
        });

        // Group rewards by type
        $groupedRewards = [
            'credit' => $rewards->where('type', 'credit'),
            'free_session' => $rewards->where('type', 'free_session'),
            'donate' => $rewards->where('type', 'donate'),
        ];

        // Check for pending free sessions
        $pendingFreeSessions = RewardRedemption::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('reward', function ($q) {
                $q->where('type', 'free_session');
            })
            ->whereDoesntHave('therapySession')
            ->get()
            ->keyBy(function ($redemption) {
                return $redemption->reward->session_type;
            });

        // Get user's recent redemptions
        $recentRedemptions = RewardRedemption::where('user_id', $user->id)
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get user's rank and milestone
        $rank = $user->getRank();
        $nextMilestone = $user->getNextMilestone();

        // Calculate accurate statistics
        $totalEarned = PointTransaction::where('user_id', $user->id)
            ->where('type', 'earned')
            ->where('points', '>', 0)
            ->sum('points');

        $totalRedeemed = PointTransaction::where('user_id', $user->id)
            ->where('type', 'redeemed')
            ->where('points', '<', 0)
            ->sum('points');

        $stats = [
            'total_earned' => $totalEarned,
            'total_redeemed' => abs($totalRedeemed),
            'total_redemptions' => RewardRedemption::where('user_id', $user->id)->where('status', 'completed')->count(),
            'current_balance' => $user->total_points,
            'credit_balance' => $user->credit_balance,
        ];

        // Get points history
        $pointsHistory = PointTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($transaction) {
                $description = $transaction->description;
                if ($description && $this->isJson($description)) {
                    $decoded = json_decode($description, true);
                    $locale = app()->getLocale();
                    $description = $decoded[$locale] ?? $decoded['en'] ?? $description;
                }
                $description = str_replace('Redeemed: ', '', $description);

                return [
                    'id' => $transaction->id,
                    'points' => $transaction->points,
                    'type' => $transaction->type,
                    'source' => $transaction->source,
                    'source_name' => RewardHelper::getSourceName($transaction->source),
                    'icon' => RewardHelper::getSourceIcon($transaction->source),
                    'color' => RewardHelper::getSourceColor($transaction->source),
                    'description' => $description,
                    'created_at' => $transaction->created_at,
                    'created_at_formatted' => $transaction->created_at->translatedFormat('M d, Y'),
                ];
            });

        return view('patient.rewards.index', compact(
            'user',
            'groupedRewards',
            'recentRedemptions',
            'rank',
            'nextMilestone',
            'stats',
            'pointsHistory',
            'pendingFreeSessions'
        ));
    }

    /**
     * Redeem a reward via AJAX
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:rewards,id',
        ]);

        $user = Auth::user();
        $reward = Reward::findOrFail($request->reward_id);

        // Check if reward is active
        if (!$reward->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('This reward is currently not available.'),
            ], 422);
        }

        // Check if user has enough points
        if ($user->total_points < $reward->points_needed) {
            return response()->json([
                'success' => false,
                'message' => __('You do not have enough points. You need :points points for this reward.', [
                    'points' => number_format($reward->points_needed)
                ]),
            ], 422);
        }

        // For free session, check if user already has pending of same type
        if ($reward->type === 'free_session') {
            $hasPending = RewardRedemption::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereHas('reward', function ($q) use ($reward) {
                    $q->where('type', 'free_session')
                        ->where('session_type', $reward->session_type);
                })
                ->whereDoesntHave('therapySession')
                ->exists();

            if ($hasPending) {
                return response()->json([
                    'success' => false,
                    'message' => __('You already have a pending free :type session. Please use it before redeeming another.', [
                        'type' => __($reward->session_type)
                    ]),
                ], 422);
            }
        }

        // Process redemption
        $result = $this->processRewardRedemption($user, $reward);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'reward' => [
                    'id' => $reward->id,
                    'name' => $this->getLocalizedText($reward->name),
                    'type' => $reward->type,
                    'points_needed' => $reward->points_needed,
                ],
                'redemption' => [
                    'id' => $result['redemption']->id,
                    'points_spent' => $reward->points_needed,
                    'remaining_points' => $user->fresh()->total_points,
                    'credit_balance' => $user->fresh()->credit_balance,
                    'free_session' => $result['free_session'] ?? false,
                    'session_type' => $result['session_type'] ?? null,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }

    /**
     * Process reward redemption
     */
    private function processRewardRedemption($user, $reward)
    {
        // Double check points before processing
        if ($user->total_points < $reward->points_needed) {
            return [
                'success' => false,
                'message' => __('Insufficient points.'),
            ];
        }

        // Create redemption record
        $redemption = RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_spent' => $reward->points_needed,
            'status' => 'pending',
            'redeemed_at' => now(),
        ]);

        // Deduct points
        $user->total_points -= $reward->points_needed;
        $user->save();

        // Get localized reward name for description
        $localizedRewardName = $this->getLocalizedText($reward->name);

        // Record point transaction
        PointTransaction::create([
            'user_id' => $user->id,
            'points' => -$reward->points_needed,
            'type' => 'redeemed',
            'source' => 'reward_' . $reward->type,
            'description' => $localizedRewardName,
            'reference_id' => $redemption->id,
            'reference_type' => RewardRedemption::class,
            'created_at' => now(),
        ]);

        // Process based on reward type
        $result = $this->processRewardByType($user, $reward, $redemption);

        if ($result['success']) {
            $redemption->markAsCompleted();

            // Send email notification
            if ($reward->type === 'credit') {
                EmailHelper::sendInUserLanguage($user, new RewardRedeemedMail([
                    'user' => $user,
                    'reward_name' => $localizedRewardName,
                    'reward_type' => $reward->type,
                    'points_spent' => $reward->points_needed,
                    'remaining_points' => $user->total_points,
                    'credit_amount' => $reward->credit_amount,
                    'redeemed_at' => now(),
                ]));
            } elseif ($reward->type === 'free_session') {
                EmailHelper::sendInUserLanguage($user, new FreeSessionRedeemedMail($user, $localizedRewardName, $reward->session_type, $reward->points_needed));
            } elseif ($reward->type === 'donate') {
                EmailHelper::sendInUserLanguage($user, new RewardRedeemedMail([
                    'user' => $user,
                    'reward_name' => $localizedRewardName,
                    'reward_type' => $reward->type,
                    'points_spent' => $reward->points_needed,
                    'remaining_points' => $user->total_points,
                    'redeemed_at' => now(),
                ]));
            }

            // Create in-app notification
            $this->createRewardNotification($user, $reward, $result);
        } else {
            // Refund points if failed
            $user->total_points += $reward->points_needed;
            $user->save();
            $redemption->markAsFailed($result['message']);

            // Reverse point transaction
            PointTransaction::create([
                'user_id' => $user->id,
                'points' => $reward->points_needed,
                'type' => 'earned',
                'source' => 'refund',
                'description' => __('Refund for failed redemption: ') . $localizedRewardName,
                'reference_id' => $redemption->id,
                'reference_type' => RewardRedemption::class,
                'created_at' => now(),
            ]);
        }

        return $result;
    }

    /**
     * Process reward based on type
     */
    private function processRewardByType($user, $reward, $redemption)
    {
        $localizedRewardName = $this->getLocalizedText($reward->name);

        switch ($reward->type) {
            case 'credit':
                // Add credit to user balance
                $user->credit_balance += $reward->credit_amount;
                $user->save();

                // Create credit transaction record
                CreditTransaction::create([
                    'donor_id' => $user->id,
                    'recipient_id' => $user->id,
                    'amount' => $reward->credit_amount,
                    'status' => 'allocated',
                    'description' => $localizedRewardName,
                    'created_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => __('You have successfully redeemed :reward_name! $:amount has been added to your credit balance.', [
                        'reward_name' => $localizedRewardName,
                        'amount' => number_format($reward->credit_amount, 2)
                    ]),
                    'redemption' => $redemption,
                    'free_session' => false,
                ];

            case 'free_session':
                $sessionTypeName = $reward->session_type == 'video' ? __('Video') : ($reward->session_type == 'audio' ? __('Audio') : __('Text'));

                return [
                    'success' => true,
                    'message' => __('You have successfully redeemed a free :session_type session! When booking your next :session_type session, it will be automatically marked as free.', [
                        'session_type' => $sessionTypeName
                    ]),
                    'redemption' => $redemption,
                    'free_session' => true,
                    'session_type' => $reward->session_type,
                ];

            case 'donate':
                // For donation, DON'T create credit transaction
                // Just record metadata
                $redemption->metadata = [
                    'donated_at' => now()->toDateTimeString(),
                    'message' => 'Points donated to help patients in need'
                ];
                $redemption->save();

                return [
                    'success' => true,
                    'message' => __('Thank you for donating :points points to help patients in need! Your generosity makes a difference.', [
                        'points' => number_format($reward->points_needed)
                    ]),
                    'redemption' => $redemption,
                    'free_session' => false,
                ];

            default:
                return [
                    'success' => false,
                    'message' => __('Invalid reward type.'),
                    'redemption' => $redemption,
                ];
        }
    }

    /**
     * Create in-app notification for reward redemption
     */
    private function createRewardNotification($user, $reward, $result)
    {
        $localizedRewardName = $this->getLocalizedText($reward->name);

        switch ($reward->type) {
            case 'credit':
                NotificationHelper::send(
                    $user->id,
                    __('Reward Redeemed! 🎉'),
                    __('You have successfully redeemed :reward_name! $:amount has been added to your credit balance.', [
                        'reward_name' => $localizedRewardName,
                        'amount' => number_format($reward->credit_amount, 2)
                    ]),
                    'points_earned'
                );
                break;
            case 'free_session':
                $sessionTypeName = $reward->session_type == 'video' ? __('Video') : ($reward->session_type == 'audio' ? __('Audio') : __('Text'));
                NotificationHelper::send(
                    $user->id,
                    __('Free Session Unlocked! 🎁'),
                    __('You have unlocked a free :session_type session! Book it now before it expires.', [
                        'session_type' => $sessionTypeName
                    ]),
                    'points_earned'
                );
                break;
            case 'donate':
                NotificationHelper::send(
                    $user->id,
                    __('Points Donated! 💝'),
                    __('Thank you for donating :points points to help patients in need. Your generosity is appreciated!', [
                        'points' => number_format($reward->points_needed)
                    ]),
                    'points_earned'
                );
                break;
        }
    }

    /**
     * Get user's redemption history via AJAX
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $redemptions = RewardRedemption::where('user_id', $user->id)
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redemptions' => $redemptions->map(function ($redemption) {
                    $rewardName = $this->getLocalizedText($redemption->reward->name);
                    $pointsSpent = '-' . number_format($redemption->points_spent);

                    return [
                        'id' => $redemption->id,
                        'reward_name' => $rewardName,
                        'reward_type' => $redemption->reward->type,
                        'points_spent_formatted' => $pointsSpent,
                        'status' => $redemption->status,
                        'status_text' => $this->getStatusText($redemption->status),
                        'redeemed_at' => $redemption->redeemed_at->translatedFormat('M d, Y'),
                    ];
                }),
                'pagination' => [
                    'current_page' => $redemptions->currentPage(),
                    'last_page' => $redemptions->lastPage(),
                    'total' => $redemptions->total(),
                    'per_page' => $redemptions->perPage(),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'redemptions' => $redemptions,
        ]);
    }

    /**
     * Get points history via AJAX
     */
    public function pointsHistory(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->get('per_page', 20);
        $transactions = PointTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'transactions' => $transactions->map(function ($transaction) {
                $description = $transaction->description;
                if ($description && $this->isJson($description)) {
                    $decoded = json_decode($description, true);
                    $locale = app()->getLocale();
                    $description = $decoded[$locale] ?? $decoded['en'] ?? $description;
                }
                $description = str_replace('Redeemed: ', '', $description);

                $pointsFormatted = $transaction->points > 0
                    ? '+' . number_format($transaction->points)
                    : '-' . number_format(abs($transaction->points));

                return [
                    'id' => $transaction->id,
                    'points' => $transaction->points,
                    'points_formatted' => $pointsFormatted,
                    'points_class' => $transaction->points > 0 ? 'points-positive' : 'points-negative',
                    'type' => $transaction->type,
                    'type_text' => $transaction->type === 'earned' ? __('Earned') : __('Redeemed'),
                    'source' => $transaction->source,
                    'source_name' => RewardHelper::getSourceName($transaction->source),
                    'icon' => RewardHelper::getSourceIcon($transaction->source),
                    'color' => RewardHelper::getSourceColor($transaction->source),
                    'description' => $description,
                    'created_at' => $transaction->created_at,
                    'created_at_formatted' => $transaction->created_at->translatedFormat('M d, Y'),
                ];
            }),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
            ],
        ]);
    }

    /**
     * Cancel a pending redemption via AJAX
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'redemption_id' => 'required|exists:reward_redemptions,id',
        ]);

        $redemption = RewardRedemption::where('id', $request->redemption_id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$redemption) {
            return response()->json([
                'success' => false,
                'message' => __('Redemption not found or cannot be cancelled.'),
            ], 422);
        }

        $user = Auth::user();
        $reward = $redemption->reward;
        $localizedRewardName = $this->getLocalizedText($reward->name);

        // Refund points
        $user->total_points += $redemption->points_spent;
        $user->save();

        // Record refund transaction
        PointTransaction::create([
            'user_id' => $user->id,
            'points' => $redemption->points_spent,
            'type' => 'earned',
            'source' => 'refund',
            'description' => __('Refund for cancelled redemption: ') . $localizedRewardName,
            'reference_id' => $redemption->id,
            'reference_type' => RewardRedemption::class,
            'created_at' => now(),
        ]);

        $redemption->cancel();

        NotificationHelper::send(
            $user->id,
            __('Redemption Cancelled'),
            __('Your redemption of :reward_name has been cancelled. :points points have been refunded to your account.', [
                'reward_name' => $localizedRewardName,
                'points' => number_format($redemption->points_spent)
            ]),
            'points_earned'
        );

        return response()->json([
            'success' => true,
            'message' => __('Redemption cancelled and :points points refunded.', ['points' => number_format($redemption->points_spent)]),
            'remaining_points' => $user->total_points,
        ]);
    }

    /**
     * Get user statistics via AJAX
     */
    public function getStats()
    {
        $user = Auth::user();
        $rank = $user->getRank();

        $totalEarned = PointTransaction::where('user_id', $user->id)
            ->where('type', 'earned')
            ->where('points', '>', 0)
            ->sum('points');

        $totalRedeemed = PointTransaction::where('user_id', $user->id)
            ->where('type', 'redeemed')
            ->where('points', '<', 0)
            ->sum('points');

        return response()->json([
            'success' => true,
            'stats' => [
                'total_points' => number_format($user->total_points),
                'credit_balance' => number_format($user->credit_balance, 2),
                'rank' => [
                    'name' => $rank['name'],
                    'name_ar' => $rank['name_ar'],
                    'icon' => $rank['icon'],
                    'color' => $rank['color'],
                ],
                'next_milestone' => $user->getNextMilestone(),
                'total_earned' => number_format($totalEarned),
                'total_redeemed' => number_format(abs($totalRedeemed)),
                'redemptions_count' => RewardRedemption::where('user_id', $user->id)->where('status', 'completed')->count(),
                'days_active' => $user->created_at->diffInDays(now()),
                'pending_free_sessions' => RewardRedemption::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->whereHas('reward', function ($q) {
                        $q->where('type', 'free_session');
                    })
                    ->whereDoesntHave('therapySession')
                    ->count(),
            ],
        ]);
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
     * Get localized text from JSON
     */
    private function getLocalizedText($data)
    {
        if (!$data)
            return '';

        if (is_string($data) && $this->isJson($data)) {
            $decoded = json_decode($data, true);
            $locale = app()->getLocale();
            return $decoded[$locale] ?? $decoded['en'] ?? $data;
        }

        if (is_array($data)) {
            $locale = app()->getLocale();
            return $data[$locale] ?? $data['en'] ?? '';
        }

        return $data;
    }

    /**
     * Get status text
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => __('Pending'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
            'failed' => __('Failed'),
        ];
        return $statuses[$status] ?? $status;
    }
}