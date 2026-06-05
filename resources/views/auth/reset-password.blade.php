{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.auth')

@section('title', __('Reset Password') . ' - ' . __('Tamman'))

@section('auth-content')
    <div class="reset-container">
        <div class="reset-icon animate-pulse">
            <div class="icon-circle">
                <i class="fas fa-lock"></i>
            </div>
        </div>

        <h2 class="reset-title">{{ __('Reset Password') }}</h2>
        <p class="reset-subtitle">{{ __('Please enter your new password') }}</p>

        <form method="POST" action="{{ route('password.store') }}" class="reset-form">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="form-group">
                <label for="email">{{ __('Email Address') }} <span class="required">*</span></label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $request->email) }}" required autofocus
                        placeholder="{{ __('your@email.com') }}">
                </div>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="password">{{ __('New Password') }} <span class="required">*</span></label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" required
                        placeholder="{{ __('At least 8 characters') }}">
                    <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('password')"></i>
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">{{ __('Confirm New Password') }} <span class="required">*</span></label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                        required placeholder="{{ __('Repeat your password') }}">
                    <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('password_confirmation')"></i>
                </div>
            </div>

            <button type="submit" class="btn-reset">
                <i class="fas fa-save"></i>
                <span>{{ __('Reset Password') }}</span>
            </button>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ __('Back to Login') }}</span>
                </a>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            .reset-container {
                text-align: center;
            }

            /* Animated Icon */
            .reset-icon {
                margin-bottom: 24px;
            }

            .icon-circle {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
                box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.2);
            }

            .icon-circle i {
                font-size: 2.5rem;
                color: #7c3aed;
            }

            @keyframes pulse {
                0% {
                    transform: scale(1);
                    opacity: 1;
                }

                50% {
                    transform: scale(1.05);
                    opacity: 0.9;
                }

                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .animate-pulse {
                animation: pulse 2s ease-in-out infinite;
            }

            /* Titles */
            .reset-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 8px;
            }

            .reset-subtitle {
                font-size: 0.85rem;
                color: #6b7280;
                margin-bottom: 24px;
            }

            /* Form */
            .reset-form {
                margin-top: 10px;
            }

            .form-group {
                text-align: left;
                margin-bottom: 20px;
            }

            body.rtl .form-group {
                text-align: right;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                font-size: 0.85rem;
                color: #374151;
            }

            .required {
                color: #ef4444;
            }

            .input-wrapper {
                position: relative;
            }

            .input-icon {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.9rem;
                z-index: 1;
            }

            body.rtl .input-icon {
                left: auto;
                right: 14px;
            }

            .form-control {
                width: 100%;
                padding: 12px 16px 12px 42px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.9rem;
                transition: all 0.3s ease;
            }

            body.rtl .form-control {
                padding: 12px 42px 12px 16px;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .form-control.is-invalid {
                border-color: #ef4444;
            }

            .error-message {
                color: #ef4444;
                font-size: 0.7rem;
                margin-top: 5px;
                display: block;
            }

            /* Password Toggle */
            .password-toggle {
                position: absolute;
                right: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 1rem;
                cursor: pointer;
                z-index: 1;
                transition: color 0.3s ease;
            }

            .password-toggle:hover {
                color: #7c3aed;
            }

            body.rtl .password-toggle {
                right: auto;
                left: 14px;
            }

            /* Reset Button */
            .btn-reset {
                width: 100%;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border: none;
                padding: 14px 20px;
                border-radius: 40px;
                color: white;
                font-weight: 600;
                font-size: 0.85rem;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                margin-top: 8px;
            }

            .btn-reset:hover {
                background: linear-gradient(135deg, #6d28d9, #5b21b6);
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);
            }

            /* Back Link */
            .back-link {
                margin-top: 20px;
            }

            .back-link a {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: #7c3aed;
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .back-link a:hover {
                text-decoration: underline;
                color: #6d28d9;
            }

            body.rtl .back-link a i {
                transform: rotate(180deg);
            }

            /* Mobile Responsive */
            @media (max-width: 480px) {
                .icon-circle {
                    width: 65px;
                    height: 65px;
                }

                .icon-circle i {
                    font-size: 2rem;
                }

                .reset-title {
                    font-size: 1.2rem;
                }

                .btn-reset {
                    padding: 12px 16px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function togglePassword(fieldId) {
                const input = document.getElementById(fieldId);
                const icon = input.parentElement.querySelector('.password-toggle');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            }
        </script>
    @endpush
@endsection