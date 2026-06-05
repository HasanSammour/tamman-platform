{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.auth')

@section('title', __('Verify Email') . ' - ' . __('Tamman'))

@section('auth-content')
    <div class="verify-container">
        <div class="verify-icon animate-pulse">
            <div class="icon-circle">
                <i class="fas fa-envelope-open-text"></i>
            </div>
        </div>

        <h2 class="verify-title">{{ __('Verify Your Email Address') }}</h2>
        <p class="verify-subtitle">
            {{ __('Thanks for signing up! Before getting started, please verify your email address.') }}</p>

        <div class="info-card animate-fade-in-up">
            <div class="info-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="info-content">
                <p>{{ __('A verification link has been sent to your email address.') }}</p>
                <small>{{ __('Please check your inbox and click the verification link.') }}</small>
            </div>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert-success animate-slide-in">
                <i class="fas fa-check-circle"></i>
                <span>{{ __('A new verification link has been sent to your email address.') }}</span>
            </div>
        @endif

        <div class="auth-actions">
            <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
                @csrf
                <button type="submit" class="btn-resend">
                    <i class="fas fa-redo-alt"></i>
                    <span>{{ __('Resend Verification Email') }}</span>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>

        <div class="help-text">
            <i class="fas fa-question-circle"></i>
            <span>{{ __('Didn\'t receive the email?') }}</span>
            <a href="#" class="help-link"
                onclick="event.preventDefault(); document.querySelector('.resend-form').submit();">
                {{ __('Click here to resend') }}
            </a>
        </div>
    </div>

    @push('styles')
        <style>
            .verify-container {
                text-align: center;
            }

            /* Animated Icon */
            .verify-icon {
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
            .verify-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 8px;
            }

            .verify-subtitle {
                font-size: 0.85rem;
                color: #6b7280;
                margin-bottom: 24px;
            }

            /* Info Card */
            .info-card {
                background: #f5f3ff;
                border-radius: 16px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                text-align: left;
                margin-bottom: 20px;
                border: 1px solid #e9d5ff;
            }

            body.rtl .info-card {
                text-align: right;
            }

            .info-icon {
                width: 45px;
                height: 45px;
                background: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .info-icon i {
                font-size: 1.2rem;
                color: #7c3aed;
            }

            .info-content p {
                font-size: 0.85rem;
                font-weight: 500;
                color: #1f2937;
                margin: 0 0 4px 0;
            }

            .info-content small {
                font-size: 0.7rem;
                color: #6b7280;
            }

            /* Alert Success */
            .alert-success {
                background: #d1fae5;
                border-radius: 12px;
                padding: 12px 16px;
                margin-bottom: 24px;
                display: flex;
                align-items: center;
                gap: 10px;
                border-left: 3px solid #10b981;
            }

            body.rtl .alert-success {
                border-left: none;
                border-right: 3px solid #10b981;
            }

            .alert-success i {
                color: #10b981;
                font-size: 1.1rem;
            }

            .alert-success span {
                font-size: 0.8rem;
                color: #065f46;
                flex: 1;
                text-align: left;
            }

            body.rtl .alert-success span {
                text-align: right;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-slide-in {
                animation: slideIn 0.4s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease-out;
            }

            /* Buttons */
            .auth-actions {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-bottom: 20px;
            }

            .resend-form,
            .logout-form {
                width: 100%;
            }

            .btn-resend {
                width: 100%;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border: none;
                padding: 12px 20px;
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
            }

            .btn-resend:hover {
                background: linear-gradient(135deg, #6d28d9, #5b21b6);
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);
            }

            .btn-resend:active {
                transform: translateY(0);
            }

            .btn-logout {
                width: 100%;
                background: #f3f4f6;
                border: 1px solid #e5e7eb;
                padding: 12px 20px;
                border-radius: 40px;
                color: #4b5563;
                font-weight: 500;
                font-size: 0.85rem;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .btn-logout:hover {
                background: #e5e7eb;
                color: #1f2937;
                transform: translateY(-2px);
            }

            /* Help Text */
            .help-text {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                font-size: 0.75rem;
                color: #6b7280;
                flex-wrap: wrap;
            }

            .help-text i {
                color: #9ca3af;
            }

            .help-link {
                color: #7c3aed;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            .help-link:hover {
                color: #6d28d9;
                text-decoration: underline;
            }

            /* RTL Adjustments */
            body.rtl .info-card {
                flex-direction: row;
            }

            body.rtl .btn-resend i,
            body.rtl .btn-logout i {
                margin-left: 8px;
                margin-right: 0;
            }

            /* Mobile Responsive */
            @media (max-width: 480px) {
                .info-card {
                    padding: 15px;
                }

                .info-icon {
                    width: 38px;
                    height: 38px;
                }

                .info-icon i {
                    font-size: 1rem;
                }

                .info-content p {
                    font-size: 0.75rem;
                }

                .info-content small {
                    font-size: 0.65rem;
                }

                .verify-title {
                    font-size: 1.2rem;
                }

                .icon-circle {
                    width: 65px;
                    height: 65px;
                }

                .icon-circle i {
                    font-size: 2rem;
                }
            }
        </style>
    @endpush
@endsection