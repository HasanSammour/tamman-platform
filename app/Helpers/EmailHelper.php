<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class EmailHelper
{
    /**
     * Send email in user's preferred language
     */
    public static function sendInUserLanguage($user, $mailable)
    {
        if (!$user || !$user->email) {
            return null;
        }
        
        $originalLocale = app()->getLocale();
        
        try {
            $userLocale = $user->preferred_locale ?? 'ar';
            app()->setLocale($userLocale);
            
            Mail::to($user->email)->send($mailable);
            
            app()->setLocale($originalLocale);
            return true;
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            app()->setLocale($originalLocale);
            return false;
        }
    }
    
    /**
     * Send email in specific locale
     */
    public static function sendInLocale($email, $mailable, $locale = 'ar')
    {
        $originalLocale = app()->getLocale();
        
        try {
            app()->setLocale($locale);
            Mail::to($email)->send($mailable);
            app()->setLocale($originalLocale);
            return true;
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            app()->setLocale($originalLocale);
            return false;
        }
    }
    
    /**
     * Queue email in user's preferred language
     */
    public static function queueInUserLanguage($user, $mailable)
    {
        if (!$user || !$user->email) {
            return null;
        }
        
        $originalLocale = app()->getLocale();
        $userLocale = $user->preferred_locale ?? 'ar';
        app()->setLocale($userLocale);
        
        try {
            Mail::to($user->email)->queue($mailable);
            app()->setLocale($originalLocale);
            return true;
        } catch (\Exception $e) {
            \Log::error('Email queue failed: ' . $e->getMessage());
            app()->setLocale($originalLocale);
            return false;
        }
    }
}