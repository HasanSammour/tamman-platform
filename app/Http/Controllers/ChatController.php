<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\TherapySession;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Events\MessageEdited;
use App\Events\MessageDeleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChatController extends Controller
{
    /**
     * Display chat index page
     */
    public function index($userId = null)
    {
        $user = Auth::user();
        $conversations = $this->getConversations($user);

        $targetConversation = null;
        $targetUserId = null;
        $errorMessage = null;

        // If user_id is passed, find that conversation
        if ($userId) {
            // Check if target user exists
            $targetUser = User::find($userId);

            if (!$targetUser) {
                $errorMessage = __('User not found.');
            } else {
                $targetConversation = Conversation::where(function ($q) use ($user, $userId) {
                    $q->where('participant_one', $user->id)->where('participant_two', $userId);
                })->orWhere(function ($q) use ($user, $userId) {
                    $q->where('participant_one', $userId)->where('participant_two', $user->id);
                })->first();

                if (!$targetConversation) {
                    $errorMessage = __('You have no conversation with this user yet. Book a session to start chatting.');
                } else {
                    $targetUserId = $userId;
                }
            }
        }

        return view('chat.index', compact('conversations', 'targetConversation', 'targetUserId', 'errorMessage'));
    }

    /**
     * Get all conversations (AJAX)
     */
    public function conversations()
    {
        $user = Auth::user();
        $conversations = $this->getConversations($user);

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
            'total_unread' => $this->getTotalUnreadCount($user),
        ]);
    }

    /**
     * Get messages for a specific conversation
     */
    public function show($conversationId)
    {
        $user = Auth::user();

        $conversation = Conversation::where(function ($q) use ($user, $conversationId) {
            $q->where('id', $conversationId)
                ->where(function ($sub) use ($user) {
                    $sub->where('participant_one', $user->id)
                        ->orWhere('participant_two', $user->id);
                });
        })->firstOrFail();

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $conversation->resetUnreadCount($user->id);

        // Get messages with pagination
        $messages = Message::where('conversation_id', $conversation->id)
            ->where(function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('sender_id', $user->id)->where('is_deleted_by_sender', false);
                })->orWhere(function ($sub) use ($user) {
                    $sub->where('receiver_id', $user->id)->where('is_deleted_by_receiver', false);
                });
            })
            ->with(['sender', 'receiver'])
            ->orderBy('sent_at', 'asc')
            ->paginate(50);

        $otherUser = $conversation->participant_one == $user->id
            ? $conversation->participantTwo
            : $conversation->participantOne;

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'is_locked' => $conversation->is_locked,
                'locked_at' => $conversation->locked_at,
                'can_send' => $this->canSendMessage($conversation, $user->id),
                'can_lock' => $user->hasRole('specialist') && !$conversation->is_locked,
                'can_unlock' => $user->hasRole('specialist') && $conversation->is_locked,
            ],
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->getProfileImageUrl(),
                'is_online' => $otherUser->isOnline(),
            ],
            'messages' => $messages->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'content' => $message->content,
                    'is_mine' => $message->sender_id == $user->id,
                    'is_read' => $message->is_read,
                    'is_system_message' => $message->is_system_message,
                    'is_deleted_for_everyone' => (bool) $message->is_deleted_for_everyone,
                    'sent_at' => $message->sent_at->diffForHumans(),
                    'sent_at_raw' => $message->sent_at->format('Y-m-d H:i:s'),
                    'edited_at' => $message->edited_at?->diffForHumans(),
                ];
            }),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    /**
     * Send a new message
     */
    public function send(Request $request)
    {
        \Log::info('Send message called', $request->all());

        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string|max:5000',
        ]);

        $user = Auth::user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        // Verify user is participant
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not part of this conversation.',
            ], 403);
        }

        // Check if can send (for patient - if conversation is locked)
        if (!$this->canSendMessage($conversation, $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot send messages in this conversation.',
            ], 403);
        }

        $receiverId = $conversation->getOtherParticipant($user->id);

        // Create message
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'conversation_id' => $conversation->id,
            'content' => $request->content,
            'sent_at' => now(),
        ]);

        \Log::info('Message created', ['id' => $message->id, 'content' => $message->content]);

        // Update conversation
        $conversation->updateLastMessage($message);
        $conversation->incrementUnreadCount($receiverId);

        // Broadcast
        broadcast(new MessageSent($message, $conversation))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'sent_at' => $message->sent_at->diffForHumans(),
            ],
            'conversation_id' => $conversation->id,
        ]);
    }

    /**
     * Edit a message
     */
    public function edit(Request $request, $messageId)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $user = Auth::user();
        $message = Message::where('id', $messageId)
            ->where('sender_id', $user->id)
            ->firstOrFail();

        if ($message->is_system_message) {
            return response()->json([
                'success' => false,
                'message' => 'System messages cannot be edited.',
            ], 403);
        }

        $message->update([
            'content' => $request->content,
            'edited_at' => now(),
        ]);

        $conversation = $message->conversation;

        // Update conversation last message if needed
        $lastMessage = Message::where('conversation_id', $conversation->id)
            ->orderBy('sent_at', 'desc')
            ->first();

        if ($lastMessage && $lastMessage->id == $message->id) {
            $conversation->updateLastMessage($message);
        }

        broadcast(new MessageEdited($message, $conversation));

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'edited_at' => $message->edited_at->diffForHumans(),
            ],
        ]);
    }

    /**
     * Delete message for me only
     */
    public function deleteForMe($messageId)
    {
        $user = Auth::user();
        $message = Message::where(function ($q) use ($user, $messageId) {
            $q->where('id', $messageId)
                ->where(function ($sub) use ($user) {
                    $sub->where('sender_id', $user->id)
                        ->orWhere('receiver_id', $user->id);
                });
        })->firstOrFail();

        $message->deleteForUser($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Message deleted for you',
        ]);
    }

    /**
     * Delete message for everyone
     */
    public function deleteForEveryone($messageId)
    {
        $user = Auth::user();
        $message = Message::where(function ($q) use ($user, $messageId) {
            $q->where('id', $messageId)
                ->where(function ($sub) use ($user) {
                    $sub->where('sender_id', $user->id)
                        ->orWhere('receiver_id', $user->id);
                });
        })->firstOrFail();

        $conversation = $message->conversation;

        // Check if within time limit (1 hour)
        if ($message->sent_at->diffInMinutes(now()) > 60) {
            return response()->json([
                'success' => false,
                'message' => 'Messages can only be deleted for everyone within 1 hour of sending.',
            ], 403);
        }

        $message->deleteForEveryone();

        broadcast(new MessageDeleted($message, $conversation, 'everyone'));

        return response()->json([
            'success' => true,
            'message' => 'Message deleted for everyone',
            'deleted_message' => [
                'id' => $message->id,
                'content' => $message->content,
                'is_deleted_for_everyone' => $message->is_deleted_for_everyone,
            ],
        ]);
    }

    /**
     * Get delete options for a message
     */
    public function getDeleteOptions($messageId)
    {
        $user = Auth::user();
        $message = Message::findOrFail($messageId);

        $canDeleteForEveryone = false;

        if (!$message->is_deleted_for_everyone) {
            $timeLimit = now()->subHour();
            if ($message->sent_at > $timeLimit) {
                $canDeleteForEveryone = true;
            }
        }

        return response()->json([
            'success' => true,
            'can_delete_for_me' => true,
            'can_delete_for_everyone' => $canDeleteForEveryone,
        ]);
    }

    /**
     * Mark all messages in conversation as read
     */
    public function markAsRead($conversationId)
    {
        $user = Auth::user();

        Message::where('conversation_id', $conversationId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $conversation = Conversation::find($conversationId);
        if ($conversation) {
            $conversation->resetUnreadCount($user->id);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Lock conversation (Specialist only - ends session)
     */
    public function lockConversation($conversationId)
    {
        $user = Auth::user();

        if (!$user->hasRole('specialist')) {
            return response()->json([
                'success' => false,
                'message' => 'Only specialists can lock conversations.',
            ], 403);
        }

        $conversation = Conversation::findOrFail($conversationId);

        if (!$conversation->hasParticipant($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not part of this conversation.',
            ], 403);
        }

        if ($conversation->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation is already locked.',
            ], 400);
        }

        $conversation->lock();

        // Add system message
        $receiverId = $conversation->getOtherParticipant($user->id);

        \Log::info('LockConversation called', [
            'conversation_id' => $conversationId,
            'user_id' => $user->id,
            'user_role' => $user->getRoleNames()->first(),
        ]);

        $systemMessage = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'conversation_id' => $conversation->id,
            'content' => __('The specialist has ended this session - You can view messages but cannot send new ones'),
            'is_system_message' => true,
            'sent_at' => now(),
        ]);

        $conversation->updateLastMessage($systemMessage);

        \Log::info('System message created', [
            'message_id' => $systemMessage->id,
            'content' => $systemMessage->content,
            'is_system_message' => $systemMessage->is_system_message,
        ]);

        broadcast(new MessageSent($systemMessage, $conversation));
        \Log::info('Broadcast sent for lock message');

        return response()->json([
            'success' => true,
            'message' => 'Conversation locked successfully.',
            'is_locked' => true,
            'system_message' => [
                'id' => $systemMessage->id,
                'content' => $systemMessage->content,
                'is_system_message' => true,
            ],
        ]);
    }

    /**
     * Unlock conversation (Specialist only - reopens session)
     */
    public function unlockConversation($conversationId)
    {
        $user = Auth::user();

        if (!$user->hasRole('specialist')) {
            return response()->json([
                'success' => false,
                'message' => 'Only specialists can unlock conversations.',
            ], 403);
        }

        $conversation = Conversation::findOrFail($conversationId);

        if (!$conversation->hasParticipant($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not part of this conversation.',
            ], 403);
        }

        if (!$conversation->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation is not locked.',
            ], 400);
        }

        $conversation->unlock();

        // Add system message
        $receiverId = $conversation->getOtherParticipant($user->id);

        $systemMessage = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'conversation_id' => $conversation->id,
            'content' => __('The specialist has reopened this session - You can now send messages again'),
            'is_system_message' => true,
            'sent_at' => now(),
        ]);

        $conversation->updateLastMessage($systemMessage);

        broadcast(new MessageSent($systemMessage, $conversation));

        return response()->json([
            'success' => true,
            'message' => 'Conversation unlocked successfully.',
            'is_locked' => false,
            'system_message' => [
                'id' => $systemMessage->id,
                'content' => $systemMessage->content,
                'is_system_message' => true,
            ],
        ]);
    }

    /**
     * Broadcast typing indicator
     */
    public function typing(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'is_typing' => 'required|boolean',
        ]);

        $user = Auth::user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        broadcast(new UserTyping($user, $conversation, $request->is_typing))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Get total unread messages count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'unread_count' => $this->getTotalUnreadCount($user),
        ]);
    }

    /**
     * Get conversation session info for header badge
     */
    public function getConversationSessionInfo($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Check if there's an upcoming text session for this conversation
        $upcomingSession = TherapySession::where(function ($q) use ($conversation) {
            $q->where('patient_id', $conversation->participant_one)
                ->where('specialist_id', $conversation->participant_two);
        })->orWhere(function ($q) use ($conversation) {
            $q->where('patient_id', $conversation->participant_two)
                ->where('specialist_id', $conversation->participant_one);
        })->where('session_type', 'text')
            ->where('session_datetime', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('session_datetime', 'asc')
            ->first();

        // Check if there's an active text session (started but not ended)
        $activeSession = TherapySession::where(function ($q) use ($conversation) {
            $q->where('patient_id', $conversation->participant_one)
                ->where('specialist_id', $conversation->participant_two);
        })->orWhere(function ($q) use ($conversation) {
            $q->where('patient_id', $conversation->participant_two)
                ->where('specialist_id', $conversation->participant_one);
        })->where('session_type', 'text')
            ->where('session_datetime', '<=', now())
            ->where('session_datetime', '>', now()->subHours(2))
            ->where('status', 'scheduled')
            ->first();

        if ($activeSession) {
            return response()->json([
                'type' => 'active',
                'text' => __('Active Session'),
                'session_time' => Carbon::parse($activeSession->session_datetime)->format('h:i A'),
                'color' => '#10b981',
                'bg' => '#d1fae5'
            ]);
        }

        if ($upcomingSession) {
            return response()->json([
                'type' => 'upcoming',
                'text' => __('Upcoming Session'),
                'session_time' => Carbon::parse($upcomingSession->session_datetime)->translatedFormat('M d, h:i A'),
                'color' => '#f59e0b',
                'bg' => '#fef3c7'
            ]);
        }

        return response()->json([
            'type' => 'general',
            'text' => __('General Chat'),
            'color' => '#6b7280',
            'bg' => '#f3f4f6'
        ]);
    }

    /**
     * Check if user can send messages
     */
    private function canSendMessage($conversation, $userId)
    {
        $user = User::find($userId);

        // Specialist can always send (regardless of lock)
        if ($user->hasRole('specialist')) {
            return true;
        }

        // Patient can send only if conversation is NOT locked
        if ($user->hasRole('patient')) {
            // Check if conversation is locked
            if ($conversation->is_locked) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Get all conversations for user
     */
    private function getConversations($user)
    {
        $conversations = Conversation::where('participant_one', $user->id)
            ->orWhere('participant_two', $user->id)
            ->with(['participantOne', 'participantTwo'])
            ->get()
            ->map(function ($conversation) use ($user) {
                $otherUser = $conversation->participant_one == $user->id
                    ? $conversation->participantTwo
                    : $conversation->participantOne;

                // Get upcoming text session info for badge
                $upcomingSession = $conversation->getUpcomingTextSession();
                $hasUpcomingSession = !is_null($upcomingSession);
                $badgeText = null;
                $badgeClass = null;

                if ($hasUpcomingSession) {
                    $badgeText = __('Session') . ': ' . $upcomingSession->session_datetime->translatedFormat('M d, h:i A');
                    $badgeClass = 'upcoming-session-badge';
                }

                return [
                    'id' => $conversation->id,
                    'is_locked' => $conversation->is_locked,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'avatar' => $otherUser->getProfileImageUrl(),
                        'is_online' => $otherUser->isOnline(),
                    ],
                    'last_message' => $conversation->last_message,
                    'last_message_at' => $conversation->last_message_at?->diffForHumans(),
                    'last_message_raw' => $conversation->last_message_at?->toIso8601String(),
                    'unread_count' => $conversation->getUnreadCountForUser($user->id),
                    'can_send' => $this->canSendMessage($conversation, $user->id),
                    'can_lock' => $user->hasRole('specialist') && !$conversation->is_locked,
                    'can_unlock' => $user->hasRole('specialist') && $conversation->is_locked,
                    'has_upcoming_session' => $hasUpcomingSession,
                    'badge_text' => $badgeText,
                    'badge_class' => $badgeClass,
                    'upcoming_session_time' => $hasUpcomingSession ? $upcomingSession->session_datetime : null,
                ];
            });

        // Sort: Upcoming text sessions first, then by last message time
        $sorted = $conversations->sortByDesc(function ($conv) {
            // Priority 1: Has upcoming text session
            $priority = $conv['has_upcoming_session'] ? 2 : 0;

            // Priority 2: Add timestamp to sort within same priority
            $timestamp = $conv['upcoming_session_time'] ? $conv['upcoming_session_time']->timestamp : 0;

            return $priority * 1000000000 + $timestamp;
        })->values();

        return $sorted;
    }

    /**
     * Get total unread count for user
     */
    private function getTotalUnreadCount($user)
    {
        $conversations = Conversation::where('participant_one', $user->id)
            ->orWhere('participant_two', $user->id)
            ->get();

        $total = 0;
        foreach ($conversations as $conversation) {
            $total += $conversation->getUnreadCountForUser($user->id);
        }

        return $total;
    }
}
