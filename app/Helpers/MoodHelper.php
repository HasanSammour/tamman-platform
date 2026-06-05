<?php

namespace App\Helpers;

class MoodHelper
{
    /**
     * Get mood color based on value (1-10 scale)
     */
    public static function getColor($value)
    {
        if ($value >= 9) return '#7c3aed';      // Purple - Excellent
        if ($value >= 7) return '#10b981';      // Green - Good
        if ($value >= 5) return '#eab308';      // Yellow - Neutral
        if ($value >= 3) return '#f59e0b';      // Orange - Low
        return '#ef4444';                        // Red - Very Low
    }
    
    /**
     * Get mood emoji based on value (1-10 scale)
     */
    public static function getEmoji($value)
    {
        $emojis = [
            10 => '😍',   // Absolutely Amazing
            9 => '😊',    // Great
            8 => '😄',    // Very Happy
            7 => '🙂',    // Happy
            6 => '😐',    // Pretty Good
            5 => '😶',    // Neutral / Okay (Face Without Mouth)
            4 => '😕',    // Slightly Sad
            3 => '😔',    // Sad
            2 => '😢',    // Very Sad
            1 => '😫',    // Terrible / Awful
        ];
        
        return $emojis[round($value)] ?? '😐';
    }
    
    /**
     * Get mood label in English
     */
    public static function getLabel($value)
    {
        $labels = [
            10 => 'Absolutely Amazing',
            9 => 'Great',
            8 => 'Very Happy',
            7 => 'Happy',
            6 => 'Pretty Good',
            5 => 'Neutral',
            4 => 'Slightly Sad',
            3 => 'Sad',
            2 => 'Very Sad',
            1 => 'Terrible',
        ];
        
        return $labels[round($value)] ?? 'Neutral';
    }
    
    /**
     * Get mood label in Arabic
     */
    public static function getLabelAr($value)
    {
        $labels = [
            10 => 'ممتاز جداً',
            9 => 'ممتاز',
            8 => 'سعيد جداً',
            7 => 'سعيد',
            6 => 'جيد',
            5 => 'عادي',
            4 => 'حزين قليلاً',
            3 => 'حزين',
            2 => 'حزين جداً',
            1 => 'فظيع',
        ];
        
        return $labels[round($value)] ?? 'عادي';
    }
    
    /**
     * Get mood description for tooltip
     */
    public static function getDescription($value)
    {
        $descriptions = [
            10 => 'Feeling absolutely amazing! 😍',
            9 => 'Feeling great today! 😊',
            8 => 'Very happy and energetic! 😄',
            7 => 'Happy and content! 🙂',
            6 => 'Pretty good, can\'t complain! 😐',
            5 => 'Neutral, just an ordinary day 😶',
            4 => 'A bit down today 😕',
            3 => 'Feeling sad 😔',
            2 => 'Very sad and low energy 😢',
            1 => 'Having a terrible day 😫',
        ];
        
        return $descriptions[round($value)] ?? 'Neutral day';
    }
}