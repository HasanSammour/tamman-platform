<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate the user
        $request->authenticate();

        // Regenerate session
        $request->session()->regenerate();

        // Get authenticated user
        $user = Auth::user();

        $user->update([
            'last_login_at' => now(),
            'is_online' => true,
        ]);

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => __('Your account is deactivated. Please contact support.'),
            ]);
        }

        // Redirect based on role
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->hasRole('specialist')) {
            // Check if specialist is verified and approved
            if (
                $user->specialistProfile &&
                $user->specialistProfile->is_verified &&
                $user->specialistProfile->application_status === 'approved'
            ) {
                return redirect()->intended(route('specialist.dashboard'));
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => __('Your account is pending approval. You will be notified once approved.'),
                ]);
            }
        }

        // Default patient
        return redirect()->intended(route('patient.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Update user before logout
        $user = Auth::user();
        if ($user) {
            $user->is_online = false;
            $user->save();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
