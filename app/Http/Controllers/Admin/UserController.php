<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\MoodLog;
use App\Models\TestResult;
use App\Models\PointTransaction;
use App\Models\Notification;
use App\Models\DonorProfile;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of users (patients).
     */
    public function index()
    {
        $stats = [
            'total' => User::role('patient')->count(),
            'active' => User::role('patient')->where('is_active', true)->count(),
            'suspended' => User::role('patient')->where('is_active', false)->count(),
            'donors' => User::role('patient')->whereHas('donorProfile')->count(),
            'online' => $this->getOnlineCount(),
        ];

        return view('admin.users.index', compact('stats'));
    }

    /**
     * Get users data for DataTable (AJAX)
     */
    public function getUsersData(Request $request)
    {
        $query = User::role('patient')
            ->with(['donorProfile', 'therapySessionsAsPatient'])
            ->select('users.*');

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'suspended') {
                $query->where('is_active', false);
            }
        }

        // Filter by donor
        if ($request->has('donor') && $request->donor !== 'all') {
            if ($request->donor === 'yes') {
                $query->whereHas('donorProfile');
            } else {
                $query->whereDoesntHave('donorProfile');
            }
        }

        // NEW: Filter by gender
        if ($request->has('gender') && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        // Add additional data to each user
        $users->getCollection()->transform(function ($user) {
            $user->total_sessions = $user->therapySessionsAsPatient->count();
            $user->completed_sessions = $user->therapySessionsAsPatient->where('status', 'completed')->count();
            $user->is_donor = $user->donorProfile ? true : false;
            $user->last_session = $user->therapySessionsAsPatient()
                ->where('status', 'completed')
                ->latest('session_datetime')
                ->first();
            $user->is_online = $this->isUserOnline($user->id);
            // Convert numeric values to proper types
            $user->total_points = (int) $user->total_points;
            $user->credit_balance = (float) $user->credit_balance;
            $user->profile_image_url = $user->getProfileImageUrl();
            return $user;
        });

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ]);
    }

    /**
     * Show user details.
     */
    public function show($id)
    {
        $user = User::role('patient')
            ->with([
                'donorProfile',
                'therapySessionsAsPatient' => function ($q) {
                    $q->with(['specialist', 'review'])->latest('session_datetime');
                },
                'moodLogs' => function ($q) {
                    $q->latest('log_date')->limit(7);
                },
                'testResults' => function ($q) {
                    $q->latest('test_date')->limit(10);
                },
                'pointTransactions' => function ($q) {
                    $q->latest()->limit(10);
                },
                'creditTransactionsAsRecipient' => function ($q) {
                    $q->with('donor')->latest();
                },
                'notifications' => function ($q) {
                    $q->latest()->limit(10);
                },
                'reviewsGiven' => function ($q) {
                    $q->with('specialist');
                },
            ])
            ->findOrFail($id);

        // Statistics
        $stats = [
            'total_sessions' => TherapySession::where('patient_id', $user->id)->count(),
            'completed_sessions' => TherapySession::where('patient_id', $user->id)->where('status', 'completed')->count(),
            'cancelled_sessions' => TherapySession::where('patient_id', $user->id)->where('status', 'cancelled')->count(),
            'total_points' => $user->total_points,
            'total_credit' => $user->credit_balance,
            'total_mood_entries' => MoodLog::where('user_id', $user->id)->count(),
            'average_mood' => round(MoodLog::where('user_id', $user->id)->avg('mood_value') ?? 0, 1),
            'tests_taken' => TestResult::where('user_id', $user->id)->count(),
            'total_donated' => $user->donorProfile ? $user->donorProfile->total_donated : 0,
            'referral_count' => $user->referrals()->count(),
            'points_earned' => PointTransaction::where('user_id', $user->id)->where('type', 'earned')->sum('points'),
            'points_redeemed' => PointTransaction::where('user_id', $user->id)->where('type', 'redeemed')->sum('points'),
        ];

        // Chart data (last 30 days mood)
        $moodChart = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $mood = MoodLog::where('user_id', $user->id)->whereDate('log_date', $date)->first();
            $moodChart['labels'][] = $date->translatedFormat('M d');
            $moodChart['values'][] = $mood ? $mood->mood_value : null;
        }

        // Session trends (last 6 months)
        $sessionTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $sessionTrends['months'][] = $month->translatedFormat('M Y');
            $sessionTrends['sessions'][] = TherapySession::where('patient_id', $user->id)
                ->whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->count();
        }

        return view('admin.users.show', compact('user', 'stats', 'moodChart', 'sessionTrends'));
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $user = User::role('patient')->with(['donorProfile'])->findOrFail($id);

        // Calculate statistics for the edit page
        $stats = [
            'total_sessions' => TherapySession::where('patient_id', $user->id)->count(),
            'completed_sessions' => TherapySession::where('patient_id', $user->id)->where('status', 'completed')->count(),
            'total_points' => $user->total_points,
            'total_credit' => $user->credit_balance,
            'total_mood_entries' => MoodLog::where('user_id', $user->id)->count(),
            'average_mood' => round(MoodLog::where('user_id', $user->id)->avg('mood_value') ?? 0, 1),
            'tests_taken' => TestResult::where('user_id', $user->id)->count(),
            'points_earned' => PointTransaction::where('user_id', $user->id)->where('type', 'earned')->sum('points'),
            'points_redeemed' => PointTransaction::where('user_id', $user->id)->where('type', 'redeemed')->sum('points'),
        ];

        return view('admin.users.edit', compact('user', 'stats'));
    }

    /**
     * Update user (excluding image - image has separate routes)
     */
    public function update(Request $request, $id)
    {
        $user = User::role('patient')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'is_active' => 'boolean',
            'is_donor' => 'boolean',
            'credit_balance' => 'nullable|numeric|min:0',
            'total_points' => 'nullable|integer|min:0',
            'organization_name' => 'nullable|string|max:255',
            'total_donated' => 'nullable|numeric|min:0',
        ]);

        // Update user basic info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'is_active' => $request->boolean('is_active'),
            'credit_balance' => $request->credit_balance ?? $user->credit_balance,
            'total_points' => $request->total_points ?? $user->total_points,
        ]);

        // Handle donor role and profile
        if ($request->boolean('is_donor')) {
            if (!$user->hasRole('donor')) {
                $user->assignRole('donor');
            }

            DonorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_name' => $request->organization_name,
                    'total_donated' => $request->total_donated ?? 0,
                ]
            );
        } else {
            if ($user->hasRole('donor')) {
                $user->removeRole('donor');
            }
            if ($user->donorProfile) {
                $user->donorProfile->delete();
            }
        }

        // Track changes to critical fields
        $changes = [];
        if ($user->isDirty('email'))
            $changes['email_changed'] = ['from' => $user->getOriginal('email'), 'to' => $user->email];
        if ($user->isDirty('credit_balance'))
            $changes['credit_balance_changed'] = ['from' => $user->getOriginal('credit_balance'), 'to' => $user->credit_balance];
        if ($user->isDirty('total_points'))
            $changes['total_points_changed'] = ['from' => $user->getOriginal('total_points'), 'to' => $user->total_points];

        if (!empty($changes)) {
            SystemLog::create([
                'admin_id' => Auth::id(),
                'action' => 'update_user',
                'details' => array_merge([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'updated_at' => now()->toDateTimeString(),
                ], $changes)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('User updated successfully'),
            'user' => $user
        ]);
    }

    /**
     * Upload user profile image (dedicated method)
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = User::findOrFail($id);

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            // Generate unique filename
            $filename = 'user_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_images', $filename, 'public');

            // Delete old image if exists and it's not a seeded image
            if ($user->profile_image && !str_contains($user->profile_image, 'profile_seed')) {
                if (Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }
            }

            $user->profile_image = $path;
            $user->save();

            // Clear cache if you're using caching
            cache()->forget('user-profile-image-' . $user->id);

            return response()->json([
                'success' => true,
                'message' => __('Profile image updated successfully'),
                'image_url' => Storage::disk('public')->url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('No image file provided')
        ], 422);
    }

    /**
     * Remove user profile image
     */
    public function removeImage($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
            $user->profile_image = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => __('Profile image removed successfully')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('No profile image to remove')
        ], 422);
    }

    /**
     * Toggle user suspend/activate.
     */
    public function toggleSuspend($id)
    {
        $user = User::role('patient')->findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'suspended';

        // Create notification for user
        Notification::create([
            'user_id' => $user->id,
            'title' => $user->is_active ? __('Account Activated') : __('Account Suspended'),
            'message' => $user->is_active
                ? __('Your account has been reactivated. You can now access the platform again.')
                : __('Your account has been suspended. Please contact support for more information.'),
            'type' => 'account_status',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => $user->is_active ? 'activate_user' : 'suspend_user',
            'details' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => 'patient',
                'previous_status' => !$user->is_active ? 'active' : 'suspended',
                'new_status' => $user->is_active ? 'active' : 'suspended',
                'action_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('User :status successfully', ['status' => __($status)]),
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Delete user (soft delete).
     */
    public function destroy($id)
    {
        $user = User::role('patient')->findOrFail($id);

        // Log the deletion
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'delete_user',
            'details' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => 'patient',
                'deleted_at' => now()->toDateTimeString(),
            ]
        ]);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('User deleted successfully')
        ]);
    }

    /**
     * Export users to PDF.
     */
    public function exportPdf(Request $request)
    {
        // Increase memory limit for this operation
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);

        // Get users with filters
        $query = User::role('patient')->with(['donorProfile']);

        // Apply filters (same as getUsersData)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'suspended') {
                $query->where('is_active', false);
            }
        }

        if ($request->has('donor') && $request->donor !== 'all') {
            if ($request->donor === 'yes') {
                $query->whereHas('donorProfile');
            } else {
                $query->whereDoesntHave('donorProfile');
            }
        }

        // Filter by gender
        if ($request->has('gender') && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        // Get all users matching filters
        $users = $query->orderBy('created_at', 'desc')->get();

        // Add stats to each user
        foreach ($users as $user) {
            $user->total_sessions = TherapySession::where('patient_id', $user->id)->count();
            $user->completed_sessions = TherapySession::where('patient_id', $user->id)->where('status', 'completed')->count();
            $user->total_points_earned = PointTransaction::where('user_id', $user->id)->where('type', 'earned')->sum('points');
            $user->is_donor = $user->donorProfile ? true : false;
            // Remove profile image to save memory
            $user->profile_image = null;
        }

        $stats = [
            'total' => $users->count(),
            'active' => $users->where('is_active', true)->count(),
            'suspended' => $users->where('is_active', false)->count(),
            'donors' => $users->where('is_donor', true)->count(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        // Render view and convert to UTF-8
        $html = view('admin.users.export-pdf', compact('users', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Load PDF with UTF-8 support
        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');

        // Set default font to support Arabic
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('users-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Impersonate user (login as user).
     */
    public function impersonate($id)
    {
        $user = User::role('patient')->findOrFail($id);

        // Store original admin ID in session
        session(['impersonate_admin' => Auth::id()]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'impersonate_user',
            'details' => [
                'target_user_id' => $user->id,
                'target_user_name' => $user->name,
                'target_user_email' => $user->email,
                'target_user_role' => 'patient',
                'impersonated_at' => now()->toDateTimeString(),
            ]
        ]);

        Auth::login($user);

        return redirect()->route('patient.dashboard')->with('success', __('You are now logged in as :name', ['name' => $user->name]));
    }

    /**
     * Stop impersonating.
     */
    public function stopImpersonate()
    {
        $adminId = session('impersonate_admin');

        if ($adminId) {
            $admin = User::find($adminId);
         
            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'stop_impersonate',
                'details' => [
                    'returned_to_admin_id' => $admin->id,
                    'returned_to_admin_name' => $admin->name,
                    'stopped_at' => now()->toDateTimeString(),
                ]
            ]);

            Auth::login($admin);
            session()->forget('impersonate_admin');

            return redirect()->route('admin.users')->with('success', __('Returned to admin panel'));
        }

        return redirect()->route('admin.dashboard');
    }

    // ==================== HELPER METHODS ====================

    private function getOnlineCount()
    {
        // Simplified: count users who have sessions in the last 5 minutes
        return User::role('patient')
            ->where('last_login_at', '>=', Carbon::now()->subMinutes(5))
            ->count();
    }

    private function isUserOnline($userId)
    {
        return cache()->has('user-is-online-' . $userId);
    }
}