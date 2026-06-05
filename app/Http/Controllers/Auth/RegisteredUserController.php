<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'الرجاء إدخال الاسم الكامل',
            'email.required' => 'الرجاء إدخال البريد الإلكتروني',
            'email.unique' => 'هذا البريد الإلكتروني مسجل مسبقاً',
            'email.email' => 'الرجاء إدخال بريد إلكتروني صحيح',
            'phone.required' => 'الرجاء إدخال رقم الهاتف',
            'password.required' => 'الرجاء إدخال كلمة المرور',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
            'gender.in' => 'الرجاء اختيار جنس صحيح',
            'date_of_birth.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم',
            'referral_code.exists' => 'رمز الإحالة غير صحيح',
            'terms.accepted' => 'الرجاء الموافقة على الشروط والأحكام',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate unique referral code for the new user
        $referralCode = User::generateReferralCode();

        // Default notification settings
        $defaultNotificationSettings = [
            'session_reminders' => true,
            'points_earned' => true,
            'new_messages' => true,
            'treatment_tasks' => true,
            'promotional_emails' => false,
        ];

        // Default privacy settings
        $defaultPrivacySettings = [
            'profile_visibility' => 'public',
            'show_email' => false,
            'show_phone' => false,
            'show_activity_status' => true,
            'allow_messages_from' => 'everyone',
        ];

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'is_active' => true,
            'total_points' => 0,
            'credit_balance' => 0,
            'referral_code' => $referralCode,
            'notification_settings' => json_encode($defaultNotificationSettings),
            'privacy_settings' => json_encode($defaultPrivacySettings),
            'preferred_locale' => app()->getLocale(),
        ]);

        // Assign patient role
        $user->assignRole('patient');

        // Handle referral code if provided
        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $user->referred_by = $referrer->id;
                $user->save();

                // Add points to the referrer
                $referrer->addReferralPoints();
            }
        }

        // Send email verification
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        return redirect()->route('patient.dashboard');
    }
}