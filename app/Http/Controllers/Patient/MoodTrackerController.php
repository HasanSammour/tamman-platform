<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MoodLog;
use App\Models\PointTransaction;
use App\Models\Notification;
use App\Helpers\MoodHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MoodTrackerController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $moodLogs = MoodLog::where('user_id', $user->id)
            ->orderBy('log_date', 'desc')
            ->paginate(15);

        $currentStreak = $this->getCurrentStreak($user->id);
        $longestStreak = $this->getLongestStreak($user->id);
        $stats = $this->getMoodStats($user->id);
        $chartData = $this->getChartData($user->id);

        $todayLog = MoodLog::where('user_id', $user->id)
            ->whereDate('log_date', Carbon::today())
            ->first();

        // Check if user already earned points for mood tracking today (even if log was deleted)
        $hasEarnedPointsToday = PointTransaction::where('user_id', $user->id)
            ->where('source', 'mood_tracking')
            ->whereDate('created_at', Carbon::today())
            ->exists();

        $weeklyAverages = $this->getWeeklyAverages($user->id);
        foreach ($weeklyAverages as &$day) {
            $day['color'] = MoodHelper::getColor($day['average']);
            $day['emoji'] = MoodHelper::getEmoji($day['average']);
        }

        $monthlyAverages = $this->getMonthlyAverages($user->id);

        return view('patient.mood-tracker', compact(
            'moodLogs',
            'currentStreak',
            'longestStreak',
            'stats',
            'chartData',
            'todayLog',
            'weeklyAverages',
            'monthlyAverages',
            'hasEarnedPointsToday'
        ));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'mood_value' => 'required|integer|min:1|max:10',
                'mood_label' => 'required|string|max:50',
                'notes' => 'nullable|string|max:500',
            ]);

            $user = Auth::user();

            // Check if already logged today
            $existingLog = MoodLog::where('user_id', $user->id)
                ->whereDate('log_date', Carbon::today())
                ->first();

            if ($existingLog) {
                return response()->json([
                    'success' => false,
                    'message' => __('You have already logged your mood today. You can edit your entry below.')
                ], 422);
            }

            // Check if already earned points today (prevents abuse)
            $hasEarnedPointsToday = PointTransaction::where('user_id', $user->id)
                ->where('source', 'mood_tracking')
                ->whereDate('created_at', Carbon::today())
                ->exists();

            // Create mood log
            $moodLog = MoodLog::create([
                'user_id' => $user->id,
                'mood_value' => $request->mood_value,
                'mood_label' => $request->mood_label,
                'notes' => $request->notes,
                'log_date' => Carbon::today(),
            ]);

            $pointsEarned = 0;
            $bonusEarned = 0;

            // Award points ONLY if not already earned today
            if (!$hasEarnedPointsToday) {
                $pointsEarned = 5;
                $user->addPoints(
                    $pointsEarned,
                    'mood_tracking',
                    __('Daily mood check-in'),
                    $moodLog->id,
                    MoodLog::class
                );

                // Send notification for regular mood points if user wants it
                if ($user->wantsNotification('points_earned')) {
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => __('Points Earned'),
                        'message' => __('You earned :points points for tracking your mood today!', ['points' => $pointsEarned]),
                        'type' => 'points_earned',
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                }

                // Check streak bonus
                $currentStreak = $this->getCurrentStreak($user->id);
                if (in_array($currentStreak, [7, 14, 21, 30])) {
                    $bonusEarned = $currentStreak == 30 ? 100 : 50;
                    $user->addPoints(
                        $bonusEarned,
                        'streak_bonus',
                        __('Streak milestone! :days days in a row', ['days' => $currentStreak]),
                        $moodLog->id,
                        MoodLog::class
                    );

                    // Only create streak notification if user wants points notifications
                    if ($user->wantsNotification('points_earned')) {
                        Notification::create([
                            'user_id' => $user->id,
                            'title' => __('Amazing Streak! 🔥'),
                            'message' => __('You\'ve logged your mood for :days days in a row! You earned :points bonus points!', ['days' => $currentStreak, 'points' => $bonusEarned]),
                            'type' => 'points_earned',
                            'is_read' => false,
                            'sent_at' => now(),
                        ]);
                    }
                }
            }

            $updatedStats = $this->getMoodStats($user->id);
            $updatedStreak = $this->getCurrentStreak($user->id);
            $updatedChartData = $this->getChartData($user->id);
            $updatedWeeklyAverages = $this->getWeeklyAverages($user->id);

            foreach ($updatedWeeklyAverages as &$day) {
                $day['color'] = MoodHelper::getColor($day['average']);
            }

            $message = $hasEarnedPointsToday
                ? __('Mood logged successfully!')
                : ($bonusEarned > 0
                    ? __('Mood logged successfully! You earned :points points + :bonus bonus points!', ['points' => $pointsEarned, 'bonus' => $bonusEarned])
                    : __('Mood logged successfully! You earned :points Tamman Points.', ['points' => $pointsEarned]));

            return response()->json([
                'success' => true,
                'message' => $message,
                'points_earned' => $pointsEarned,
                'bonus_earned' => $bonusEarned,
                'has_earned_points' => $hasEarnedPointsToday,
                'mood_log' => [
                    'id' => $moodLog->id,
                    'mood_value' => $moodLog->mood_value,
                    'mood_label' => $moodLog->mood_label,
                    'mood_emoji' => MoodHelper::getEmoji($moodLog->mood_value),
                    'mood_color' => MoodHelper::getColor($moodLog->mood_value),
                    'mood_name' => app()->getLocale() === 'ar' ? MoodHelper::getLabelAr($moodLog->mood_value) : MoodHelper::getLabel($moodLog->mood_value),
                    'notes' => $moodLog->notes,
                    'date' => $moodLog->log_date->translatedFormat('l, M d, Y'),
                    'formatted_value' => $moodLog->mood_value . '/10',
                ],
                'stats' => $updatedStats,
                'streak' => $updatedStreak,
                'chart_data' => $updatedChartData,
                'weekly_averages' => $updatedWeeklyAverages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again.')
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'mood_value' => 'required|integer|min:1|max:10',
                'mood_label' => 'required|string|max:50',
                'notes' => 'nullable|string|max:500',
            ]);

            $moodLog = MoodLog::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $moodLog->update([
                'mood_value' => $request->mood_value,
                'mood_label' => $request->mood_label,
                'notes' => $request->notes,
            ]);

            $user = Auth::user();
            $updatedStats = $this->getMoodStats($user->id);
            $updatedChartData = $this->getChartData($user->id);
            $updatedWeeklyAverages = $this->getWeeklyAverages($user->id);

            foreach ($updatedWeeklyAverages as &$day) {
                $day['color'] = MoodHelper::getColor($day['average']);
            }

            return response()->json([
                'success' => true,
                'message' => __('Mood updated successfully!'),
                'mood_log' => [
                    'id' => $moodLog->id,
                    'mood_value' => $moodLog->mood_value,
                    'mood_label' => $moodLog->mood_label,
                    'mood_emoji' => MoodHelper::getEmoji($moodLog->mood_value),
                    'mood_color' => MoodHelper::getColor($moodLog->mood_value),
                    'mood_name' => app()->getLocale() === 'ar' ? MoodHelper::getLabelAr($moodLog->mood_value) : MoodHelper::getLabel($moodLog->mood_value),
                    'notes' => $moodLog->notes,
                    'formatted_value' => $moodLog->mood_value . '/10',
                ],
                'stats' => $updatedStats,
                'chart_data' => $updatedChartData,
                'weekly_averages' => $updatedWeeklyAverages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again.')
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $moodLog = MoodLog::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // IMPORTANT: DO NOT delete point transactions
            // This prevents abuse where users delete and re-add mood to get points again
            $moodLog->delete();

            $user = Auth::user();
            $updatedStats = $this->getMoodStats($user->id);
            $updatedStreak = $this->getCurrentStreak($user->id);
            $updatedChartData = $this->getChartData($user->id);
            $updatedWeeklyAverages = $this->getWeeklyAverages($user->id);

            foreach ($updatedWeeklyAverages as &$day) {
                $day['color'] = MoodHelper::getColor($day['average']);
            }

            return response()->json([
                'success' => true,
                'message' => __('Mood entry deleted successfully.'),
                'stats' => $updatedStats,
                'streak' => $updatedStreak,
                'chart_data' => $updatedChartData,
                'weekly_averages' => $updatedWeeklyAverages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again.')
            ], 500);
        }
    }

    public function history()
    {
        $user = Auth::user();

        $logs = MoodLog::where('user_id', $user->id)
            ->orderBy('log_date', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'date' => $log->log_date->format('Y-m-d'),
                    'formatted_date' => $log->log_date->translatedFormat('M d, Y'),
                    'mood_value' => $log->mood_value,
                    'mood_label' => $log->mood_label,
                    'notes' => $log->notes,
                    'mood_icon' => MoodHelper::getEmoji($log->mood_value),
                    'mood_color' => MoodHelper::getColor($log->mood_value),
                    'mood_name' => app()->getLocale() === 'ar' ? MoodHelper::getLabelAr($log->mood_value) : MoodHelper::getLabel($log->mood_value),
                ];
            });

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'streak' => $this->getCurrentStreak($user->id),
            'total_entries' => $logs->count(),
        ]);
    }

    private function getCurrentStreak($userId)
    {
        $moods = MoodLog::where('user_id', $userId)
            ->orderBy('log_date', 'desc')
            ->pluck('log_date');

        $streak = 0;
        $currentDate = Carbon::today();

        foreach ($moods as $moodDate) {
            if ($moodDate->eq($currentDate) || $moodDate->eq($currentDate->copy()->subDays($streak))) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    private function getLongestStreak($userId)
    {
        $moods = MoodLog::where('user_id', $userId)
            ->orderBy('log_date', 'asc')
            ->pluck('log_date');

        $longest = 0;
        $current = 0;
        $lastDate = null;

        foreach ($moods as $moodDate) {
            if ($lastDate && $moodDate->diffInDays($lastDate) == 1) {
                $current++;
            } else {
                $current = 1;
            }

            if ($current > $longest) {
                $longest = $current;
            }

            $lastDate = $moodDate;
        }

        return $longest;
    }

    private function getMoodStats($userId)
    {
        $logs = MoodLog::where('user_id', $userId)->get();

        if ($logs->isEmpty()) {
            return ['average' => 0, 'highest' => 0, 'lowest' => 0, 'total' => 0];
        }

        return [
            'average' => round($logs->avg('mood_value'), 1),
            'highest' => $logs->max('mood_value'),
            'lowest' => $logs->min('mood_value'),
            'total' => $logs->count(),
        ];
    }

    private function getChartData($userId)
    {
        $labels = [];
        $values = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->translatedFormat('M d');

            $log = MoodLog::where('user_id', $userId)
                ->whereDate('log_date', $date)
                ->first();

            $values[] = $log ? $log->mood_value : null;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getWeeklyAverages($userId)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $averages = [];

        foreach ($days as $index => $day) {
            $avg = MoodLog::where('user_id', $userId)
                ->whereRaw('DAYOFWEEK(log_date) = ?', [$index + 1])
                ->avg('mood_value');

            $averages[] = [
                'day' => __($day),
                'day_index' => $index,
                'average' => round($avg ?? 0, 1),
            ];
        }

        return $averages;
    }

    private function getMonthlyAverages($userId)
    {
        $months = [];
        $averages = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->translatedFormat('M Y');

            $avg = MoodLog::where('user_id', $userId)
                ->whereBetween('log_date', [$monthStart, $monthEnd])
                ->avg('mood_value');

            $averages[] = round($avg ?? 0, 1);
        }

        return ['months' => $months, 'averages' => $averages];
    }
}