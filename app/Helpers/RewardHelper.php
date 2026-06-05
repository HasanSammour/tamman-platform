<?php

namespace App\Helpers;

use App\Models\Reward;
use App\Models\RewardRedemption;

class RewardHelper
{
    /**
     * Get points badge color based on amount
     */
    public static function getPointsBadgeColor($points)
    {
        if ($points >= 5000)
            return '#eab308';
        if ($points >= 2000)
            return '#fbbf24';
        if ($points >= 1000)
            return '#9ca3af';
        if ($points >= 500)
            return '#cd7f32';
        if ($points >= 200)
            return '#7c3aed';
        if ($points >= 100)
            return '#f59e0b';
        if ($points >= 50)
            return '#10b981';
        return '#6b7280';
    }

    /**
     * Get source icon
     */
    public static function getSourceIcon($source)
    {
        $icons = [
            'mood_tracking' => 'fa-smile',
            'session_attendance' => 'fa-calendar-check',
            'test_completed' => 'fa-clipboard-list',
            'task_completed' => 'fa-check-circle',
            'referral' => 'fa-users',
            'specialist_rating' => 'fa-star',
            'streak_bonus' => 'fa-fire',
            'booking' => 'fa-calendar-plus',
            'donation' => 'fa-hand-holding-heart',
            'reward_credit' => 'fa-coins',
            'reward_free_session' => 'fa-gift',
            'reward_donate' => 'fa-hand-holding-heart',
            'redeemed' => 'fa-ticket-alt',
        ];
        return $icons[$source] ?? 'fa-coins';
    }

    /**
     * Get source color
     */
    public static function getSourceColor($source)
    {
        $colors = [
            'mood_tracking' => '#10b981',
            'session_attendance' => '#7c3aed',
            'test_completed' => '#f59e0b',
            'task_completed' => '#06b6d4',
            'referral' => '#ec4899',
            'specialist_rating' => '#fbbf24',
            'streak_bonus' => '#ef4444',
            'booking' => '#8b5cf6',
            'donation' => '#f97316',
            'reward_credit' => '#10b981',
            'reward_free_session' => '#7c3aed',
            'reward_donate' => '#ec4899',
            'redeemed' => '#ef4444',
        ];
        return $colors[$source] ?? '#6b7280';
    }

    /**
     * Get source name in Arabic
     */
    public static function getSourceNameAr($source)
    {
        $names = [
            'mood_tracking' => 'تتبع المزاج',
            'session_attendance' => 'حضور جلسة',
            'test_completed' => 'إكمال اختبار',
            'task_completed' => 'إكمال مهمة',
            'referral' => 'دعوة صديق',
            'specialist_rating' => 'تقييم مختص',
            'streak_bonus' => 'مكافأة الاستمرارية',
            'booking' => 'حجز جلسة',
            'donation' => 'تبرع',
            'reward_credit' => 'رصيد مضافة',
            'reward_free_session' => 'جلسة مجانية',
            'reward_donate' => 'تبرع بالنقاط',
            'redeemed' => 'استبدال نقاط',
        ];
        return $names[$source] ?? $source;
    }

    /**
     * Get source name in English
     */
    public static function getSourceNameEn($source)
    {
        $names = [
            'mood_tracking' => 'Mood Tracking',
            'session_attendance' => 'Session Attendance',
            'test_completed' => 'Test Completed',
            'task_completed' => 'Task Completed',
            'referral' => 'Friend Referral',
            'specialist_rating' => 'Specialist Rating',
            'streak_bonus' => 'Streak Bonus',
            'booking' => 'Session Booking',
            'donation' => 'Donation',
            'reward_credit' => 'Credit Reward',
            'reward_free_session' => 'Free Session',
            'reward_donate' => 'Points Donation',
            'redeemed' => 'Points Redeemed',
        ];
        return $names[$source] ?? $source;
    }

    /**
     * Get source name based on locale
     */
    public static function getSourceName($source)
    {
        if (app()->getLocale() === 'ar') {
            return self::getSourceNameAr($source);
        }
        return self::getSourceNameEn($source);
    }

    /**
     * Get next milestone information
     */
    public static function getNextMilestone($currentPoints)
    {
        $milestones = [100, 250, 500, 1000, 2500, 5000, 10000];
        foreach ($milestones as $milestone) {
            if ($currentPoints < $milestone) {
                $progress = ($currentPoints / $milestone) * 100;
                return [
                    'current' => $currentPoints,
                    'next' => $milestone,
                    'needed' => $milestone - $currentPoints,
                    'progress' => min(100, $progress),
                    'percentage' => round($progress, 1),
                ];
            }
        }
        return null;
    }

    /**
     * Get rank based on points
     */
    public static function getRank($points)
    {
        if ($points >= 10000)
            return ['name' => 'Legend', 'name_ar' => 'أسطوري', 'icon' => 'fa-crown', 'color' => '#eab308', 'level' => 6];
        if ($points >= 5000)
            return ['name' => 'Platinum', 'name_ar' => 'بلاتينيوم', 'icon' => 'fa-crown', 'color' => '#94a3b8', 'level' => 5];
        if ($points >= 2000)
            return ['name' => 'Gold', 'name_ar' => 'ذهبي', 'icon' => 'fa-medal', 'color' => '#fbbf24', 'level' => 4];
        if ($points >= 1000)
            return ['name' => 'Silver', 'name_ar' => 'فضي', 'icon' => 'fa-medal', 'color' => '#9ca3af', 'level' => 3];
        if ($points >= 500)
            return ['name' => 'Bronze', 'name_ar' => 'برونزي', 'icon' => 'fa-medal', 'color' => '#cd7f32', 'level' => 2];
        return ['name' => 'Starter', 'name_ar' => 'مبتدئ', 'icon' => 'fa-seedling', 'color' => '#10b981', 'level' => 1];
    }

    /**
     * Get reward type name
     */
    public static function getRewardTypeName($type)
    {
        $names = [
            'credit' => ['en' => 'Credit', 'ar' => 'رصيد'],
            'free_session' => ['en' => 'Free Session', 'ar' => 'جلسة مجانية'],
            'donate' => ['en' => 'Donate', 'ar' => 'تبرع'],
        ];

        $locale = app()->getLocale();
        return $names[$type][$locale] ?? $type;
    }

    /**
     * Process reward redemption
     */
    public static function processRedemption($user, $rewardId)
    {
        $reward = Reward::findOrFail($rewardId);

        // Check if user has enough points
        if (!$user->canRedeemReward($reward->points_needed)) {
            return [
                'success' => false,
                'message' => __('You do not have enough points to redeem this reward.'),
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

        // Record point transaction
        $user->addPoints(
            -$reward->points_needed,
            'reward_' . $reward->type,
            __('Redeemed: ') . $reward->getName(),
            $redemption->id,
            RewardRedemption::class
        );

        // Process based on reward type
        $result = self::processRewardType($user, $reward, $redemption);

        if ($result['success']) {
            $redemption->markAsCompleted();
        } else {
            // Refund points if failed
            $user->total_points += $reward->points_needed;
            $user->save();
            $redemption->markAsFailed($result['message']);
        }

        return $result;
    }

    /**
     * Process reward based on type
     */
    private static function processRewardType($user, $reward, $redemption)
    {
        switch ($reward->type) {
            case 'credit':
                $user->credit_balance += $reward->credit_amount;
                $user->save();

                \App\Models\CreditTransaction::create([
                    'donor_id' => $user->id,
                    'recipient_id' => $user->id,
                    'amount' => $reward->credit_amount,
                    'status' => 'allocated',
                    'description' => 'مكافأة من استبدال النقاط: ' . $reward->getName(),
                ]);

                return [
                    'success' => true,
                    'message' => __('You have successfully redeemed :reward_name! $:amount has been added to your credit balance.', [
                        'reward_name' => $reward->getName(),
                        'amount' => $reward->credit_amount
                    ]),
                    'redemption' => $redemption,
                ];

            case 'free_session':
                return [
                    'success' => true,
                    'message' => __('You have successfully redeemed a free :session_type session! Please book a session and it will be marked as free.', [
                        'session_type' => __($reward->session_type)
                    ]),
                    'redemption' => $redemption,
                    'free_session' => true,
                    'session_type' => $reward->session_type,
                ];

            case 'donate':
                return [
                    'success' => true,
                    'message' => __('Thank you for donating your points to help others!'),
                    'redemption' => $redemption,
                ];

            default:
                return [
                    'success' => false,
                    'message' => __('Invalid reward type.'),
                ];
        }
    }
}