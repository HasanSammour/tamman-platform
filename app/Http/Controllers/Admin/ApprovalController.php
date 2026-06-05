<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialistProfile;
use App\Models\Notification;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\SpecialistApprovedMail;
use App\Mail\SpecialistRejectionMail;
use App\Mail\SpecialistRequestInfoMail;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    /**
     * Display specialist applications with filters.
     */
    public function index(Request $request)
    {
        $currentStatus = $request->get('status', 'pending');

        $stats = [
            'pending' => SpecialistProfile::where('is_verified', false)
                ->where('application_status', 'pending')
                ->count(),
            'approved' => SpecialistProfile::where('is_verified', true)
                ->where('application_status', 'approved')
                ->count(),
            'rejected' => SpecialistProfile::where('application_status', 'rejected')->count(),
            'total' => SpecialistProfile::count(),
        ];

        return view('admin.approvals.index', compact('stats', 'currentStatus'));
    }

    /**
     * Get applications data for DataTable (AJAX).
     */
    public function getApprovalsData(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = SpecialistProfile::with('user');

        if ($status === 'pending') {
            $query->where('is_verified', false)->where('application_status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('is_verified', true)->where('application_status', 'approved');
        } elseif ($status === 'rejected') {
            $query->where('application_status', 'rejected');
        }

        $query->orderBy('created_at', 'desc');

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $applications = $query->paginate($perPage);

        $applications->getCollection()->transform(function ($application) {
            $application->user_name = $application->user->name;
            $application->user_email = $application->user->email;
            $application->user_phone = $application->user->phone;
            $application->profile_image_url = $application->user->getProfileImageUrl();
            return $application;
        });

        return response()->json([
            'success' => true,
            'data' => $applications->items(),
            'total' => $applications->total(),
            'per_page' => $applications->perPage(),
            'current_page' => $applications->currentPage(),
            'last_page' => $applications->lastPage(),
        ]);
    }

    /**
     * Show application details.
     */
    public function show($id)
    {
        $application = SpecialistProfile::with('user')->findOrFail($id);

        $canApprove = $application->application_status === 'pending';
        $canReject = $application->application_status === 'pending';
        $canRequestInfo = $application->application_status === 'pending';

        $certificateInfo = $application->getCertificateInfo();
        $licenseInfo = $application->getLicenseInfo();

        return view('admin.approvals.show', compact(
            'application',
            'certificateInfo',
            'licenseInfo',
            'canApprove',
            'canReject',
            'canRequestInfo'
        ));
    }

    /**
     * Approve specialist application.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $application = SpecialistProfile::with('user')->findOrFail($id);

        if ($application->application_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('This application has already been processed.')
            ], 422);
        }

        // Save admin notes
        $notes = $request->notes;
        $application->update([
            'is_verified' => true,
            'application_status' => 'approved',
            'verified_at' => now(),
            'application_notes' => $notes ? ($application->application_notes ? $application->application_notes . "\n\n" : '')
                . '[' . now()->format('Y-m-d H:i') . '] APPROVED: ' . $notes : $application->application_notes,
        ]);

        $application->user->update([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        if (!$application->user->hasRole('specialist')) {
            $application->user->assignRole('specialist');
        }

        // Send Welcome Email
        Mail::to($application->user->email)->send(new SpecialistApprovedMail($application->user));

        // In-app notification
        Notification::create([
            'user_id' => $application->user_id,
            'title' => __('Application Approved 🎉'),
            'message' => __('Congratulations! Your specialist application has been approved. You can now log in and start accepting sessions.'),
            'type' => 'application_status',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'approve_specialist',
            'details' => [
                'specialist_id' => $application->user_id,
                'specialist_name' => $application->user->name,
                'specialist_email' => $application->user->email,
                'specialization' => $application->specialization,
                'license_number' => $application->license_number,
                'admin_notes' => $notes ?? null,
                'approved_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Specialist application approved successfully and welcome email sent')
        ]);
    }

    /**
     * Reject specialist application.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);

        $application = SpecialistProfile::with('user')->findOrFail($id);

        if ($application->application_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('This application has already been processed.')
            ], 422);
        }

        $notes = $request->notes;
        $application->update([
            'application_status' => 'rejected',
            'is_verified' => false,
            'application_notes' => ($application->application_notes ? $application->application_notes . "\n\n" : '')
                . '[' . now()->format('Y-m-d H:i') . '] REJECTED: ' . $request->reason
                . ($notes ? " | Admin Notes: {$notes}" : ''),
        ]);

        // Send Rejection Email with reason
        Mail::to($application->user->email)->send(new SpecialistRejectionMail($application->user, $request->reason));

        SystemLog::create([
            'admin_id' => Auth::id(),
            'action' => 'reject_specialist',
            'details' => [
                'specialist_id' => $application->user_id,
                'specialist_name' => $application->user->name,
                'specialist_email' => $application->user->email,
                'specialization' => $application->specialization,
                'rejection_reason' => $request->reason,
                'admin_notes' => $notes ?? null,
                'rejected_at' => now()->toDateTimeString(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Specialist application rejected and email sent to applicant')
        ]);
    }

    /**
     * Request more information from applicant.
     */
    public function requestInfo(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:10|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);

        $application = SpecialistProfile::with('user')->findOrFail($id);

        if ($application->application_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('Cannot request info for already processed applications.')
            ], 422);
        }

        $notes = $request->notes;

        // Send Request Info Email
        Mail::to($application->user->email)->send(new SpecialistRequestInfoMail($application->user, $request->message));

        $application->update([
            'application_notes' => ($application->application_notes ? $application->application_notes . "\n\n" : '')
                . '[' . now()->format('Y-m-d H:i') . '] REQUEST: ' . $request->message
                . ($notes ? " | Admin Notes: {$notes}" : ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Request sent to applicant via email')
        ]);
    }
}