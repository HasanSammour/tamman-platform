{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', __('Login') . ' - ' . __('Tamman'))

@section('auth-content')
<div class="auth-header">
    <div class="auth-icon">
        <i class="fas fa-sign-in-alt"></i>
    </div>
    <h2>{{ __('Welcome Back') }}</h2>
    <p>{{ __('Please login to your account') }}</p>
</div>

<form method="POST" action="{{ route('login') }}" class="auth-form">
    @csrf

    <!-- Email Address -->
    <div class="form-group">
        <label for="email">{{ __('Email Address') }} <span class="required">*</span></label>
        <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="{{ __('your@email.com') }}">
        </div>
        @error('email')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Password -->
    <div class="form-group">
        <label for="password">{{ __('Password') }} <span class="required">*</span></label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="{{ __('Enter your password') }}">
            <i class="fas fa-eye-slash password-toggle" style="cursor: pointer;" onclick="togglePassword(this)"></i>
        </div>
        @error('password')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="form-options">
        <label class="checkbox-label">
            <input type="checkbox" name="remember">
            <span>{{ __('Remember me') }}</span>
        </label>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-link">
                {{ __('Forgot password?') }}
            </a>
        @endif
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn-auth">
        <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
    </button>

    <!-- Register Link -->
    <div class="auth-footer">
        <p>{{ __("Don't have an account?") }} <a href="{{ route('register') }}">{{ __('Create one') }}</a></p>
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
    
    .error-message {
        color: #ef4444;
        font-size: 0.7rem;
        margin-top: 5px;
        display: block;
    }
    
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
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
    
    .forgot-link {
        font-size: 0.875rem;
        color: #7c3aed;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .forgot-link:hover {
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
    
    body.rtl .form-control {
        padding: 12px 45px 12px 45px;
    }
    
    @media (max-width: 576px) {
        .auth-header h2 {
            font-size: 1.25rem;
        }
        
        .form-options {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
    }
</style>
@endpush