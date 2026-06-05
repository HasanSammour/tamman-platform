<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TherapySession;
use App\Models\Availability;
use App\Models\RewardRedemption;
use App\Models\Conversation;
use App\Models\Message;
use App\Mail\BookingConfirmationMail;
use App\Mail\FreeSessionRedeemedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\EmailHelper;
use App\Helpers\NotificationHelper;

class BookingController extends Controller
{
    /**
     * Show booking form for a specialist
     */
    public function book($specialistId)
    {
        $specialist = User::where('id', $specialistId)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'specialist');
            })
            ->with('specialistProfile')
            ->firstOrFail();

        if (!$specialist->specialistProfile || !$specialist->specialistProfile->is_verified) {
            return redirect()->route('specialists.index')
                ->with('error', __('This specialist is not yet verified to conduct sessions.'));
        }

        $user = Auth::user();
        $creditBalance = $user->credit_balance;

        // Get pre-selected session type from URL parameter
        $preSelectedType = request()->get('type');
        $validTypes = ['video', 'audio', 'text'];
        $preSelectedType = in_array($preSelectedType, $validTypes) ? $preSelectedType : null;

        // ==================== CHECK FOR PENDING FREE SESSION ====================
        $freeSessionTypes = [
            'video' => null,
            'audio' => null,
            'text' => null,
        ];
        $hasAnyFreeSession = false;

        // Check for each session type if user has a pending free redemption
        foreach (['video', 'audio', 'text'] as $type) {
            $redemption = RewardRedemption::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereHas('reward', function ($q) use ($type) {
                    $q->where('type', 'free_session')
                        ->where('session_type', $type);
                })
                ->whereDoesntHave('therapySession')
                ->first();

            if ($redemption) {
                $freeSessionTypes[$type] = $redemption;
                $hasAnyFreeSession = true;
            }
        }

        // Prepare session types with prices
        $baseFee = $specialist->specialistProfile->consultation_fee;

        $sessionTypes = [
            'video' => [
                'name' => __('Video Session'),
                'icon' => 'fa-video',
                'price' => $baseFee,
                'description' => __('Face-to-face video call therapy session'),
                'color' => '#7c3aed',
                'has_free' => $freeSessionTypes['video'] !== null,
                'free_redemption_id' => $freeSessionTypes['video']?->id,
            ],
            'audio' => [
                'name' => __('Audio Session'),
                'icon' => 'fa-phone-alt',
                'price' => round($baseFee * 0.9, 2),
                'description' => __('Voice-only call therapy session'),
                'color' => '#10b981',
                'has_free' => $freeSessionTypes['audio'] !== null,
                'free_redemption_id' => $freeSessionTypes['audio']?->id,
            ],
            'text' => [
                'name' => __('Text Chat Session'),
                'icon' => 'fa-comment-dots',
                'price' => round($baseFee * 0.8, 2),
                'description' => __('Real-time text chat therapy session'),
                'color' => '#f59e0b',
                'has_free' => $freeSessionTypes['text'] !== null,
                'free_redemption_id' => $freeSessionTypes['text']?->id,
            ],
        ];

        return view('patient.bookings.book', compact(
            'specialist',
            'sessionTypes',
            'creditBalance',
            'hasAnyFreeSession',
            'preSelectedType'
        ));
    }

    /**
     * Get available slots via AJAX
     */
    public function getAvailableSlotsAjax(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required|exists:users,id',
            'days' => 'nullable|integer|min:1|max:30',
        ]);

        $specialistId = $request->specialist_id;
        $days = (int) ($request->days ?? 14);

        $availableSlots = $this->getAvailableSlots($specialistId, $days);

        return response()->json([
            'success' => true,
            'slots' => $availableSlots
        ]);
    }

    /**
     * Store booking
     */
    public function store(Request $request)
    {
        // DEBUG: Log the entire request
        // ! Note The problem at end was That i am not add new therapy session columns to Model fillable. :(

        \Log::info('=== BOOKING REQUEST START ===');
        \Log::info('Request Data:', $request->all());

        $request->validate([
            'specialist_id' => 'required|exists:users,id',
            'session_datetime' => 'required|date|after:now',
            'session_type' => 'required|in:video,audio,text',
            'payment_method' => 'required|in:credit,free',
            'free_redemption_id' => 'nullable|exists:reward_redemptions,id',
        ]);

        $user = Auth::user();
        $specialist = User::findOrFail($request->specialist_id);
        $sessionDateTime = Carbon::parse($request->session_datetime)->setTimezone('Asia/Gaza');

        \Log::info('User ID: ' . $user->id);
        \Log::info('Specialist ID: ' . $specialist->id);
        \Log::info('Session DateTime: ' . $sessionDateTime);
        \Log::info('Payment Method: ' . $request->payment_method);
        \Log::info('Free Redemption ID from request: ' . $request->free_redemption_id);

        // Check if slot is still available
        if (!$this->isSlotAvailable($request->specialist_id, $sessionDateTime)) {
            \Log::error('Slot not available');
            return response()->json([
                'success' => false,
                'message' => __('This time slot is no longer available. Please choose another time.')
            ], 422);
        }

        $baseFee = $specialist->specialistProfile->consultation_fee;
        $fee = $this->calculateFee($baseFee, $request->session_type);

        $isPaidByCredit = false;
        $paymentMethodText = '';
        $isFreeSession = false;
        $freeRedemption = null;

        // Check for free session redemption
        if ($request->payment_method === 'free') {
            \Log::info('Processing FREE session...');

            // First try to get using the provided ID
            if ($request->free_redemption_id) {
                \Log::info('Looking for redemption with ID: ' . $request->free_redemption_id);

                $freeRedemption = RewardRedemption::where('id', $request->free_redemption_id)
                    ->where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->whereHas('reward', function ($q) use ($request) {
                        $q->where('type', 'free_session')
                            ->where('session_type', $request->session_type);
                    })
                    ->whereDoesntHave('therapySession')
                    ->first();

                if ($freeRedemption) {
                    \Log::info('Found redemption by ID: ' . $freeRedemption->id);
                } else {
                    \Log::warning('Redemption NOT found by ID: ' . $request->free_redemption_id);
                }
            }

            // If not found by ID, try to find any available for this type
            if (!$freeRedemption) {
                \Log::info('Trying to find ANY available free redemption for type: ' . $request->session_type);

                $freeRedemption = RewardRedemption::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->whereHas('reward', function ($q) use ($request) {
                        $q->where('type', 'free_session')
                            ->where('session_type', $request->session_type);
                    })
                    ->whereDoesntHave('therapySession')
                    ->first();

                if ($freeRedemption) {
                    \Log::info('Found ANY redemption: ' . $freeRedemption->id);
                } else {
                    \Log::warning('NO free redemption found for type: ' . $request->session_type);
                }
            }

            if ($freeRedemption) {
                $isFreeSession = true;
                $fee = 0;
                $paymentMethodText = __('Free Session (Reward)');
                \Log::info('Free session applied! Redemption ID: ' . $freeRedemption->id);
            } else {
                \Log::error('Free session requested but no valid redemption found');
                return response()->json([
                    'success' => false,
                    'message' => __('No free session available. Please choose another payment method.')
                ], 422);
            }
        }

        // Process credit payment if not free
        if (!$isFreeSession && $request->payment_method === 'credit') {
            \Log::info('Processing CREDIT payment. Fee: ' . $fee);
            if ($user->credit_balance < $fee) {
                \Log::error('Insufficient credit balance. Balance: ' . $user->credit_balance . ', Fee: ' . $fee);
                return response()->json([
                    'success' => false,
                    'message' => __('Insufficient credit balance. Please add funds to continue.')
                ], 422);
            }
            $user->credit_balance -= $fee;
            $user->save();
            $isPaidByCredit = true;
            $paymentMethodText = __('Credit Balance');
        }

        // Generate secure, unique Jitsi meeting link (only for video/audio)
        $meetingLink = null;
        $secureRoomName = null;
        if ($request->session_type !== 'text') {
            // We don't have session ID yet, so use timestamp + random
            $timestamp = $sessionDateTime->timestamp;
            $randomBytes = bin2hex(random_bytes(32)); // 64 characters
            $secureRoomName = 'tamman-' . $timestamp . '-' . $randomBytes;
            $meetingLink = 'https://meet.jit.si/' . $secureRoomName;
        }

        // Create session
        \Log::info('Creating session with:');
        \Log::info('reward_redemption_id: ' . ($freeRedemption?->id ?? 'NULL'));
        \Log::info('is_free: ' . ($isFreeSession ? 'true' : 'false'));

        $session = TherapySession::create([
            'patient_id' => $user->id,
            'specialist_id' => $request->specialist_id,
            'session_datetime' => $sessionDateTime,
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'session_type' => $request->session_type,
            'meeting_link' => $meetingLink,
            'secure_room_name' => $secureRoomName,
            'is_paid_by_credit' => $isPaidByCredit,
            'is_free' => $isFreeSession,
            'reward_redemption_id' => $freeRedemption?->id,
            'notes' => $request->notes ?? null,
        ]);

        \Log::info('Session created with ID: ' . $session->id);
        \Log::info('Session reward_redemption_id in DB: ' . $session->reward_redemption_id);

        // Create or get conversation between patient and specialist
        $conversation = Conversation::where(function ($q) use ($user, $specialist) {
            $q->where('participant_one', $user->id)->where('participant_two', $specialist->id);
        })->orWhere(function ($q) use ($user, $specialist) {
            $q->where('participant_one', $specialist->id)->where('participant_two', $user->id);
        })->first();

        if (!$conversation) {
            $participantOne = min($user->id, $specialist->id);
            $participantTwo = max($user->id, $specialist->id);

            $conversation = Conversation::create([
                'participant_one' => $participantOne,
                'participant_two' => $participantTwo,
                'is_locked' => true,
                'locked_at' => now(),
                'therapy_session_id' => $session->id,
                'is_text_session' => ($request->session_type === 'text'),
                'last_message' => __('📅 Session booked for :date (:type)', [
                        'date' => $sessionDateTime->translatedFormat('l, M d, Y h:i A'),
                        'type' => __(ucfirst($request->session_type))
                    ]),
                'last_message_at' => now(),
            ]);
        } else {
            // Update existing conversation with new session info
            $conversation->update([
                'therapy_session_id' => $session->id,
                'is_text_session' => ($request->session_type === 'text'),
                'is_locked' => true,
                'locked_at' => now(),
            ]);
        }

        // Create system message about booking
        $systemMessage = Message::create([
            'sender_id' => $specialist->id,
            'receiver_id' => $user->id,
            'conversation_id' => $conversation->id,
            'content' => __('📅 Session booked for :date (:type)', [
                    'date' => $sessionDateTime->translatedFormat('l, M d, Y h:i A'),
                    'type' => __(ucfirst($request->session_type))
                ]),
            'is_system_message' => true,
            'sent_at' => now(),
        ]);

        // Update conversation last message
        $conversation->updateLastMessage($systemMessage);

        // Update session with conversation_id
        $session->update(['conversation_id' => $conversation->id]);

        // Award points for booking (only if not free)
        if (!$isFreeSession) {
            $user->addPoints(5, 'booking', __('Session booked'), $session->id, TherapySession::class);
        }

        // Mark free redemption as used
        if ($freeRedemption) {
            \Log::info('Marking redemption ' . $freeRedemption->id . ' as used');

            $freeRedemption->update([
                'metadata' => array_merge($freeRedemption->metadata ?? [], [
                    'session_id' => $session->id,
                    'used_at' => now()->toDateTimeString(),
                ])
            ]);

            // Also update the redemption status to ensure it's completed
            if ($freeRedemption->status !== 'completed') {
                $freeRedemption->markAsCompleted();
                \Log::info('Redemption status changed to completed');
            }

            // Verify the update worked
            $updatedRedemption = RewardRedemption::find($freeRedemption->id);
            \Log::info('Redemption after update - metadata: ' . json_encode($updatedRedemption->metadata));

            // Send free session email
            $rewardName = $freeRedemption->reward->getName();
            EmailHelper::sendInUserLanguage($user, new FreeSessionRedeemedMail($user, $rewardName, $request->session_type, $freeRedemption->points_spent));
        }

        // Send confirmation emails
        EmailHelper::sendInUserLanguage($user, new BookingConfirmationMail($user, $session, $paymentMethodText, $fee));
        EmailHelper::sendInUserLanguage($specialist, new BookingConfirmationMail($specialist, $session, $paymentMethodText, $fee));

        // Create notifications for patient
        if ($user->wantsNotification('session_reminders')) {
            NotificationHelper::send(
                $user->id,
                __('Session Booked'),
                __('Your :type session with :specialist has been booked for :datetime.', [
                    'type' => __($request->session_type),
                    'specialist' => $specialist->name,
                    'datetime' => $sessionDateTime->translatedFormat('l, M d, Y h:i A')
                ]),
                'session_reminder'
            );
        }

        // Create notifications for specialist
        if ($specialist->wantsNotification('session_reminders')) {
            NotificationHelper::send(
                $specialist->id,
                __('New Session Booked'),
                __(':patient has booked a :type session with you for :datetime.', [
                    'patient' => $user->name,
                    'type' => __($request->session_type),
                    'datetime' => $sessionDateTime->translatedFormat('l, M d, Y h:i A')
                ]),
                'session_reminder'
            );
        }

        \Log::info('=== BOOKING REQUEST END ===');

        return response()->json([
            'success' => true,
            'message' => __('Session booked successfully!'),
            'redirect_url' => route('patient.bookings.confirmation', $session->id)
        ]);
    }

    /**
     * Show booking confirmation
     */
    public function confirmation($sessionId)
    {
        $session = TherapySession::where('id', $sessionId)
            ->where('patient_id', Auth::id())
            ->with(['specialist', 'specialist.specialistProfile', 'rewardRedemption.reward'])
            ->firstOrFail();

        $paymentMethodText = $this->getPaymentMethodText($session);
        $statusBadgeClass = $this->getStatusBadgeClass($session->status);
        $canJoin = $this->canJoinSession($session);
        $canCancel = $this->canCancelSession($session);
        $joinTime = Carbon::parse($session->session_datetime)->subMinutes(15);

        // Get free session reward name if applicable
        $freeRewardName = null;
        if ($session->is_free && $session->rewardRedemption && $session->rewardRedemption->reward) {
            $freeRewardName = $session->rewardRedemption->reward->getName();
        }

        return view('patient.bookings.confirmation', compact(
            'session',
            'paymentMethodText',
            'statusBadgeClass',
            'canJoin',
            'canCancel',
            'joinTime',
            'freeRewardName'
        ));
    }


    // /**
    //  * Cancel booking
    //  */
    // public function cancel($sessionId)
    // {
    //     $session = TherapySession::where('id', $sessionId)
    //         ->where('patient_id', Auth::id())
    //         ->where('status', 'scheduled')
    //         ->with(['specialist', 'specialist.specialistProfile'])
    //         ->firstOrFail();

    //     $sessionDateTime = Carbon::parse($session->session_datetime);
    //     $hoursDifference = abs(now()->diffInHours($sessionDateTime));

    //     if ($hoursDifference < 24) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => __('Sessions can only be cancelled at least 24 hours in advance.')
    //         ], 422);
    //     }

    //     $session->status = 'cancelled';
    //     $session->save();

    //     $refundAmount = 0;

    //     // Refund if paid by credit (not for free sessions)
    //     if ($session->is_paid_by_credit && !$session->is_free) {
    //         $refundAmount = $this->calculateFee(
    //             $session->specialist->specialistProfile->consultation_fee,
    //             $session->session_type
    //         );
    //         $user = Auth::user();
    //         $user->credit_balance += $refundAmount;
    //         $user->save();
    //     }

    //     // Send cancellation emails
    //     EmailHelper::sendInUserLanguage(Auth::user(), new BookingCancellationMail(Auth::user(), $session, $refundAmount));
    //     EmailHelper::sendInUserLanguage($session->specialist, new BookingCancellationMail($session->specialist, $session, $refundAmount));

    //     // Create notifications for patient
    //     $patient = Auth::user();
    //     if ($patient->wantsNotification('session_reminders')) {
    //         NotificationHelper::send(
    //             $patient->id,
    //             __('Session Cancelled'),
    //             __('Your session with :specialist has been cancelled.', [
    //                 'specialist' => $session->specialist->name
    //             ]),
    //             'session_reminder'
    //         );
    //     }

    //     // Create notifications for specialist
    //     if ($session->specialist->wantsNotification('session_reminders')) {
    //         NotificationHelper::send(
    //             $session->specialist_id,
    //             __('Session Cancelled'),
    //             __(':patient has cancelled their session with you.', [
    //                 'patient' => $patient->name
    //             ]),
    //             'session_reminder'
    //         );
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => __('Session cancelled successfully.'),
    //         'redirect_url' => route('patient.sessions')
    //     ]);
    // }

    /**
     * Get available time slots for a specialist
     */
    private function getAvailableSlots($specialistId, int $days = 14)
    {
        $slots = [];
        $allSlotKeys = []; // Track unique slots to prevent duplicates
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($days);

        // Get all recurring availability (weekly schedule)
        $recurringAvailabilities = Availability::where('specialist_id', $specialistId)
            ->where('is_recurring', true)
            ->where('is_available', true)
            ->get();

        // Get all one-time availability (specific dates) - keyed by date
        $oneTimeAvailabilities = Availability::where('specialist_id', $specialistId)
            ->where('is_recurring', false)
            ->where('is_available', true)
            ->where('specific_date', '>=', $startDate)
            ->where('specific_date', '<=', $endDate)
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->specific_date)->format('Y-m-d');
            });

        // Get all blocked dates (unavailable days)
        $blockedDates = Availability::where('specialist_id', $specialistId)
            ->where('is_recurring', false)
            ->where('is_available', false)
            ->where('specific_date', '>=', $startDate)
            ->where('specific_date', '<=', $endDate)
            ->get()
            ->pluck('specific_date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Get booked sessions
        $bookedSessions = TherapySession::where('specialist_id', $specialistId)
            ->where('status', 'scheduled')
            ->where('session_datetime', '>=', $startDate)
            ->where('session_datetime', '<=', $endDate)
            ->get()
            ->pluck('session_datetime')
            ->map(function ($datetime) {
                return Carbon::parse($datetime)->format('Y-m-d H:i:00');
            })
            ->toArray();

        // Process each day in the date range
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek;

            // Check if this day is blocked
            $isBlocked = in_array($dateKey, $blockedDates);

            // Check if there are one-time slots for this day
            $hasOneTimeSlots = $oneTimeAvailabilities->has($dateKey);

            // Store temporary slots for this day to deduplicate
            $daySlots = [];

            // ==================== RULE 1: Blocked day with NO one-time slots ====================
            if ($isBlocked && !$hasOneTimeSlots) {
                continue;
            }

            // ==================== RULE 2: Blocked day WITH one-time slots ====================
            if ($isBlocked && $hasOneTimeSlots) {
                $oneTimeSlots = $oneTimeAvailabilities->get($dateKey);
                foreach ($oneTimeSlots as $availability) {
                    $this->collectTimeSlots($availability, $date, $bookedSessions, $daySlots);
                }
            }

            // ==================== RULES 3 & 4: Normal day (not blocked) ====================
            if (!$isBlocked) {
                // Add recurring slots for this day
                $recurringSlots = $recurringAvailabilities->filter(function ($slot) use ($dayOfWeek) {
                    return $slot->day_of_week == $dayOfWeek;
                });

                foreach ($recurringSlots as $availability) {
                    $this->collectTimeSlots($availability, $date, $bookedSessions, $daySlots);
                }

                // Add one-time slots for this day (if any)
                if ($hasOneTimeSlots) {
                    $oneTimeSlots = $oneTimeAvailabilities->get($dateKey);
                    foreach ($oneTimeSlots as $availability) {
                        $this->collectTimeSlots($availability, $date, $bookedSessions, $daySlots);
                    }
                }
            }

            // Deduplicate slots for this day (remove duplicate times)
            $uniqueSlots = [];
            $seenTimes = [];

            foreach ($daySlots as $slot) {
                $timeKey = $slot['time']; // e.g., "09:00 AM"
                if (!in_array($timeKey, $seenTimes)) {
                    $seenTimes[] = $timeKey;
                    $uniqueSlots[] = $slot;
                }
            }

            // Sort slots by time
            usort($uniqueSlots, function ($a, $b) {
                return strtotime($a['datetime']) - strtotime($b['datetime']);
            });

            // Add to main slots array
            foreach ($uniqueSlots as $slot) {
                $slots[] = $slot;
            }
        }

        // Group slots by date for response
        $groupedSlots = [];
        foreach ($slots as $slot) {
            $dateKey = $slot['date'];
            if (!isset($groupedSlots[$dateKey])) {
                $groupedSlots[$dateKey] = [
                    'date' => $slot['date'],
                    'date_formatted' => $slot['date_formatted'],
                    'slots' => []
                ];
            }
            $groupedSlots[$dateKey]['slots'][] = $slot;
        }

        return array_values($groupedSlots);
    }

    /**
     * Collect time slots from availability without adding to main array
     */
    private function collectTimeSlots($availability, $date, $bookedSessions, &$daySlots)
    {
        $startTime = Carbon::parse($availability->start_time);
        $endTime = Carbon::parse($availability->end_time);
        $slotDuration = 60; // minutes

        while ($startTime->copy()->addMinutes($slotDuration) <= $endTime) {
            $slotDateTime = $date->copy()->setTimeFromTimeString($startTime->format('H:i:s'));
            $slotKey = $slotDateTime->format('Y-m-d H:i:00');

            // Only add if not already booked and in future
            if (!in_array($slotKey, $bookedSessions) && $slotDateTime->isAfter(now())) {
                $daySlots[] = [
                    'datetime' => $slotDateTime->copy(),
                    'date' => $slotDateTime->format('Y-m-d'),
                    'time' => $slotDateTime->format('h:i A'),
                    'date_formatted' => $slotDateTime->translatedFormat('l, M d'),
                    'available' => true,
                ];
            }
            $startTime->addMinutes($slotDuration);
        }
    }

    /**
     * Check if a specific time slot is still available
     */
    private function isSlotAvailable($specialistId, Carbon $datetime)
    {
        $existingBooking = TherapySession::where('specialist_id', $specialistId)
            ->where('session_datetime', $datetime)
            ->where('status', 'scheduled')
            ->exists();

        if ($existingBooking) {
            return false;
        }

        $dayOfWeek = $datetime->dayOfWeek;
        $time = $datetime->format('H:i:s');

        $availability = Availability::where('specialist_id', $specialistId)
            ->where('is_available', true)
            ->where(function ($q) use ($dayOfWeek, $datetime, $time) {
                $q->where(function ($sub) use ($dayOfWeek, $time) {
                    $sub->where('is_recurring', true)
                        ->where('day_of_week', $dayOfWeek)
                        ->where('start_time', '<=', $time)
                        ->where('end_time', '>=', $time);
                })->orWhere(function ($sub) use ($datetime, $time) {
                    $sub->where('is_recurring', false)
                        ->whereDate('specific_date', $datetime->toDateString())
                        ->where('start_time', '<=', $time)
                        ->where('end_time', '>=', $time);
                });
            })
            ->exists();

        return $availability;
    }

    /**
     * Calculate fee based on session type
     */
    private function calculateFee($baseFee, $sessionType)
    {
        switch ($sessionType) {
            case 'audio':
                return round($baseFee * 0.9, 2);
            case 'text':
                return round($baseFee * 0.8, 2);
            default:
                return $baseFee;
        }
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
     * Get session status badge class
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
     * Check if session can be joined
     */
    private function canJoinSession($session)
    {
        if ($session->status !== 'scheduled') {
            return false;
        }

        $sessionTime = Carbon::parse($session->session_datetime);
        $now = now();
        $minutesUntil = $now->diffInMinutes($sessionTime, false);

        // Can join 15 minutes before session starts
        return $minutesUntil <= 15 && $minutesUntil >= -60;
    }

    /**
     * Check if session can be cancelled
     */
    private function canCancelSession($session)
    {
        if ($session->status !== 'scheduled') {
            return false;
        }

        $sessionTime = Carbon::parse($session->session_datetime);
        return $sessionTime->diffInHours(now()) >= 24;
    }
}
