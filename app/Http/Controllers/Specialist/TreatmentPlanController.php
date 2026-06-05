<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TreatmentPlan;
use App\Models\TreatmentTask;
use App\Models\Notification;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TreatmentPlanController extends Controller
{
    /**
     * Display a listing of treatment plans.
     */
    public function index()
    {
        $specialistId = Auth::id();

        // Statistics
        $stats = [
            'total' => TreatmentPlan::where('specialist_id', $specialistId)->count(),
            'active' => TreatmentPlan::where('specialist_id', $specialistId)->where('status', 'active')->count(),
            'completed' => TreatmentPlan::where('specialist_id', $specialistId)->where('status', 'completed')->count(),
            'cancelled' => TreatmentPlan::where('specialist_id', $specialistId)->where('status', 'cancelled')->count(),
        ];

        // Get recent plans for quick view
        $recentPlans = TreatmentPlan::where('specialist_id', $specialistId)
            ->with(['patient', 'tasks'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('specialist.treatment-plans.index', compact('stats', 'recentPlans'));
    }

    /**
     * Get plans data for DataTable (AJAX)
     */
    public function getPlansData(Request $request)
    {
        $specialistId = Auth::id();

        $query = TreatmentPlan::where('specialist_id', $specialistId)
            ->with(['patient', 'tasks'])
            ->select('treatment_plans.*');

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by patient
        if ($request->has('patient_id') && $request->patient_id !== 'all') {
            $query->where('patient_id', $request->patient_id);
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $plans = $query->paginate($perPage);

        // Add calculated data
        $plans->getCollection()->transform(function ($plan) {
            $completedTasks = $plan->tasks->where('is_completed', true)->count();
            $totalTasks = $plan->tasks->count();

            $plan->patient_name = $plan->patient->name;
            $plan->progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            $plan->tasks_count = $totalTasks;
            $plan->completed_tasks = $completedTasks;
            $plan->status_badge = $this->getStatusBadge($plan->status);
            $plan->progress_color = $this->getProgressColor($plan->progress);

            return $plan;
        });

        return response()->json([
            'success' => true,
            'data' => $plans->items(),
            'total' => $plans->total(),
            'per_page' => $plans->perPage(),
            'current_page' => $plans->currentPage(),
            'last_page' => $plans->lastPage(),
        ]);
    }

    /**
     * Get patients for dropdown (AJAX)
     */
    public function getPatients(Request $request)
    {
        $specialistId = Auth::id();

        // Get all unique patients who have sessions with this specialist
        $patientIds = \App\Models\TherapySession::where('specialist_id', $specialistId)
            ->distinct('patient_id')
            ->pluck('patient_id');

        $patients = User::whereIn('id', $patientIds)
            ->where('is_active', true)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'patients' => $patients,
        ]);
    }

    /**
     * Show form to create new treatment plan.
     */
    public function create(Request $request)
    {
        $specialistId = Auth::id();

        // Get the pre-selected patient ID from URL parameter
        $selectedPatientId = $request->query('patient');

        // Get patients for dropdown
        $patientIds = \App\Models\TherapySession::where('specialist_id', $specialistId)
            ->distinct('patient_id')
            ->pluck('patient_id');

        $patients = User::whereIn('id', $patientIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Validate that the selected patient exists and belongs to this specialist
        $preSelectedPatient = null;
        if ($selectedPatientId) {
            $preSelectedPatient = $patients->where('id', $selectedPatientId)->first();
            if (!$preSelectedPatient) {
                $selectedPatientId = null;
            }
        }

        return view('specialist.treatment-plans.create', compact('patients', 'selectedPatientId', 'preSelectedPatient'));
    }

    /**
     * Store a newly created treatment plan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'tasks' => 'nullable|array',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.due_date' => 'nullable|date',
            'tasks.*.points_reward' => 'nullable|integer|min:0|max:100',
        ]);

        $specialistId = Auth::id();

        DB::beginTransaction();

        try {
            // Create treatment plan
            $plan = TreatmentPlan::create([
                'specialist_id' => $specialistId,
                'patient_id' => $request->patient_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'active', // Always active for new plans
            ]);

            // Create tasks
            if ($request->has('tasks') && !empty($request->tasks)) {
                foreach ($request->tasks as $taskData) {
                    TreatmentTask::create([
                        'plan_id' => $plan->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'due_date' => $taskData['due_date'] ?? null,
                        'points_reward' => $taskData['points_reward'] ?? 0,
                        'is_completed' => false,
                    ]);
                }
            }

            // Send notification to patient
            $this->sendPlanCreatedNotification($plan);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Treatment plan created successfully!'),
                'redirect_url' => route('specialist.treatment-plans.show', $plan->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating treatment plan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Failed to create treatment plan. Please try again.'),
            ], 500);
        }
    }

    /**
     * Display the specified treatment plan.
     */
    public function show($id)
    {
        $specialistId = Auth::id();

        $plan = TreatmentPlan::where('specialist_id', $specialistId)
            ->with(['patient', 'tasks'])
            ->findOrFail($id);

        $completedTasks = $plan->tasks->where('is_completed', true)->count();
        $totalTasks = $plan->tasks->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Group tasks by status
        $pendingTasks = $plan->tasks->where('is_completed', false);
        $completedTasksList = $plan->tasks->where('is_completed', true);

        // Check for overdue tasks
        foreach ($pendingTasks as $task) {
            $task->is_overdue = $task->due_date && Carbon::parse($task->due_date)->isPast();
        }

        return view('specialist.treatment-plans.show', compact('plan', 'progress', 'pendingTasks', 'completedTasksList'));
    }

    /**
     * Show form to edit treatment plan.
     */
    public function edit($id)
    {
        $specialistId = Auth::id();

        $plan = TreatmentPlan::where('specialist_id', $specialistId)
            ->with(['patient', 'tasks'])
            ->findOrFail($id);

        // Get patients for dropdown
        $patientIds = \App\Models\TherapySession::where('specialist_id', $specialistId)
            ->distinct('patient_id')
            ->pluck('patient_id');

        $patients = User::whereIn('id', $patientIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('specialist.treatment-plans.edit', compact('plan', 'patients'));
    }

    /**
     * Update the specified treatment plan.
     */
    public function update(Request $request, $id)
    {
        $specialistId = Auth::id();

        $plan = TreatmentPlan::where('specialist_id', $specialistId)
            ->with(['tasks'])
            ->findOrFail($id);

        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,cancelled',
            'tasks' => 'nullable|array',
            'tasks.*.id' => 'nullable|exists:treatment_tasks,id',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.due_date' => 'nullable|date',
            'tasks.*.points_reward' => 'nullable|integer|min:0|max:100',
            'deleted_tasks' => 'nullable|array',
            'deleted_tasks.*' => 'exists:treatment_tasks,id',
        ]);

        DB::beginTransaction();

        try {
            // Update plan
            $plan->update([
                'patient_id' => $request->patient_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ]);

            // Delete removed tasks
            if ($request->has('deleted_tasks')) {
                TreatmentTask::whereIn('id', $request->deleted_tasks)
                    ->where('plan_id', $plan->id)
                    ->delete();
            }

            // Update or create tasks
            $existingTaskIds = [];
            if ($request->has('tasks')) {
                foreach ($request->tasks as $taskData) {
                    if (isset($taskData['id']) && !empty($taskData['id'])) {
                        // Update existing task
                        $task = TreatmentTask::where('id', $taskData['id'])
                            ->where('plan_id', $plan->id)
                            ->first();
                        if ($task) {
                            $task->update([
                                'title' => $taskData['title'],
                                'description' => $taskData['description'] ?? null,
                                'due_date' => $taskData['due_date'] ?? null,
                                'points_reward' => $taskData['points_reward'] ?? 0,
                            ]);
                            $existingTaskIds[] = $task->id;
                        }
                    } else {
                        // Create new task
                        $newTask = TreatmentTask::create([
                            'plan_id' => $plan->id,
                            'title' => $taskData['title'],
                            'description' => $taskData['description'] ?? null,
                            'due_date' => $taskData['due_date'] ?? null,
                            'points_reward' => $taskData['points_reward'] ?? 0,
                            'is_completed' => false,
                        ]);
                        $existingTaskIds[] = $newTask->id;
                    }
                }
            }

            // Send notification to patient about updates
            $this->sendPlanUpdatedNotification($plan);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Treatment plan updated successfully!'),
                'redirect_url' => route('specialist.treatment-plans.show', $plan->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating treatment plan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Failed to update treatment plan. Please try again.'),
            ], 500);
        }
    }

    /**
     * Remove the specified treatment plan.
     */
    public function destroy($id)
    {
        $specialistId = Auth::id();

        $plan = TreatmentPlan::where('specialist_id', $specialistId)->findOrFail($id);

        // Send notification before deletion
        $this->sendPlanDeletedNotification($plan);

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => __('Treatment plan deleted successfully!'),
        ]);
    }

    // ==================== HELPER METHODS ====================

    private function getStatusBadge($status)
    {
        $badges = [
            'active' => '<span class="badge badge-active"><i class="fas fa-play-circle"></i> ' . __('Active') . '</span>',
            'completed' => '<span class="badge badge-completed"><i class="fas fa-check-circle"></i> ' . __('Completed') . '</span>',
            'cancelled' => '<span class="badge badge-cancelled"><i class="fas fa-times-circle"></i> ' . __('Cancelled') . '</span>',
        ];
        return $badges[$status] ?? '<span class="badge">' . ucfirst($status) . '</span>';
    }

    private function getProgressColor($progress)
    {
        if ($progress >= 75)
            return '#10b981';
        if ($progress >= 50)
            return '#3b82f6';
        if ($progress >= 25)
            return '#f59e0b';
        return '#ef4444';
    }

    private function sendPlanCreatedNotification($plan)
    {
        Notification::create([
            'user_id' => $plan->patient_id,
            'title' => __('New Treatment Plan 📋'),
            'message' => __('Dr. :specialist has created a new treatment plan for you: ":title". Please review it and start working on the tasks.', [
                'specialist' => $plan->specialist->name,
                'title' => $plan->title,
            ]),
            'type' => 'treatment_plan',
            'is_read' => false,
            'sent_at' => now(),
        ]);
    }

    private function sendPlanUpdatedNotification($plan)
    {
        Notification::create([
            'user_id' => $plan->patient_id,
            'title' => __('Treatment Plan Updated ✏️'),
            'message' => __('Your treatment plan ":title" has been updated. Please check the changes.', [
                'title' => $plan->title,
            ]),
            'type' => 'treatment_plan',
            'is_read' => false,
            'sent_at' => now(),
        ]);
    }

    private function sendPlanDeletedNotification($plan)
    {
        Notification::create([
            'user_id' => $plan->patient_id,
            'title' => __('Treatment Plan Removed 🗑️'),
            'message' => __('The treatment plan ":title" has been removed by your specialist.', [
                'title' => $plan->title,
            ]),
            'type' => 'treatment_plan',
            'is_read' => false,
            'sent_at' => now(),
        ]);
    }
}