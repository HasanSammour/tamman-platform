<?php

namespace App\Helpers;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * HTML Purifier Helper
 * 
 * This class sanitizes HTML content from admin users to prevent XSS attacks.
 * It allows safe HTML tags and attributes while removing malicious scripts,
 * event handlers, and other dangerous content.
 * 
 * Usage:
 *   $cleanHtml = HtmlPurifierHelper::clean($dirtyHtml);
 * 
 * @package App\Helpers
 */
class HtmlPurifierHelper
{
    /**
     * Sanitize HTML content by removing malicious scripts and attributes
     * 
     * @param string $html The raw HTML content to clean
     * @return string The sanitized HTML content
     */
    public static function clean($html)
    {
        // If input is null or empty, return as is
        if (empty($html)) {
            return $html;
        }

        // Create default configuration
        $config = HTMLPurifier_Config::createDefault();
        
        // ==================== ALLOWED HTML TAGS ====================
        // These tags are safe for educational content
        $config->set('HTML.Allowed', 
            'h1,h2,h3,h4,h5,h6,' .             // Headings
            'strong,b,' .                      // Bold
            'em,i,' .                          // Italic
            'u,' .                             // Underline
            'p,' .                             // Paragraphs
            'br,' .                            // Line breaks
            'ul,ol,li,' .                      // Lists
            'a[href|target],' .                // Links with href and target attributes
            'img[src|alt],' .                  // Images with src and alt attributes
            'blockquote,' .                    // Blockquotes
            'hr,' .                            // Horizontal rules
            'code,' .                          // Inline code
            'pre'                              // Preformatted text/code blocks
        );
        
        // ==================== ALLOWED ATTRIBUTES ====================
        // IMPORTANT: Set ALL attributes at once, not in multiple calls
        $config->set('HTML.AllowedAttributes', [
            'a.href',      // Link URL
            'a.target',    // Link target (_blank, _self, etc.)
            'img.src',     // Image source URL
            'img.alt'      // Image alt text
        ]);
        
        // ==================== URI SCHEMES ====================
        // Only allow safe URL schemes
        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,  // Email links
        ]);
        
        // ==================== SECURITY SETTINGS ====================
        
        // Force all links to open in new tab (security best practice)
        $config->set('HTML.TargetBlank', true);
        
        // Disable embedded Flash and other risky objects
        $config->set('HTML.SafeObject', false);
        $config->set('HTML.SafeEmbed', false);
        
        // Disallow CSS styles entirely
        // This prevents inline style attributes
        $config->set('CSS.AllowedProperties', []);
        $config->set('HTML.ForbiddenAttributes', ['style']);
        
        // ==================== IFRAME SETTINGS ====================
        // Explicitly disable iframes (can be enabled for YouTube/Vimeo if needed)
        $config->set('HTML.SafeIframe', false);
        
        // Optional: Allow only specific iframe sources (for video embeds)
        // $config->set('HTML.SafeIframe', true);
        // $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube\.com/embed/|player\.vimeo\.com/video/)%');
        
        // ==================== AUTO-PARAGRAPH ====================
        // Disable auto-paragraph (handled manually by admin)
        $config->set('Core.EscapeInvalidTags', true);
        
        // Initialize HTML Purifier with configuration
        $purifier = new HTMLPurifier($config);
        
        // Clean and return the sanitized HTML
        return $purifier->purify($html);
    }
    
    /**
     * Clean HTML content for database storage (alias of clean method)
     * 
     * @param string $html
     * @return string
     */
    public static function sanitize($html)
    {
        return self::clean($html);
    }
    
    /**
     * Check if HTML content contains potentially malicious code
     * 
     * @param string $html
     * @return bool Returns true if content is safe, false if contains threats
     */
    public static function isSafe($html)
    {
        $cleaned = self::clean($html);
        return $cleaned === $html;
    }
    
    /**
     * Get only plain text from HTML (strip all tags)
     * 
     * @param string $html
     * @return string
     */
    public static function toPlainText($html)
    {
        return strip_tags($html);
    }
}