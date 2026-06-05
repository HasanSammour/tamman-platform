{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.auth')

@section('title', __('Register') . ' - ' . __('Tamman'))

@section('auth-content')
<div class="auth-header">
    <div class="auth-icon">
        <i class="fas fa-user-plus"></i>
    </div>
    <h2>{{ __('Create Account') }}</h2>
    <p>{{ __('Join Tamman and start your mental health journey') }}</p>
</div>

<form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf

    <!-- Full Name -->
    <div class="form-group">
        <label for="name">{{ __('Full Name') }} <span class="required">*</span></label>
        <div class="input-wrapper">
            <i class="fas fa-user input-icon"></i>
            <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="{{ __('e.g., Ahmed Mohamed') }}">
        </div>
        @error('name')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Email Address -->
    <div class="form-group">
        <label for="email">{{ __('Email Address') }} <span class="required">*</span></label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="{{ __('your@email.com') }}">
        </div>
        @error('email')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Phone Number -->
    <div class="form-group">
        <label for="phone">{{ __('Phone Number') }} <span class="required">*</span></label>
        <div class="input-wrapper">
            <i class="fas fa-phone input-icon"></i>
            <input id="phone" type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required placeholder="{{ __('+970 5X XXX XXXX') }}">
        </div>
        @error('phone')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Gender -->
    <div class="form-group">
        <label for="gender">{{ __('Gender') }}</label>
        <div class="input-wrapper">
            <i class="fas fa-venus-mars input-icon"></i>
            <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror">
                <option value="">{{ __('Select gender') }}</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
            </select>
            <i class="fas fa-chevron-down select-icon"></i>
        </div>
        @error('gender')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Date of Birth -->
    <div class="form-group">
        <label for="date_of_birth">{{ __('Date of Birth') }}</label>
        <div class="input-wrapper">
            <i class="fas fa-calendar-alt input-icon"></i>
            <input id="date_of_birth" type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">
        </div>
        @error('date_of_birth')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Password -->
    <div class="form-group">
        <label for="password">{{ __('Password') }} <span class="required">*</span></label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="{{ __('At least 8 characters') }}">
            <i class="fas fa-eye-slash password-toggle" style="cursor: pointer;" onclick="togglePassword(this)"></i>
        </div>
        @error('password')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="form-group">
        <label for="password_confirmation">{{ __('Confirm Password') }} <span class="required">*</span></label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required placeholder="{{ __('Repeat your password') }}">
            <i class="fas fa-eye-slash password-toggle" style="cursor: pointer;" onclick="togglePassword(this)"></i>
        </div>
    </div>

    <!-- Referral Code -->
    <div class="form-group">
        <label for="referral_code">{{ __('Referral Code (Optional)') }}</label>
        <div class="input-wrapper">
            <i class="fas fa-gift input-icon"></i>
            <input id="referral_code" type="text" name="referral_code" class="form-control @error('referral_code') is-invalid @enderror" value="{{ old('referral_code') }}" placeholder="{{ __('Enter referral code') }}">
        </div>
        <small class="form-hint">{{ __('Have a referral code? Enter it here to get bonus points!') }}</small>
        @error('referral_code')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Terms and Conditions -->
    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
            <span>{{ __('I agree to the') }} <a href="{{ route('terms') }}" class="terms-link">{{ __('Terms and Conditions') }}</a> {{ __('and') }} <a href="{{ route('privacy') }}" class="terms-link">{{ __('Privacy Policy') }}</a></span>
        </label>
        @error('terms')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn-auth">
        <i class="fas fa-user-plus"></i> {{ __('Register') }}
    </button>

    <!-- Login Link -->
    <div class="auth-footer">
        <p>{{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Login here') }}</a></p>
    </div>
</form>

<script>
    function togglePassword(element) {
        var wrapper = element.parentElement;
        var input = wrapper.querySelector('input');
        
        if (input) {
            if (input.type === 'password') {
                input.type = 'text';
                element.classList.remove('fa-eye-slash');
                element.classList.add('fa-eye');
            } else {
                input.type = 'password';
                element.classList.remove('fa-eye');
                element.classList.add('fa-eye-slash');
            }
        }
    }
</script>
@endsection

@push('styles')
<style>
    .auth-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .auth-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    
    .auth-icon i {
        font-size: 2rem;
        color: #7c3aed;
    }
    
    .auth-header h2 {
        font-size: 1.5rem;
        margin-bottom: 5px;
        color: #1f2937;
    }
    
    .auth-header p {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
    }
    
    .required {
        color: #ef4444;
    }
    
    .input-wrapper {
        position: relative;
    }
    
    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1rem;
    }
    
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #9ca3af;
        transition: color 0.3s ease;
        z-index: 2;
    }
    
    .password-toggle:hover {
        color: #7c3aed;
    }
    
    .select-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 45px 12px 45px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #7c3aed;
        background: white;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }
    
    .form-control.is-invalid {
        border-color: #ef4444;
        background: #fef2f2;
    }
    
    select.form-control {
        cursor: pointer;
        appearance: none;
    }
    
    .error-message {
        color: #ef4444;
        font-size: 0.7rem;
        margin-top: 5px;
        display: block;
    }
    
    .form-hint {
        display: block;
        font-size: 0.7rem;
        color: #9ca3af;
        margin-top: 5px;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 0.875rem;
        color: #4b5563;
    }
    
    .checkbox-label input {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #7c3aed;
    }
    
    .checkbox-label a {
        color: #7c3aed;
        text-decoration: none;
    }
    
    .checkbox-label a:hover {
        text-decoration: underline;
    }
    
    .btn-auth {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 10px;
    }
    
    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
    }
    
    .auth-footer {
        text-align: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    
    .auth-footer p {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .auth-footer a {
        color: #7c3aed;
        text-decoration: none;
        font-weight: 500;
    }
    
    .auth-footer a:hover {
        text-decoration: underline;
    }
    
    /* RTL Support */
    body.rtl .input-icon {
        left: auto;
        right: 15px;
    }
    
    body.rtl .password-toggle {
        right: auto;
        left: 15px;
    }
    
    body.rtl .select-icon {
        right: auto;
        left: 15px;
    }
    
    body.rtl .form-control {
        padding: 12px 45px 12px 45px;
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        .auth-header h2 {
            font-size: 1.25rem;
        }
    }
</style>
@endpush