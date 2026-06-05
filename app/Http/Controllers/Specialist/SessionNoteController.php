<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionNoteController extends Controller
{
    /**
     * Display a listing of session notes.
     */
    public function index(Request $request)
    {
        $specialistId = Auth::id();
        
        // Get client ID from URL parameter if present
        $preSelectedClient = $request->get('client');
        
        // Statistics
        $stats = [
            'total_sessions' => TherapySession::where('specialist_id', $specialistId)->count(),
            'with_notes' => TherapySession::where('specialist_id', $specialistId)
                ->whereNotNull('notes')
                ->where('notes', '!=', '')
                ->count(),
            'without_notes' => TherapySession::where('specialist_id', $specialistId)
                ->where(function($q) {
                    $q->whereNull('notes')->orWhere('notes', '');
                })
                ->count(),
            'completed_sessions' => TherapySession::where('specialist_id', $specialistId)
                ->where('status', 'completed')
                ->count(),
        ];
        
        // Get patients for filter dropdown
        $patientIds = TherapySession::where('specialist_id', $specialistId)
            ->distinct('patient_id')
            ->pluck('patient_id');
        
        $patients = User::whereIn('id', $patientIds)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        // Pass the pre-selected client ID to the view
        return view('specialist.session-notes.index', compact('stats', 'patients', 'preSelectedClient'));
    }

    /**
     * Get session notes data for DataTable (AJAX)
     */
    public function getNotesData(Request $request)
    {
        $specialistId = Auth::id();

        $query = TherapySession::where('specialist_id', $specialistId)
            ->with(['patient'])
            ->select('therapy_sessions.*');

        // Filter by patient
        if ($request->has('patient_id') && $request->patient_id !== 'all') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by status (has notes / no notes)
        if ($request->has('notes_status') && $request->notes_status !== 'all') {
            if ($request->notes_status === 'has_notes') {
                $query->whereNotNull('notes')->where('notes', '!=', '');
            } elseif ($request->notes_status === 'no_notes') {
                $query->where(function ($q) {
                    $q->whereNull('notes')->orWhere('notes', '');
                });
            }
        }

        // Filter by session status
        if ($request->has('session_status') && $request->session_status !== 'all') {
            $query->where('status', $request->session_status);
        }

        // Search by patient name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('session_datetime', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('session_datetime', '<=', $request->date_to);
        }

        // Sort
        $sortField = $request->get('sort_field', 'session_datetime');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $sessions = $query->paginate($perPage);

        // Add additional data with proper UTF-8 handling
        $sessions->getCollection()->transform(function ($session) {
            $session->patient_name = $session->patient->name;
            $session->has_notes = !empty($session->notes);

            // Fix: Use mb_substr for multi-byte UTF-8 characters (Arabic)
            if ($session->has_notes) {
                // Remove HTML tags and decode entities
                $notesPreview = strip_tags($session->notes);
                $notesPreview = html_entity_decode($notesPreview, ENT_QUOTES, 'UTF-8');
                // Use mb_substr for proper UTF-8 handling
                if (mb_strlen($notesPreview) > 100) {
                    $notesPreview = mb_substr($notesPreview, 0, 100) . '...';
                }
                $session->notes_preview = $notesPreview;
            } else {
                $session->notes_preview = null;
            }

            $session->date_formatted = Carbon::parse($session->session_datetime)->translatedFormat('M d, Y');
            $session->time_formatted = Carbon::parse($session->session_datetime)->format('h:i A');
            $session->status_text = __(ucfirst($session->status));
            $session->status_class = $session->status;
            $session->type_text = __(ucfirst($session->session_type));
            $session->type_class = $session->session_type;

            return $session;
        });

        return response()->json([
            'success' => true,
            'data' => $sessions->items(),
            'total' => $sessions->total(),
            'per_page' => $sessions->perPage(),
            'current_page' => $sessions->currentPage(),
            'last_page' => $sessions->lastPage(),
        ]);
    }

    /**
     * Get patients for filter dropdown (AJAX)
     */
    public function getPatients()
    {
        $specialistId = Auth::id();

        $patientIds = TherapySession::where('specialist_id', $specialistId)
            ->distinct('patient_id')
            ->pluck('patient_id');

        $patients = User::whereIn('id', $patientIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'patients' => $patients,
        ]);
    }

    /**
     * Show form to edit session notes.
     */
    public function edit($sessionId)
    {
        $specialistId = Auth::id();

        $session = TherapySession::where('specialist_id', $specialistId)
            ->with(['patient'])
            ->findOrFail($sessionId);

        // Only allow editing for completed or scheduled sessions
        if (!in_array($session->status, ['completed', 'scheduled'])) {
            return redirect()->route('specialist.session-notes.index')
                ->with('error', __('Notes can only be added for completed or scheduled sessions.'));
        }

        return view('specialist.session-notes.edit', compact('session'));
    }

    /**
     * Update session notes.
     */
    public function update(Request $request, $sessionId)
    {
        $specialistId = Auth::id();

        $request->validate([
            'notes' => 'nullable|string|max:5000',
        ]);

        $session = TherapySession::where('specialist_id', $specialistId)
            ->findOrFail($sessionId);

        $session->notes = $request->notes;
        $session->save();

        return response()->json([
            'success' => true,
            'message' => __('Session notes saved successfully!'),
            'redirect_url' => route('specialist.session-notes.index'),
        ]);
    }

    // ==================== HELPER METHODS ====================

    private function getStatusBadge($status)
    {
        $badges = [
            'scheduled' => '<span class="badge badge-scheduled"><i class="fas fa-clock"></i> ' . e(__('Scheduled')) . '</span>',
            'ongoing' => '<span class="badge badge-ongoing"><i class="fas fa-spinner fa-pulse"></i> ' . e(__('Ongoing')) . '</span>',
            'completed' => '<span class="badge badge-completed"><i class="fas fa-check-circle"></i> ' . e(__('Completed')) . '</span>',
            'cancelled' => '<span class="badge badge-cancelled"><i class="fas fa-times-circle"></i> ' . e(__('Cancelled')) . '</span>',
            'no_show' => '<span class="badge badge-no-show"><i class="fas fa-user-slash"></i> ' . e(__('No Show')) . '</span>',
        ];
        return $badges[$status] ?? '<span class="badge">' . e(ucfirst($status)) . '</span>';
    }

    private function getTypeBadge($type)
    {
        $badges = [
            'video' => '<span class="type-badge video"><i class="fas fa-video"></i> ' . e(__('Video')) . '</span>',
            'audio' => '<span class="type-badge audio"><i class="fas fa-phone-alt"></i> ' . e(__('Audio')) . '</span>',
            'text' => '<span class="type-badge text"><i class="fas fa-comment-dots"></i> ' . e(__('Text')) . '</span>',
        ];
        return $badges[$type] ?? '<span class="type-badge">' . e(ucfirst($type)) . '</span>';
    }
}