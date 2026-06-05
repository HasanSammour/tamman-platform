<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SpecialistProfile;
use App\Models\TherapySession;
use App\Models\Review;
use App\Models\Notification;
use App\Models\SystemLog;
use App\Models\DonorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Mail\SpecialistMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SpecialistController extends Controller
{
    /**
     * Display a listing of verified specialists.
     */
    public function index()
    {
        $stats = [
            'total' => User::role('specialist')->whereHas('specialistProfile', function($q) {
                $q->where('is_verified', true);
            })->count(),
            'active' => User::role('specialist')->whereHas('specialistProfile', function($q) {
                $q->where('is_verified', true);
            })->where('is_active', true)->count(),
            'suspended' => User::role('specialist')->whereHas('specialistProfile', function($q) {
                $q->where('is_verified', true);
            })->where('is_active', false)->count(),
            'pending' => SpecialistProfile::where('is_verified', false)->count(),
            'online' => $this->getOnlineCount(),
        ];

        return view('admin.specialists.index', compact('stats'));
    }

    /**
     * Get specialists data for DataTable (AJAX)
     */
    public function getSpecialistsData(Request $request)
    {
        $query = User::role('specialist')
            ->whereHas('specialistProfile', function($q) {
                $q->where('is_verified', true);
            })
            ->with(['specialistProfile', 'donorProfile'])
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
    
        // Filter by specialization
        if ($request->has('specialization') && !empty($request->specialization) && $request->specialization !== 'all') {
            $query->whereHas('specialistProfile', function($q) use ($request) {
                $q->where('specialization', 'like', "%{$request->specialization}%");
            });
        }
    
        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
    
        // Pagination
        $perPage = $request->get('per_page', 15);
        $specialists = $query->paginate($perPage);
    
        // Add additional data to each specialist
        $specialists->getCollection()->transform(function ($specialist) {
            $profile = $specialist->specialistProfile;
            $specialist->specialization = $profile->specialization ?? '-';
            $specialist->consultation_fee = $profile->consultation_fee ?? 0;
            $specialist->total_sessions = $profile->total_sessions ?? 0;
            $specialist->rating_avg = $profile->rating_avg ?? 0;
            
            // Calculate earnings by joining with specialist_profiles
            $specialist->total_earnings = TherapySession::where('therapy_sessions.specialist_id', $specialist->id)
                ->where('therapy_sessions.status', 'completed')
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee') ?? 0;
            
            $specialist->is_verified = $profile->is_verified ?? false;
            $specialist->is_donor = $specialist->donorProfile ? true : false;
            $specialist->is_online = $this->isUserOnline($specialist->id);
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
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ]);
    }
    
    /**
     * Show specialist details.
     */
    public function show($id)
    {
        $specialist = User::role('specialist')
            ->with([
                'specialistProfile',
                'donorProfile',
                'therapySessionsAsSpecialist' => function ($q) {
                    $q->with(['patient', 'review'])->latest('session_datetime');
                },
                'reviewsReceived' => function ($q) {
                    $q->with('reviewer')->latest();
                },
                'pointTransactions' => function ($q) {
                    $q->latest()->limit(10);
                },
                'notifications' => function ($q) {
                    $q->latest()->limit(10);
                },
                'availability'
            ])
            ->findOrFail($id);
    
        $profile = $specialist->specialistProfile;
    
        // Statistics
        $stats = [
            'total_sessions' => TherapySession::where('specialist_id', $specialist->id)->count(),
            'completed_sessions' => TherapySession::where('specialist_id', $specialist->id)->where('status', 'completed')->count(),
            'cancelled_sessions' => TherapySession::where('specialist_id', $specialist->id)->where('status', 'cancelled')->count(),
            'no_show_sessions' => TherapySession::where('specialist_id', $specialist->id)->where('status', 'no_show')->count(),
            'total_clients' => TherapySession::where('specialist_id', $specialist->id)->distinct('patient_id')->count('patient_id'),
            'total_earnings' => TherapySession::where('therapy_sessions.specialist_id', $specialist->id)
                ->where('therapy_sessions.status', 'completed')
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee') ?? 0,
            'average_rating' => $profile->rating_avg ?? 0,
            'total_reviews' => Review::where('specialist_id', $specialist->id)->count(),
            'total_points' => $specialist->total_points,
            'total_credit' => $specialist->credit_balance,
            'total_donated' => $specialist->donorProfile ? $specialist->donorProfile->total_donated : 0,
            'is_donor' => $specialist->donorProfile ? true : false,
        ];
    
        // Monthly sessions chart (last 6 months)
        $monthlySessions = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthlySessions['months'][] = $month->translatedFormat('M Y');
            $monthlySessions['sessions'][] = TherapySession::where('specialist_id', $specialist->id)
                ->whereBetween('session_datetime', [$monthStart, $monthEnd])
                ->count();
        }
    
        // Monthly earnings chart (last 6 months)
        $monthlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthlyEarnings['months'][] = $month->translatedFormat('M Y');
            $monthlyEarnings['earnings'][] = TherapySession::where('therapy_sessions.specialist_id', $specialist->id)
                ->where('therapy_sessions.status', 'completed')
                ->whereBetween('therapy_sessions.session_datetime', [$monthStart, $monthEnd])
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee') ?? 0;
        }
    
        // Rating distribution
        $ratingDistribution = [
            1 => Review::where('specialist_id', $specialist->id)->where('rating', 1)->count(),
            2 => Review::where('specialist_id', $specialist->id)->where('rating', 2)->count(),
            3 => Review::where('specialist_id', $specialist->id)->where('rating', 3)->count(),
            4 => Review::where('specialist_id', $specialist->id)->where('rating', 4)->count(),
            5 => Review::where('specialist_id', $specialist->id)->where('rating', 5)->count(),
        ];
    
        // Recent clients (last 5 unique patients)
        $recentClients = User::whereHas('therapySessionsAsPatient', function($q) use ($specialist) {
            $q->where('specialist_id', $specialist->id);
        })->with(['therapySessionsAsPatient' => function($q) use ($specialist) {
            $q->where('specialist_id', $specialist->id)->latest()->limit(1);
        }])->limit(5)->get();
    
        // Recent sessions (last 10)
        $recentSessions = TherapySession::where('specialist_id', $specialist->id)
            ->with(['patient', 'review'])
            ->latest('session_datetime')
            ->limit(10)
            ->get();
    
        return view('admin.specialists.show', compact(
            'specialist', 'profile', 'stats', 'monthlySessions', 'monthlyEarnings',
            'ratingDistribution', 'recentClients', 'recentSessions'
        ));
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $specialist = User::role('specialist')
            ->with(['specialistProfile', 'donorProfile'])
            ->findOrFail($id);

        $profile = $specialist->specialistProfile;

        $stats = [
            'total_sessions' => TherapySession::where('specialist_id', $specialist->id)->count(),
            'completed_sessions' => TherapySession::where('specialist_id', $specialist->id)->where('status', 'completed')->count(),
            'total_clients' => TherapySession::where('specialist_id', $specialist->id)->distinct('patient_id')->count('patient_id'),
            'total_earnings' => TherapySession::where('therapy_sessions.specialist_id', $specialist->id)
                ->where('therapy_sessions.status', 'completed')
                ->join('specialist_profiles', 'therapy_sessions.specialist_id', '=', 'specialist_profiles.user_id')
                ->sum('specialist_profiles.consultation_fee') ?? 0,
            'average_rating' => $profile->rating_avg ?? 0,
            'total_reviews' => Review::where('specialist_id', $specialist->id)->count(),
        ];

        return view('admin.specialists.edit', compact('specialist', 'profile', 'stats'));
    }

    /**
     * Update specialist (excluding image - image has separate routes)
     */
    public function update(Request $request, $id)
    {
        $specialist = User::role('specialist')->findOrFail($id);
        $profile = $specialist->specialistProfile;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $specialist->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'qualifications' => 'nullable|string',
            'bio' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0',
            'languages' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'is_donor' => 'boolean',
            'credit_balance' => 'nullable|numeric|min:0',
            'organization_name' => 'nullable|string|max:255',
            'total_donated' => 'nullable|numeric|min:0',
            'rating_avg' => 'nullable|numeric|min:0|max:5',
        ]);

        // Update user basic info (no points for specialists)
        $specialist->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active'),
            'credit_balance' => $request->credit_balance ?? $specialist->credit_balance,
        ]);

        // Update specialist profile
        $profile->update([
            'specialization' => $request->specialization,
            'license_number' => $request->license_number,
            'qualifications' => $request->qualifications,
            'bio' => $request->bio,
            'consultation_fee' => $request->consultation_fee,
            'experience_years' => $request->experience_years,
            'languages' => $request->languages,
            'is_verified' => $request->boolean('is_verified'),
            'rating_avg' => $request->rating_avg ?? $profile->rating_avg,
        ]);

        // Handle donor role and profile
        if ($request->boolean('is_donor')) {
            if (!$specialist->hasRole('donor')) {
                $specialist->assignRole('donor');
            }
            DonorProfile::updateOrCreate(
                ['user_id' => $specialist->id],
                [
                    'organization_name' => $request->organization_name,
                    'total_donated' => $request->total_donated ?? 0,
                ]
            );
        } else {
            if ($specialist->hasRole('donor')) {
                $specialist->removeRole('donor');
            }
            if ($specialist->donorProfile) {
                $specialist->donorProfile->delete();
            }
        }

        // Log the action
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'update_specialist',
            'details' => [
                'specialist_id' => $specialist->id,
                'specialist_name' => $specialist->name,
                'specialist_email' => $specialist->email,
                'updates' => [
                    'name' => $request->name,
                    'email' => $request->email,
                    'is_active' => $request->boolean('is_active'),
                    'is_verified' => $request->boolean('is_verified'),
                    'consultation_fee' => $request->consultation_fee,
                    'specialization' => $request->specialization,
                ],
                'updated_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Specialist updated successfully'),
            'specialist' => $specialist
        ]);
    }

    /**
     * Upload specialist profile image (dedicated method)
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $specialist = User::findOrFail($id);

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            // Generate unique filename
            $filename = 'specialist_' . $specialist->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_images', $filename, 'public');

            // Delete old image if exists and it's not a seeded image
            if ($specialist->profile_image && !str_contains($specialist->profile_image, 'profile_seed')) {
                if (Storage::disk('public')->exists($specialist->profile_image)) {
                    Storage::disk('public')->delete($specialist->profile_image);
                }
            }

            $specialist->profile_image = $path;
            $specialist->save();

            // Clear cache if you're using caching
            cache()->forget('user-profile-image-' . $specialist->id);

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
     * Remove specialist profile image
     */
    public function removeImage($id)
    {
        $specialist = User::findOrFail($id);

        if ($specialist->profile_image && Storage::disk('public')->exists($specialist->profile_image)) {
            Storage::disk('public')->delete($specialist->profile_image);
            $specialist->profile_image = null;
            $specialist->save();

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
     * Update specialist documents (certificate and license)
     */
    public function updateDocuments(Request $request, $id)
    {
        $profile = SpecialistProfile::where('user_id', $id)->firstOrFail();

        // Handle certificate file upload
        if ($request->hasFile('certificate_file')) {
            $request->validate([
                'certificate_file' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            $file = $request->file('certificate_file');
            $filename = 'cert_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('specialist_documents', $filename, 'public');

            // Delete old file if exists
            if ($profile->certificate_file && Storage::disk('public')->exists($profile->certificate_file)) {
                Storage::disk('public')->delete($profile->certificate_file);
            }

            $profile->certificate_file = $path;
            $profile->save();

            return response()->json([
                'success' => true,
                'message' => __('Certificate uploaded successfully'),
                'certificate_url' => $profile->getCertificateUrl()
            ]);
        }

        // Handle license file upload
        if ($request->hasFile('license_file')) {
            $request->validate([
                'license_file' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            $file = $request->file('license_file');
            $filename = 'lic_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('specialist_documents', $filename, 'public');

            // Delete old file if exists
            if ($profile->license_file && Storage::disk('public')->exists($profile->license_file)) {
                Storage::disk('public')->delete($profile->license_file);
            }

            $profile->license_file = $path;
            $profile->save();

            return response()->json([
                'success' => true,
                'message' => __('License uploaded successfully'),
                'license_url' => $profile->getLicenseUrl()
            ]);
        }

        // Handle document removal
        if ($request->has('remove_certificate')) {
            if ($profile->certificate_file && Storage::disk('public')->exists($profile->certificate_file)) {
                Storage::disk('public')->delete($profile->certificate_file);
            }
            $profile->certificate_file = null;
            $profile->save();

            return response()->json([
                'success' => true,
                'message' => __('Certificate removed successfully')
            ]);
        }

        if ($request->has('remove_license')) {
            if ($profile->license_file && Storage::disk('public')->exists($profile->license_file)) {
                Storage::disk('public')->delete($profile->license_file);
            }
            $profile->license_file = null;
            $profile->save();

            return response()->json([
                'success' => true,
                'message' => __('License removed successfully')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('No file provided')
        ], 422);
    }

    /**
     * Toggle specialist suspend/activate.
     */
    public function toggleSuspend($id)
    {
        $specialist = User::role('specialist')->findOrFail($id);
        $specialist->is_active = !$specialist->is_active;
        $specialist->save();

        $status = $specialist->is_active ? 'activated' : 'suspended';

        Notification::create([
            'user_id' => $specialist->id,
            'title' => $specialist->is_active ? __('Account Activated') : __('Account Suspended'),
            'message' => $specialist->is_active
                ? __('Your account has been reactivated. You can now access the platform again.')
                : __('Your account has been suspended. Please contact support for more information.'),
            'type' => 'account_status',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => $specialist->is_active ? 'activate_specialist' : 'suspend_specialist',
            'details' => [
                'specialist_id' => $specialist->id,
                'specialist_name' => $specialist->name,
                'specialist_email' => $specialist->email,
                'status' => $specialist->is_active ? 'active' : 'suspended',
                'changed_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Specialist :status successfully', ['status' => __($status)]),
            'is_active' => $specialist->is_active
        ]);
    }

    /**
     * Delete specialist.
     */
    public function destroy($id)
    {
        $specialist = User::role('specialist')->findOrFail($id);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'delete_specialist',
            'details' => [
                'specialist_id' => $specialist->id,
                'specialist_name' => $specialist->name,
                'specialist_email' => $specialist->email,
                'specialization' => $specialist->specialistProfile?->specialization,
                'deleted_at' => now()->toDateTimeString(),
            ]
        ]);

        $specialist->delete();

        return response()->json([
            'success' => true,
            'message' => __('Specialist deleted successfully')
        ]);
    }

    /**
     * Export specialists to PDF.
     */
    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);

        $query = User::role('specialist')
            ->whereHas('specialistProfile', function($q) {
                $q->where('is_verified', true);
            })
            ->with(['specialistProfile', 'donorProfile']);

        // Apply filters
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

        $specialists = $query->orderBy('created_at', 'desc')->get();

        foreach ($specialists as $specialist) {
            $profile = $specialist->specialistProfile;
            $specialist->specialization = $profile->specialization ?? '-';
            $specialist->consultation_fee = $profile->consultation_fee ?? 0;
            $specialist->total_sessions = $profile->total_sessions ?? 0;
            $specialist->rating_avg = $profile->rating_avg ?? 0;
            $specialist->is_verified = $profile->is_verified ?? false;
            $specialist->is_donor = $specialist->donorProfile ? true : false;
        }

        $stats = [
            'total' => $specialists->count(),
            'active' => $specialists->where('is_active', true)->count(),
            'suspended' => $specialists->where('is_active', false)->count(),
            'pending' => SpecialistProfile::where('is_verified', false)->count(), // Add this line
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name,
        ];

        $html = view('admin.specialists.export-pdf', compact('specialists', 'stats'))->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->download('specialists-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Send email to specialist.
     */
    public function sendEmail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $specialist = User::role('specialist')->findOrFail($id);

        Mail::to($specialist->email)->send(new SpecialistMail($specialist, $request->subject, $request->message));

        // Log the action
        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'send_email_to_specialist',
            'details' => [
                'specialist_id' => $specialist->id,
                'specialist_name' => $specialist->name,
                'specialist_email' => $specialist->email,
                'subject' => $request->subject,
                'sent_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Email sent successfully to :name', ['name' => $specialist->name])
        ]);
    }

    // ==================== HELPER METHODS ====================

    private function getOnlineCount()
    {
        return User::role('specialist')
            ->where('last_login_at', '>=', Carbon::now()->subMinutes(5))
            ->count();
    }

    private function isUserOnline($userId)
    {
        return cache()->has('user-is-online-' . $userId);
    }
}