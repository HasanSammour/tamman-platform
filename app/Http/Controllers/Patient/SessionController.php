<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\TherapySession;
use App\Models\Review;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionController extends Controller
{
    /**
     * Display a listing of patient sessions.
     */
    public function index()
    {
        $user = Auth::user();

        // Update any expired sessions before displaying
        $this->updateExpiredSessions($user->id);

        // Get upcoming sessions (scheduled)
        $upcomingSessions = TherapySession::where('patient_id', $user->id)
            ->where('status', 'scheduled')
            ->where('session_datetime', '>', now())
            ->with(['specialist', 'specialist.specialistProfile', 'rewardRedemption.reward'])
            ->orderBy('session_datetime', 'asc')
            ->get();

        // Get past sessions (completed, cancelled, no_show)
        $pastSessions = TherapySession::where('patient_id', $user->id)
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere('status', 'cancelled')
                    ->orWhere('status', 'no_show');
            })
            ->with(['specialist', 'specialist.specialistProfile', 'review', 'rewardRedemption.reward'])
            ->orderBy('session_datetime', 'desc')
            ->get();

        // Get session status counts
        $statusCounts = [
            'upcoming' => $upcomingSessions->count(),
            'completed' => TherapySession::where('patient_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => TherapySession::where('patient_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        return view('patient.sessions.index', compact('upcomingSessions', 'pastSessions', 'statusCounts'));
    }

    /**
     * Update expired scheduled sessions to no_show status
     */
    private function updateExpiredSessions($userId)
    {
        $oneHourAgo = now()->subHour();

        $expiredSessions = TherapySession::where('patient_id', $userId)
            ->where('status', 'scheduled')
            ->where('session_datetime', '<=', $oneHourAgo)
            ->with(['specialist'])
            ->get();

        foreach ($expiredSessions as $session) {
            $session->status = 'no_show';
            $session->save();

            // Create notification for patient
            Notification::create([
                'user_id' => $userId,
                'title' => __('Session Marked as No-Show'),
                'message' => __('You missed your session with :specialist on :date. The session has been marked as no-show.', [
                    'specialist' => $session->specialist->name,
                    'date' => Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y \a\t h:i A')
                ]),
                'type' => 'session_reminder',
                'is_read' => false,
                'sent_at' => now(),
            ]);

            // Create notification for specialist
            Notification::create([
                'user_id' => $session->specialist_id,
                'title' => __('Patient No-Show'),
                'message' => __('The patient :patient did not join the scheduled session on :date and has been marked as no-show.', [
                    'patient' => $session->patient->name,
                    'date' => Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y \a\t h:i A')
                ]),
                'type' => 'session_reminder',
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }
    }

    /**
     * Display session details.
     */
    public function show($sessionId)
    {
        // Update expired sessions first
        $this->updateExpiredSessions(Auth::id());

        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->with(['specialist', 'specialist.specialistProfile', 'review', 'rewardRedemption.reward'])
            ->firstOrFail();

        // Check if session can be joined (15 minutes before)
        $canJoin = false;
        $joinTime = null;
        if (in_array($session->status, ['scheduled', 'ongoing']) && $session->session_type !== 'text') {
            $now = now();
            $sessionTime = Carbon::parse($session->session_datetime);
            $minutesUntil = $now->diffInMinutes($sessionTime, false);
            $canJoin = $minutesUntil <= 15 && $minutesUntil >= -60;
            $joinTime = $sessionTime->copy()->subMinutes(15);
        }

        // Check if session can be cancelled (24 hours before)
        $canCancel = false;
        if ($session->status === 'scheduled') {
            $canCancel = abs(Carbon::parse($session->session_datetime)->diffInHours(now())) >= 24;
        }

        // Check if session can be rated
        $canRate = false;
        if ($session->status === 'completed' && !$session->review) {
            $canRate = true;
        }

        // Get free reward name if applicable
        $freeRewardName = $this->getFreeRewardName($session);

        // Get payment method text
        $paymentMethodText = $this->getPaymentMethodText($session);

        // Get session type icon
        $sessionTypeIcon = $this->getSessionTypeIcon($session->session_type);
        $sessionTypeColor = $this->getSessionTypeColor($session->session_type);

        // Get status badge class and icon
        $statusBadgeClass = $this->getStatusBadgeClass($session->status);
        $statusIcon = $this->getStatusIcon($session->status);
        $statusText = $this->getStatusText($session->status);

        // Get formatted date and time
        $formattedDate = Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y');
        $startTime = Carbon::parse($session->session_datetime)->translatedFormat('h:i A');
        $endTime = Carbon::parse($session->session_datetime)->addMinutes($session->duration_minutes)->translatedFormat('h:i A');

        // Get time until session
        $timeUntil = null;
        if ($session->status === 'scheduled') {
            $sessionTime = Carbon::parse($session->session_datetime);
            $now = now();
            if ($sessionTime->greaterThan($now)) {
                $diff = $now->diff($sessionTime);
                $timeUntil = [];
                if ($diff->d > 0)
                    $timeUntil['days'] = $diff->d;
                if ($diff->h > 0)
                    $timeUntil['hours'] = $diff->h;
                if ($diff->i > 0 && $diff->d === 0)
                    $timeUntil['minutes'] = $diff->i;
            }
        }

        // Get session rating if exists
        $sessionRating = $session->review->rating ?? null;
        $sessionComment = $session->review->comment ?? null;
        $reviewDate = $session->review ? Carbon::parse($session->review->created_at)->translatedFormat('M d, Y') : null;

        return view('patient.sessions.show', compact(
            'session',
            'canJoin',
            'canCancel',
            'canRate',
            'joinTime',
            'freeRewardName',
            'paymentMethodText',
            'sessionTypeIcon',
            'sessionTypeColor',
            'statusBadgeClass',
            'statusIcon',
            'statusText',
            'formattedDate',
            'startTime',
            'endTime',
            'timeUntil',
            'sessionRating',
            'sessionComment',
            'reviewDate'
        ));
    }

    /**
     * Join session meeting.
     */
    /**
     * Join session meeting.
     */
    public function join($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->with(['specialist', 'rewardRedemption.reward'])
            ->firstOrFail();

        // If this is a text session, redirect to chat directly
        if ($session->session_type === 'text') {
            return redirect()->route('chat.index', ['user' => $session->specialist_id]);
        }

        $sessionTime = Carbon::parse($session->session_datetime);
        $sessionEndTime = $sessionTime->copy()->addMinutes($session->duration_minutes);
        $now = now();
        $minutesBefore = $now->diffInMinutes($sessionTime, false);
        $isSessionWindow = $now->between($sessionTime, $sessionEndTime);

        // CASE 1: Session is in the FUTURE (not started yet)
        if ($sessionTime->greaterThan($now)) {
            // Check if within 15 minutes before session
            if ($minutesBefore <= 15 && $minutesBefore >= 0) {
                // Within join window, proceed
                // Update session status to 'ongoing' if within window
                if ($session->status === 'scheduled') {
                    $session->status = 'ongoing';
                    $session->save();
                }
            } else {
                // Too early to join
                $joinTime = $sessionTime->copy()->subMinutes(15);
                return redirect()->route('patient.sessions.show', $session->id)
                    ->with('info', __('You can join the session 15 minutes before the scheduled time. Join time is :time.', [
                        'time' => $joinTime->translatedFormat('h:i A')
                    ]));
            }
        }

        // CASE 2: Session is currently ACTIVE (during session window)
        elseif ($isSessionWindow) {
            // Allow rejoin even if disconnected
            if ($session->status === 'scheduled' || $session->status === 'ongoing') {
                if ($session->status === 'scheduled') {
                    $session->status = 'ongoing';
                    $session->save();
                }
                // Continue to meeting
            }
        }

        // CASE 3: Session has ENDED (past end time)
        elseif ($now->greaterThan($sessionEndTime)) {
            // Only allow if session is still scheduled (patient never joined)
            if ($session->status === 'scheduled') {
                // Mark as no-show after 15 minutes grace period
                if ($now->diffInMinutes($sessionEndTime) > 15) {
                    $session->status = 'no_show';
                    $session->save();

                    Notification::create([
                        'user_id' => Auth::id(),
                        'title' => __('Session Marked as No-Show'),
                        'message' => __('You missed your session with :specialist on :date. The session has been marked as no-show.', [
                            'specialist' => $session->specialist->name,
                            'date' => $sessionTime->translatedFormat('l, F d, Y \a\t h:i A')
                        ]),
                        'type' => 'session_reminder',
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);

                    Notification::create([
                        'user_id' => $session->specialist_id,
                        'title' => __('Patient No-Show'),
                        'message' => __('The patient :patient did not join the scheduled session on :date and has been marked as no-show.', [
                            'patient' => Auth::user()->name,
                            'date' => $sessionTime->translatedFormat('l, F d, Y \a\t h:i A')
                        ]),
                        'type' => 'session_reminder',
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);

                    return redirect()->route('patient.sessions')
                        ->with('error', __('You missed your session. It has been marked as no-show.'));
                }
            }

            // Session has ended
            return redirect()->route('patient.sessions.show', $session->id)
                ->with('error', __('This session has already ended. You cannot join it now.'));
        }

        // Check if session is cancelled or completed
        if ($session->status === 'cancelled') {
            return redirect()->route('patient.sessions.show', $session->id)
                ->with('error', __('This session has been cancelled.'));
        }

        if ($session->status === 'completed') {
            return redirect()->route('patient.sessions.show', $session->id)
                ->with('error', __('This session has already been completed.'));
        }

        // For text sessions, redirect to chat
        if ($session->session_type === 'text') {
            return redirect()->route('patient.chat.show', $session->specialist_id);
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

        $freeRewardName = $this->getFreeRewardName($session);

        return view('patient.sessions.join', compact('session', 'freeRewardName'));
    }

    /**
     * Cancel session (AJAX).
     */
    public function cancel(Request $request, $sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->where('status', 'scheduled')
            ->with(['specialist', 'specialist.specialistProfile'])
            ->firstOrFail();

        $sessionDateTime = Carbon::parse($session->session_datetime);
        $hoursDifference = abs($sessionDateTime->diffInHours(now()));

        // Check if cancellation is allowed (at least 24 hours before)
        if ($hoursDifference < 24) {
            return response()->json([
                'success' => false,
                'message' => __('Sessions can only be cancelled at least 24 hours in advance.')
            ], 422);
        }

        $session->status = 'cancelled';
        $session->save();

        $refundAmount = 0;

        // Refund if paid by credit (not for free sessions)
        if ($session->is_paid_by_credit && !$session->is_free) {
            $fee = $session->specialist->specialistProfile->consultation_fee;

            // Apply discount based on session type
            if ($session->session_type === 'audio') {
                $fee = $fee * 0.9;
            } elseif ($session->session_type === 'text') {
                $fee = $fee * 0.8;
            }

            $user = Auth::user();
            $user->credit_balance += $fee;
            $user->save();
            $refundAmount = $fee;
        }

        // Create notification for patient
        Notification::create([
            'user_id' => Auth::id(),
            'title' => __('Session Cancelled'),
            'message' => __('Your :type session with :specialist has been cancelled.', [
                'type' => __($session->session_type),
                'specialist' => $session->specialist->name
            ]),
            'type' => 'session_reminder',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        // Create notification for specialist
        Notification::create([
            'user_id' => $session->specialist_id,
            'title' => __('Session Cancelled'),
            'message' => __(':patient has cancelled their :type session with you.', [
                'patient' => Auth::user()->name,
                'type' => __($session->session_type)
            ]),
            'type' => 'session_reminder',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Session cancelled successfully.'),
            'refund_amount' => $refundAmount,
            'session' => [
                'id' => $session->id,
                'status' => $session->status,
                'status_text' => __('Cancelled'),
                'status_badge_class' => 'cancelled',
                'can_cancel' => false
            ]
        ]);
    }

    /**
     * Rate session specialist (AJAX).
     */
    public function rate(Request $request, $sessionId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->where('status', 'completed')
            ->with(['specialist', 'specialist.specialistProfile'])
            ->firstOrFail();

        // Check if already reviewed
        if ($session->review) {
            return response()->json([
                'success' => false,
                'message' => __('You have already rated this session.')
            ], 422);
        }

        // Create review
        $review = Review::create([
            'session_id' => $session->id,
            'reviewer_id' => Auth::id(),
            'specialist_id' => $session->specialist_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update specialist average rating
        $specialistProfile = $session->specialist->specialistProfile;
        $avgRating = Review::where('specialist_id', $session->specialist_id)->avg('rating');
        $specialistProfile->rating_avg = $avgRating;
        $specialistProfile->save();

        // Award points for rating (3 points) - only if not a free session
        if (!$session->is_free) {
            Auth::user()->addPoints(3, 'specialist_rating', __('Rated specialist'), $review->id, Review::class);
        }

        // Create notification for specialist
        Notification::create([
            'user_id' => $session->specialist_id,
            'title' => __('New Rating'),
            'message' => __(':patient rated you :rating stars for your session.', [
                'patient' => Auth::user()->name,
                'rating' => $request->rating
            ]),
            'type' => 'session_reminder',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $session->is_free
                ? __('Thank you for rating!')
                : __('Thank you for rating! You earned 3 Tamman Points.'),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);
    }

    /**
     * Get upcoming sessions count for AJAX.
     */
    public function getUpcomingCount()
    {
        $count = TherapySession::where('patient_id', Auth::id())
            ->where('status', 'scheduled')
            ->where('session_datetime', '>', now())
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Check if session can be joined (AJAX helper).
     */
    public function checkJoinStatus($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'can_join' => false]);
        }

        $sessionTime = Carbon::parse($session->session_datetime);
        $now = now();
        $minutesUntil = $now->diffInMinutes($sessionTime, false);
        $canJoin = $minutesUntil <= 15 && $minutesUntil >= -60 && $session->status === 'scheduled';

        return response()->json([
            'success' => true,
            'can_join' => $canJoin,
            'minutes_until' => max(0, $minutesUntil),
            'session_time' => $sessionTime->format('Y-m-d H:i:s'),
            'status' => $session->status,
            'is_free' => $session->is_free
        ]);
    }

    /**
     * Mark session as ongoing (AJAX)
     */
    public function markOngoing($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->first();

        if ($session && $session->status === 'scheduled') {
            $session->status = 'ongoing';
            $session->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 422);
    }

    /**
     * Check and mark session as expired if not joined (AJAX)
     */
    public function checkExpiry($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->with(['specialist'])
            ->first();

        if (!$session) {
            return response()->json(['success' => false], 404);
        }

        $sessionEndTime = Carbon::parse($session->session_datetime)->addMinutes($session->duration_minutes);

        if ($session->status === 'scheduled' && now()->greaterThan($sessionEndTime)) {
            $session->status = 'no_show';
            $session->save();

            Notification::create([
                'user_id' => $session->patient_id,
                'title' => __('Session Marked as No-Show'),
                'message' => __('You missed your session with :specialist. It has been marked as no-show.', [
                    'specialist' => $session->specialist->name
                ]),
                'type' => 'session_reminder',
                'is_read' => false,
                'sent_at' => now(),
            ]);

            return response()->json(['success' => true, 'status' => 'no_show']);
        }

        return response()->json(['success' => true, 'status' => $session->status]);
    }

    /**
     * Check if patient can join the session
     * 
     * @param int $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function canJoinSession($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        // Text sessions are handled via chat, not video join
        if ($session->session_type === 'text') {
            return response()->json([
                'can_join' => false,
                'reason' => 'text_session',
                'redirect_url' => route('chat.index', ['user' => $session->specialist_id]),
                'message' => __('This is a text session. Redirecting to chat...')
            ]);
        }

        // Check if session is already full (both have joined)
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

        // Patient can join (first time OR rejoin)
        return response()->json([
            'can_join' => true,
            'reason' => $session->patient_joined ? 'rejoin' : 'first_join',
            'message' => $session->patient_joined ? __('You are rejoining the session.') : __('You can join the session.')
        ]);
    }

    /**
     * Register that patient has joined the session
     * 
     * @param int $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerJoin($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        // Always update the joined timestamp on rejoin
        if (!$session->patient_joined) {
            $session->patient_joined = true;
        }
        $session->patient_joined_at = now();
        $session->save();

        return response()->json([
            'success' => true,
            'user_type' => 'patient',
            'session_full' => $session->isFull()
        ]);
    }

    /**
     * Get payment method display text
     */
    private function getPaymentMethodText($session)
    {
        if ($session->is_free) {
            return __('Free Session (Reward)');
        }
        if ($session->is_paid_by_credit) {
            return __('Credit Balance');
        }
        return __('Cash / Bank Transfer');
    }

    /**
     * Get free reward name
     */
    private function getFreeRewardName($session)
    {
        if (!$session->is_free || !$session->rewardRedemption || !$session->rewardRedemption->reward) {
            return null;
        }

        $rewardName = $session->rewardRedemption->reward->name;
        if (is_string($rewardName) && str_starts_with($rewardName, '{')) {
            $decoded = json_decode($rewardName, true);
            $locale = app()->getLocale();
            return $decoded[$locale] ?? $decoded['en'] ?? __('Free Session');
        }

        return $rewardName;
    }

    /**
     * Get session type icon
     */
    private function getSessionTypeIcon($type)
    {
        return match ($type) {
            'video' => 'fa-video',
            'audio' => 'fa-phone-alt',
            'text' => 'fa-comment-dots',
            default => 'fa-calendar',
        };
    }

    /**
     * Get session type color
     */
    private function getSessionTypeColor($type)
    {
        return match ($type) {
            'video' => '#7c3aed',
            'audio' => '#10b981',
            'text' => '#f59e0b',
            default => '#6b7280',
        };
    }

    /**
     * Get status badge class
     */
    private function getStatusBadgeClass($status)
    {
        return match ($status) {
            'scheduled' => 'scheduled',
            'ongoing' => 'ongoing',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'no_show' => 'no-show',
            default => 'scheduled',
        };
    }

    /**
     * Get status icon
     */
    private function getStatusIcon($status)
    {
        return match ($status) {
            'scheduled' => 'fa-clock',
            'ongoing' => 'fa-play-circle',
            'completed' => 'fa-check-circle',
            'cancelled' => 'fa-times-circle',
            'no_show' => 'fa-user-slash',
            default => 'fa-info-circle',
        };
    }

    /**
     * Get status text
     */
    private function getStatusText($status)
    {
        return match ($status) {
            'scheduled' => __('Scheduled'),
            'ongoing' => __('Ongoing'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
            'no_show' => __('No Show'),
            default => ucfirst($status),
        };
    }
}
