<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notifications center page
     */
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total' => Notification::where('user_id', $user->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            'read' => Notification::where('user_id', $user->id)->where('is_read', true)->count(),
        ];

        return view('notifications.index', compact('stats'));
    }

    /**
     * Fetch notifications for dropdown (AJAX)
     */
    public function fetch(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'time_ago' => $notification->created_at->diffForHumans(),
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'icon' => $this->getNotificationIcon($notification->type),
                    'color' => $this->getNotificationColor($notification->type),
                ];
            });

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'has_more' => Notification::where('user_id', $user->id)->count() > $limit,
        ]);
    }

    /**
     * Get paginated notifications for center page (AJAX)
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();

        $query = Notification::where('user_id', $user->id);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 12);
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $notifications->getCollection()->transform(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'created_at_formatted' => $notification->created_at->translatedFormat('M d, Y h:i A'),
                'time_ago' => $notification->created_at->diffForHumans(),
                'icon' => $this->getNotificationIcon($notification->type),
                'color' => $this->getNotificationColor($notification->type),
            ];
        });

        // Get filter counts
        $filterCounts = [
            'all' => Notification::where('user_id', $user->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            'read' => Notification::where('user_id', $user->id)->where('is_read', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'total' => $notifications->total(),
            'per_page' => $notifications->perPage(),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'filter_counts' => $filterCounts,
        ]);
    }

    /**
     * Get unread count for header badge (AJAX)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Mark a single notification as read (AJAX)
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->is_read = true;
        $notification->save();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'message' => __('Notification marked as read'),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications as read (AJAX)
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => __('All notifications marked as read'),
            'marked_count' => $count,
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete a single notification (AJAX)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->delete();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'message' => __('Notification deleted'),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Delete all notifications (AJAX)
     */
    public function destroyAll()
    {
        $user = Auth::user();
        $count = Notification::where('user_id', $user->id)->count();

        Notification::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => __('All notifications cleared'),
            'deleted_count' => $count,
            'unread_count' => 0,
        ]);
    }

    /**
     * Bulk action on notifications (AJAX)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_read,mark_unread,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:notifications,id',
        ]);

        $user = Auth::user();
        $ids = $request->ids;

        // Verify all notifications belong to the user
        $validIds = Notification::where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (empty($validIds)) {
            return response()->json([
                'success' => false,
                'message' => __('No valid notifications selected'),
            ], 422);
        }

        $action = $request->action;
        $affectedCount = 0;

        if ($action === 'mark_read') {
            $affectedCount = Notification::whereIn('id', $validIds)
                ->update(['is_read' => true]);
            $message = __('Selected notifications marked as read');
        } elseif ($action === 'mark_unread') {
            $affectedCount = Notification::whereIn('id', $validIds)
                ->update(['is_read' => false]);
            $message = __('Selected notifications marked as unread');
        } elseif ($action === 'delete') {
            $affectedCount = Notification::whereIn('id', $validIds)->delete();
            $message = __('Selected notifications deleted');
        }

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'affected_count' => $affectedCount,
            'unread_count' => $unreadCount,
        ]);
    }

    // ==================== PRIVATE HELPERS ====================

    private function getNotificationIcon($type)
    {
        $icons = [
            'session_reminder' => 'fa-calendar-alt',
            'points_earned' => 'fa-star',
            'payment' => 'fa-dollar-sign',
            'application_status' => 'fa-clipboard-list',
            'account_status' => 'fa-user-shield',
            'donation' => 'fa-hand-holding-heart',
            'credit' => 'fa-credit-card',
        ];
        return $icons[$type] ?? 'fa-bell';
    }

    private function getNotificationColor($type)
    {
        $colors = [
            'session_reminder' => '#7c3aed',
            'points_earned' => '#10b981',
            'payment' => '#f59e0b',
            'application_status' => '#3b82f6',
            'account_status' => '#ef4444',
            'donation' => '#ec4899',
            'credit' => '#06b6d4',
        ];
        return $colors[$type] ?? '#6b7280';
    }
}