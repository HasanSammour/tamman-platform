<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\TreatmentPlan;
use App\Models\TreatmentTask;
use App\Models\PointTransaction;
use App\Models\Notification;
use App\Mail\TaskCompletedMail;
use App\Mail\PlanCompletedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TreatmentPlanController extends Controller
{
    /**
     * Display patient's treatment plans
     */
    public function index()
    {
        $user = Auth::user();

        // Auto-update any plans that should be completed (all tasks done)
        $userPlans = TreatmentPlan::where('patient_id', $user->id)->where('status', 'active')->get();
        foreach ($userPlans as $plan) {
            $totalTasks = $plan->tasks->count();
            $completedTasks = $plan->tasks->where('is_completed', true)->count();
            if ($totalTasks > 0 && $completedTasks === $totalTasks && $plan->status === 'active') {
                $plan->status = 'completed';
                $plan->end_date = now();
                $plan->save();
            }
        }

        // Active plans (only active status, NOT cancelled)
        $activePlans = TreatmentPlan::where('patient_id', $user->id)
            ->where('status', 'active')
            ->with(['tasks', 'specialist'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate progress for each active plan
        foreach ($activePlans as $plan) {
            $totalTasks = $plan->tasks->count();
            $completedTasks = $plan->tasks->where('is_completed', true)->count();
            $plan->progress_percentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            $plan->completed_tasks = $completedTasks;
            $plan->total_tasks = $totalTasks;
        }

        // Completed plans (only completed status, NOT cancelled)
        $completedPlans = TreatmentPlan::where('patient_id', $user->id)
            ->where('status', 'completed')
            ->with(['tasks', 'specialist'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Calculate total tasks and completed tasks from active + completed plans ONLY (exclude cancelled)
        $allActiveCompletedPlans = TreatmentPlan::where('patient_id', $user->id)
            ->whereIn('status', ['active', 'completed'])
            ->pluck('id');

        $totalTasks = TreatmentTask::whereIn('plan_id', $allActiveCompletedPlans)->count();
        $completedTasks = TreatmentTask::whereIn('plan_id', $allActiveCompletedPlans)
            ->where('is_completed', true)
            ->count();
        $totalPointsEarned = PointTransaction::where('user_id', $user->id)
            ->where('source', 'task_completed')
            ->sum('points');

        // Statistics
        $stats = [
            'total_plans' => TreatmentPlan::where('patient_id', $user->id)->whereIn('status', ['active', 'completed'])->count(),
            'active_plans' => $activePlans->count(),
            'completed_plans' => $completedPlans->count(),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'total_points_earned' => $totalPointsEarned,
        ];

        return view('patient.treatment-plan.index', compact('activePlans', 'completedPlans', 'stats'));
    }

    /**
     * Show single treatment plan details
     */
    public function show($id)
    {
        $plan = TreatmentPlan::where('id', $id)
            ->where('patient_id', Auth::id())
            ->whereIn('status', ['active', 'completed'])
            ->with(['tasks', 'specialist', 'specialist.specialistProfile'])
            ->firstOrFail();

        // Calculate progress
        $totalTasks = $plan->tasks->count();
        $completedTasks = $plan->tasks->where('is_completed', true)->count();
        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Group tasks by status
        $pendingTasks = $plan->tasks->where('is_completed', false);
        $completedTasksList = $plan->tasks->where('is_completed', true);

        $progress = [
            'total' => $totalTasks,
            'completed' => $completedTasks,
            'percentage' => $progressPercentage,
        ];

        return view('patient.treatment-plan.show', compact('plan', 'progress', 'pendingTasks', 'completedTasksList'));
    }

    /**
     * Complete a task via AJAX
     */
    public function completeTask(Request $request, $taskId)
    {
        try {
            $task = TreatmentTask::where('id', $taskId)
                ->whereHas('plan', function ($q) {
                    $q->where('patient_id', Auth::id())
                        ->whereIn('status', ['active', 'completed']);
                })
                ->with(['plan', 'plan.specialist'])
                ->firstOrFail();

            // Check if task already completed
            if ($task->is_completed) {
                return response()->json([
                    'success' => false,
                    'message' => __('This task has already been completed.')
                ], 422);
            }

            // Check if plan is still active (not cancelled)
            if ($task->plan->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => __('This treatment plan is no longer active.')
                ], 422);
            }

            // Mark task as completed
            $task->is_completed = true;
            $task->completed_at = now();
            $task->save();

            // Award points for task completion
            $points = $task->points_reward ?? 10;
            Auth::user()->addPoints(
                $points,
                'task_completed',
                __('Completed task: :task', ['task' => $task->title]),
                $task->id,
                TreatmentTask::class
            );

            // Send email for task completion (if user wants notifications)
            if (Auth::user()->wantsNotification('points_earned')) {
                try {
                    Mail::to(Auth::user()->email)->send(new TaskCompletedMail(Auth::user(), $task, $points));
                } catch (\Exception $e) {
                    Log::error('Failed to send task completion email: ' . $e->getMessage());
                }
            }

            // Create in-app notification for task completion
            if (Auth::user()->wantsNotification('points_earned')) {
                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => __('Task Completed! 🎯'),
                    'message' => __('You earned :points points for completing ":task"', [
                        'points' => $points,
                        'task' => $task->title
                    ]),
                    'type' => 'points_earned',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }

            // Check if all tasks in plan are completed
            $plan = $task->plan;
            $totalTasks = $plan->tasks->count();
            $completedTasks = $plan->tasks->where('is_completed', true)->count();
            $allCompleted = $completedTasks === $totalTasks;

            $planCompleted = false;
            $bonusPoints = 0;

            if ($allCompleted && $plan->status === 'active') {
                $plan->status = 'completed';
                $plan->end_date = now();
                $plan->save();
                $planCompleted = true;

                // Bonus points for completing entire plan
                $bonusPoints = 50;
                Auth::user()->addPoints(
                    $bonusPoints,
                    'task_completed',
                    __('Completed treatment plan: :plan', ['plan' => $plan->title]),
                    $plan->id,
                    TreatmentPlan::class
                );

                // Send email for plan completion
                try {
                    Mail::to(Auth::user()->email)->send(new PlanCompletedMail(Auth::user(), $plan, $bonusPoints));
                } catch (\Exception $e) {
                    Log::error('Failed to send plan completion email: ' . $e->getMessage());
                }

                // Create in-app notification for plan completion
                if (Auth::user()->wantsNotification('points_earned')) {
                    Notification::create([
                        'user_id' => Auth::id(),
                        'title' => __('Treatment Plan Completed! 🏆'),
                        'message' => __('Congratulations! You completed the ":plan" treatment plan and earned :points bonus points!', [
                            'plan' => $plan->title,
                            'points' => $bonusPoints
                        ]),
                        'type' => 'points_earned',
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                }

                // Notify specialist that patient completed the plan
                if ($plan->specialist && $plan->specialist->wantsNotification('treatment_tasks')) {
                    Notification::create([
                        'user_id' => $plan->specialist_id,
                        'title' => __('Patient Completed Treatment Plan! 🎉'),
                        'message' => __(':patient has completed the treatment plan ":plan".', [
                            'patient' => Auth::user()->name,
                            'plan' => $plan->title
                        ]),
                        'type' => 'treatment_tasks',
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                }
            }

            // Calculate updated progress
            $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            return response()->json([
                'success' => true,
                'message' => $planCompleted
                    ? __('Task completed! You earned :points points + :bonus bonus points for completing the plan!', ['points' => $points, 'bonus' => $bonusPoints])
                    : __('Task completed! You earned :points points.', ['points' => $points]),
                'points_earned' => $points,
                'bonus_earned' => $bonusPoints,
                'plan_completed' => $planCompleted,
                'progress' => [
                    'completed' => $completedTasks,
                    'total' => $totalTasks,
                    'percentage' => $progressPercentage,
                ],
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'completed' => true,
                    'completed_at' => $task->completed_at->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Task completion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again.')
            ], 500);
        }
    }

    /**
     * Show completed plans history with AJAX pagination
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $completedPlans = TreatmentPlan::where('patient_id', $user->id)
            ->where('status', 'completed')
            ->with(['specialist', 'tasks'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // Calculate completion data for each plan
        foreach ($completedPlans as $plan) {
            $totalTasks = $plan->tasks->count();
            $completedTasks = $plan->tasks->where('is_completed', true)->count();
            $plan->completion_percentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 100;
            $plan->total_points_earned = PointTransaction::where('user_id', $user->id)
                ->where('source', 'task_completed')
                ->where('reference_id', $plan->id)
                ->sum('points');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('patient.treatment-plan.partials.history_table', compact('completedPlans'))->render(),
                'pagination' => $completedPlans->links()->render(),
            ]);
        }

        return view('patient.treatment-plan.history', compact('completedPlans'));
    }

    /**
     * Get plan progress via AJAX (for dashboard updates)
     */
    public function getProgress($planId)
    {
        $plan = TreatmentPlan::where('id', $planId)
            ->where('patient_id', Auth::id())
            ->whereIn('status', ['active', 'completed'])
            ->with('tasks')
            ->firstOrFail();

        $totalTasks = $plan->tasks->count();
        $completedTasks = $plan->tasks->where('is_completed', true)->count();
        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return response()->json([
            'success' => true,
            'progress' => [
                'completed' => $completedTasks,
                'total' => $totalTasks,
                'percentage' => $progressPercentage,
            ],
            'status' => $plan->status,
        ]);
    }
}