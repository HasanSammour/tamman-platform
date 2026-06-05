<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is logged in, use their preferred locale from database
        if (Auth::check()) {
            $locale = Auth::user()->preferred_locale ?? 'ar';
            Session::put('locale', $locale);
            app()->setLocale($locale);
        } 
        // If session has locale, use it
        elseif (Session::has('locale')) {
            app()->setLocale(Session::get('locale'));
        }
        // Default to Arabic
        else {
            app()->setLocale('ar');
        }
        
        return $next($request);
    }
}
