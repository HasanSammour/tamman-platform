<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
     /**
     * Display settings page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's settings or defaults
        $notificationSettings = $user->notification_settings;
        $privacySettings = $user->privacy_settings;
        $preferredLocale = $user->preferred_locale ?? 'ar';
        
        // Decode JSON if it's a string
        if (is_string($notificationSettings)) {
            $notificationSettings = json_decode($notificationSettings, true);
        }
        if (is_string($privacySettings)) {
            $privacySettings = json_decode($privacySettings, true);
        }
        
        // Ensure defaults if null
        if (!$notificationSettings) {
            $notificationSettings = [
                'session_reminders' => true,
                'points_earned' => true,
                'new_messages' => true,
                'treatment_tasks' => true,
                'promotional_emails' => false,
            ];
        }
        
        if (!$privacySettings) {
            $privacySettings = [
                'profile_visibility' => 'public',
                'show_email' => false,
                'show_phone' => false,
                'show_activity_status' => true,
                'allow_messages_from' => 'everyone',
            ];
        }
        
        return view('user-settings', compact(
            'user',
            'notificationSettings',
            'privacySettings',
            'preferredLocale'
        ));
    }
    
    /**
     * Update notification settings via AJAX
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'session_reminders' => 'nullable|boolean',
            'points_earned' => 'nullable|boolean',
            'new_messages' => 'nullable|boolean',
            'treatment_tasks' => 'nullable|boolean',
            'promotional_emails' => 'nullable|boolean',
        ]);
        
        $user = Auth::user();
        
        // Get existing settings or defaults
        $existingSettings = $user->notification_settings;
        if (is_string($existingSettings)) {
            $existingSettings = json_decode($existingSettings, true);
        }
        
        if (!$existingSettings) {
            $existingSettings = [
                'session_reminders' => true,
                'points_earned' => true,
                'new_messages' => true,
                'treatment_tasks' => true,
                'promotional_emails' => false,
            ];
        }
        
        // Update only the fields that were sent
        $notificationSettings = [
            'session_reminders' => $request->has('session_reminders') ? (bool)$request->session_reminders : ($existingSettings['session_reminders'] ?? true),
            'points_earned' => $request->has('points_earned') ? (bool)$request->points_earned : ($existingSettings['points_earned'] ?? true),
            'new_messages' => $request->has('new_messages') ? (bool)$request->new_messages : ($existingSettings['new_messages'] ?? true),
            'treatment_tasks' => $request->has('treatment_tasks') ? (bool)$request->treatment_tasks : ($existingSettings['treatment_tasks'] ?? true),
            'promotional_emails' => $request->has('promotional_emails') ? (bool)$request->promotional_emails : ($existingSettings['promotional_emails'] ?? false),
        ];
        
        // Save as JSON
        $user->notification_settings = json_encode($notificationSettings);
        $user->save();
        
        \Log::info('Notification settings saved', ['user_id' => $user->id, 'settings' => $notificationSettings]);
        
        return response()->json([
            'success' => true,
            'message' => __('Notification settings updated successfully!'),
            'settings' => $notificationSettings
        ]);
    }
    
    /**
     * Update privacy settings via AJAX
     */
    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'profile_visibility' => 'nullable|in:public,private,contacts_only',
            'show_email' => 'nullable|boolean',
            'show_phone' => 'nullable|boolean',
            'show_activity_status' => 'nullable|boolean',
            'allow_messages_from' => 'nullable|in:everyone,only_specialists,only_patients,none',
        ]);
        
        $user = Auth::user();
        
        // Get existing settings or defaults
        $existingSettings = $user->privacy_settings;
        if (is_string($existingSettings)) {
            $existingSettings = json_decode($existingSettings, true);
        }
        
        if (!$existingSettings) {
            $existingSettings = [
                'profile_visibility' => 'public',
                'show_email' => false,
                'show_phone' => false,
                'show_activity_status' => true,
                'allow_messages_from' => 'everyone',
            ];
        }
        
        // Update privacy settings
        $privacySettings = [
            'profile_visibility' => $request->profile_visibility ?? ($existingSettings['profile_visibility'] ?? 'public'),
            'show_email' => $request->has('show_email') ? (bool)$request->show_email : ($existingSettings['show_email'] ?? false),
            'show_phone' => $request->has('show_phone') ? (bool)$request->show_phone : ($existingSettings['show_phone'] ?? false),
            'show_activity_status' => $request->has('show_activity_status') ? (bool)$request->show_activity_status : ($existingSettings['show_activity_status'] ?? true),
            'allow_messages_from' => $request->allow_messages_from ?? ($existingSettings['allow_messages_from'] ?? 'everyone'),
        ];
        
        // Save as JSON
        $user->privacy_settings = json_encode($privacySettings);
        $user->save();
        
        \Log::info('Privacy settings saved', ['user_id' => $user->id, 'settings' => $privacySettings]);
        
        return response()->json([
            'success' => true,
            'message' => __('Privacy settings updated successfully!'),
            'settings' => $privacySettings
        ]);
    }

    /**
     * Update language preference via AJAX
     */
    public function updateLanguage(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:ar,en',
        ]);

        $user = Auth::user();
        $user->preferred_locale = $request->locale;
        $user->save();

        // Update session locale
        App::setLocale($request->locale);
        Session::put('locale', $request->locale);

        // Set messages based on selected locale
        if ($request->locale === 'ar') {
            $title = 'تم بنجاح!';
            $message = 'تم تحديث تفضيلات اللغة بنجاح!';
        } else {
            $title = 'Success!';
            $message = 'Language preference updated successfully!';
        }

        return response()->json([
            'success' => true,
            'title' => $title,
            'message' => $message,
            'locale' => $request->locale,
        ]);
    }
}