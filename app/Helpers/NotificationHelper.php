<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    /**
     * Send notification to user
     */
    public static function send($userId, $title, $message, $type = 'system')
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
            'sent_at' => now(),
        ]);
    }
    
    /**
     * Send bulk notification to multiple users
     */
    public static function sendBulk($userIds, $title, $message, $type = 'system')
    {
        $notifications = [];
        $now = now();
        
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
                'sent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        return Notification::insert($notifications);
    }
    
    /**
     * Mark notification as read
     */
    public static function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
        
        if ($notification) {
            $notification->is_read = true;
            $notification->save();
            return true;
        }
        
        return false;
    }
    
    /**
     * Mark all user notifications as read
     */
    public static function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
    
    /**
     * Get unread count for user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}