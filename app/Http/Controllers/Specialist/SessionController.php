<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\TherapySession;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionController extends Controller
{
    /**
     * Show session details
     */
    public function show($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('specialist_id', Auth::id())
            ->with(['patient', 'review'])
            ->firstOrFail();

        $sessionTime = Carbon::parse($session->session_datetime);
        $now = now();

        // Calculate hours difference (positive for future)
        $hoursUntil = $now->diffInHours($sessionTime, false);

        // Join conditions
        $canJoin = false;
        $joinTime = null;

        if (in_array($session->status, ['scheduled', 'ongoing']) && $session->session_type !== 'text') {
            $minutesUntil = $now->diffInMinutes($sessionTime, false);
            $canJoin = $minutesUntil <= 15 && $minutesUntil >= -60;
            $joinTime = $sessionTime->copy()->subMinutes(15);
        }

        // Cancel condition - FIXED: Use abs() to get positive value
        $canCancel = $session->status === 'scheduled' && abs($hoursUntil) >= 24;

        // Complete condition
        $canComplete = $session->status === 'scheduled' &&
            Carbon::parse($session->session_datetime)->diffInMinutes(now()) <= -60;

        // Debug (remove after testing)
        \Log::info('Session cancel check', [
            'session_id' => $session->id,
            'status' => $session->status,
            'hours_until' => abs($hoursUntil),
            'can_cancel' => $canCancel
        ]);

        return view('specialist.sessions.show', compact('session', 'canJoin', 'canCancel', 'canComplete', 'joinTime'));
    }


    /**
     * Join session meeting - with rejoin within 1 hour time functionality
     */
    public function join($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('specialist_id', Auth::id())
            ->with(['patient'])
            ->firstOrFail();

        // For text sessions, redirect to chat directly
        if ($session->session_type === 'text') {
            return redirect()->route('chat.index', ['user' => $session->patient_id]);
        }

        $sessionTime = Carbon::parse($session->session_datetime);
        $sessionEndTime = $sessionTime->copy()->addMinutes($session->duration_minutes);
        $now = now();

        // Check if session is already completed
        if ($session->status === 'completed') {
            return redirect()->route('specialist.sessions.show', $session->id)
                ->with('info', __('This session has already been completed.'));
        }

        // Check if session is cancelled
        if ($session->status === 'cancelled') {
            return redirect()->route('specialist.sessions.show', $session->id)
                ->with('error', __('This session has been cancelled.'));
        }

        // Session is in the FUTURE (not started yet)
        if ($sessionTime->greaterThan($now)) {
            $minutesBefore = $now->diffInMinutes($sessionTime, false);
            $minutesBeforeAbsolute = abs($minutesBefore);

            if ($minutesBeforeAbsolute <= 15) {
                // Within join window, proceed
                if ($session->status === 'scheduled') {
                    $session->status = 'ongoing';
                    $session->save();
                }
            } else {
                $joinTime = $sessionTime->copy()->subMinutes(15);
                return redirect()->route('specialist.sessions.show', $session->id)
                    ->with('info', __('You can join the session 15 minutes before the scheduled time. Join time is :time.', [
                        'time' => $joinTime->translatedFormat('h:i A')
                    ]));
            }
        }
        // Session is ACTIVE (started but not ended yet) - REJOIN ALLOWED
        else if ($now->between($sessionTime, $sessionEndTime)) {
            if ($session->status !== 'ongoing') {
                $session->status = 'ongoing';
                $session->save();
            }
        }
        // Session has ENDED
        else if ($now->greaterThan($sessionEndTime)) {
            $minutesAfter = $now->diffInMinutes($sessionEndTime);

            if ($minutesAfter <= 60 && in_array($session->status, ['scheduled', 'ongoing'])) {
                return redirect()->route('specialist.sessions.show', $session->id)
                    ->with('info', __('This session has ended. You can mark it as completed from the session details page.'));
            }

            if ($minutesAfter > 60 && $session->status === 'scheduled') {
                $session->status = 'no_show';
                $session->save();

                Notification::create([
                    'user_id' => $session->patient_id,
                    'title' => __('Session Marked as No-Show'),
                    'message' => __('You missed your session with :specialist on :date. The session has been marked as no-show.', [
                        'specialist' => Auth::user()->name,
                        'date' => $sessionTime->translatedFormat('l, F d, Y \a\t h:i A')
                    ]),
                    'type' => 'session_reminder',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }

            return redirect()->route('specialist.sessions.show', $session->id)
                ->with('error', __('This session has ended. You cannot join it now.'));
        }

        // Generate secure, unique meeting link if not exists
        if (!$session->meeting_link) {
            $timestamp = $session->session_datetime->timestamp;
            $randomBytes = bin2hex(random_bytes(32)); // 64 characters
            $secureRoomName = 'tamman-' . $session->id . '-' . $timestamp . '-' . $randomBytes;

            $session->secure_room_name = $secureRoomName;
            $session->meeting_link = 'https://meet.jit.si/' . $secureRoomName;
            $session->save();
        }

        return view('specialist.sessions.join', compact('session'));
    }

    /**
     * Cancel session (AJAX)
     */
    public function cancel(Request $request, $sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('specialist_id', Auth::id())
            ->where('status', 'scheduled')
            ->with(['patient', 'specialist'])
            ->firstOrFail();

        $sessionDateTime = Carbon::parse($session->session_datetime);

        if ($sessionDateTime->diffInHours(now()) < 24) {
            return response()->json([
                'success' => false,
                'message' => __('Sessions can only be cancelled at least 24 hours in advance.')
            ], 422);
        }

        $session->status = 'cancelled';
        $session->save();

        // Notify patient
        Notification::create([
            'user_id' => $session->patient_id,
            'title' => __('Session Cancelled'),
            'message' => __('Your session with :specialist scheduled for :datetime has been cancelled.', [
                'specialist' => $session->specialist->name,
                'datetime' => $sessionDateTime->translatedFormat('l, M d, Y h:i A')
            ]),
            'type' => 'session_reminder',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Session cancelled successfully.'),
            'session' => [
                'id' => $session->id,
                'status' => $session->status,
                'status_badge' => '<span class="status-badge cancelled"><i class="fas fa-times-circle"></i> ' . __('Cancelled') . '</span>'
            ]
        ]);
    }

    /**
     * Complete session (AJAX)
     */
    public function complete(Request $request, $sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('specialist_id', Auth::id())
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->with(['patient', 'specialist', 'specialist.specialistProfile'])
            ->firstOrFail();

        $sessionDateTime = Carbon::parse($session->session_datetime);

        if ($sessionDateTime->diffInMinutes(now()) < 30) {
            return response()->json([
                'success' => false,
                'message' => __('Session can only be marked as completed after the scheduled time.')
            ], 422);
        }

        $session->status = 'completed';
        $session->save();

        // Award points to patient (15 points for attending session)
        $points = 15;
        $session->patient->addPoints($points, 'session_attendance', __('Attended session'), $session->id, TherapySession::class);
        $session->points_awarded = $points;
        $session->save();

        // Update specialist total sessions count
        $profile = $session->specialist->specialistProfile;
        $profile->total_sessions = ($profile->total_sessions ?? 0) + 1;
        $profile->save();

        // Notify patient
        Notification::create([
            'user_id' => $session->patient_id,
            'title' => __('Session Completed'),
            'message' => __('Your session with :specialist has been completed. You earned :points points!', [
                'specialist' => $session->specialist->name,
                'points' => $points
            ]),
            'type' => 'points_earned',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Session marked as completed. :points points awarded to patient.', ['points' => $points]),
            'session' => [
                'id' => $session->id,
                'status' => $session->status,
                'status_badge' => '<span class="status-badge completed"><i class="fas fa-check-circle"></i> ' . __('Completed') . '</span>'
            ],
        ]);
    }

    /**
     * Mark session as ongoing
     */
    public function markOngoing($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('specialist_id', Auth::id())
            ->where('status', 'scheduled')
            ->firstOrFail();

        $session->status = 'ongoing';
        $session->save();

        // Notify patient that session started
        Notification::create([
            'user_id' => $session->patient_id,
            'title' => __('Session Started'),
            'message' => __('Your session with :specialist has started. You can now join the meeting.', [
                'specialist' => $session->specialist->name
            ]),
            'type' => 'session_reminder',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Session marked as ongoing.')
        ]);
    }

    /**
     * Mark session as no-show
     */
    public function markNoShow($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('specialist_id', Auth::id())
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->firstOrFail();

        $session->status = 'no_show';
        $session->save();

        // Notify patient about no-show
        Notification::create([
            'user_id' => $session->patient_id,
            'title' => __('Session Missed'),
            'message' => __('You missed your session with :specialist. Please contact support to reschedule.', [
                'specialist' => $session->specialist->name
            ]),
            'type' => 'session_reminder',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Session marked as no-show.')
        ]);
    }

    /**
     * Check if specialist can join the session
     * 
     * @param int $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function canJoinSession($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('specialist_id', Auth::id())
            ->firstOrFail();

        // Check if session is already full (both have joined AND session is still ongoing)
        // But allow rejoin if the user is the one trying to rejoin
        if ($session->isFull()) {
            // If both have joined, check if THIS user is one of them
            $isThisUserJoined = ($session->specialist_joined && $session->specialist_id == Auth::id()) ||
                ($session->patient_joined && $session->patient_id == Auth::id());

            if (!$isThisUserJoined) {
                return response()->json([
                    'can_join' => false,
                    'reason' => 'full',
                    'message' => __('Both participants have already joined. Session is full.')
                ], 403);
            }
        }

        // Specialist can join (first time OR rejoin)
        return response()->json([
            'can_join' => true,
            'reason' => $session->specialist_joined ? 'rejoin' : 'first_join',
            'message' => $session->specialist_joined ? __('You are rejoining the session.') : __('You can join the session.')
        ]);
    }

    /**
     * Register that specialist has joined the session
     * 
     * @param int $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerJoin($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('specialist_id', Auth::id())
            ->firstOrFail();

        // Always update the joined timestamp on rejoin
        if (!$session->specialist_joined) {
            $session->specialist_joined = true;
        }
        $session->specialist_joined_at = now();
        $session->save();

        return response()->json([
            'success' => true,
            'user_type' => 'specialist',
            'session_full' => $session->isFull()
        ]);
    }
}
