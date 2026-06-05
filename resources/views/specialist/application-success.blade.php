@extends('layouts.guest')

@section('title', __('Application Submitted - Tamman'))

@section('content')

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
                <div class="success-icon-bg"></div>
            </div>

            <h1>{{ __('Application Submitted Successfully!') }}</h1>

            <div class="success-message">
                <p>{{ __('Thank you for applying to become a specialist on Tamman.') }}</p>
                <p>{{ __('Your application has been received and is now under review.') }}</p>
                <p>{{ __('Our team will review your application within 5-7 business days.') }}</p>
                <p>{{ __('You will receive an email notification once your application is reviewed and approved.') }}</p>
            </div>

            <div class="success-details">
                <div class="detail-item">
                    <i class="fas fa-envelope"></i>
                    <span>{{ __('Check your email for updates') }}</span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ __('Review time: 5-7 business days') }}</span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-bell"></i>
                    <span>{{ __('You will be notified when approved') }}</span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-lock"></i>
                    <span>{{ __('You cannot log in until your application is approved') }}</span>
                </div>
            </div>

            <div class="success-buttons">
                <a href="{{ route('home') }}" class="btn-home">
                    <i class="fas fa-home"></i> {{ __('Back to Home') }}
                </a>
                <a href="{{ route('specialist.apply') }}" class="btn-apply">
                    <i class="fas fa-file-alt"></i> {{ __('New Application') }}
                </a>
            </div>
        </div>
    </div>

    <style>
        .success-container {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
        }

        .success-card {
            max-width: 550px;
            width: 100%;
            text-align: center;
            background: white;
            padding: 50px 40px;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes checkmark {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.2;
            }
        }

        .success-icon {
            position: relative;
            width: 90px;
            height: 90px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon i {
            font-size: 4rem;
            color: #10b981;
            z-index: 2;
            animation: checkmark 0.5s ease-in-out 0.2s both;
        }

        .success-icon-bg {
            position: absolute;
            width: 90px;
            height: 90px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        .success-card h1 {
            font-size: 1.75rem;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .success-message {
            margin-bottom: 30px;
        }

        .success-message p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .success-details {
            background: #f9fafb;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-item i {
            width: 30px;
            color: #7c3aed;
            font-size: 1.1rem;
        }

        .detail-item span {
            color: #374151;
            font-size: 0.875rem;
        }

        .success-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-home,
        .btn-apply {
            padding: 12px 28px;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-home {
            background: transparent;
            border: 1px solid #e5e7eb;
            color: #4b5563;
        }

        .btn-home:hover {
            border-color: #7c3aed;
            color: #7c3aed;
            transform: translateY(-2px);
        }

        .btn-apply {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
        }

        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
            color: white;
        }

        @media (max-width: 768px) {
            .success-card {
                padding: 35px 25px;
            }

            .success-card h1 {
                font-size: 1.4rem;
            }

            .success-buttons {
                flex-direction: column;
            }

            .btn-home,
            .btn-apply {
                justify-content: center;
            }
        }
    </style>
@endsection