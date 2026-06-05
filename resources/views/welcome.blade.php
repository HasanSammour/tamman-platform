{{-- resources/views/welcome.blade.php --}}
@extends('layouts.guest')

@section('title', __('Tamman - Digital Mental Health Platform'))

@section('description', __('Tamman is a secure digital mental health platform providing psychological support services online. Get help from licensed specialists in a private, stigma-free environment.'))

@push('styles')
    <style>
        /* Hero Section */
        .hero {
            padding: 80px 0 60px;
            background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(139, 92, 246, 0.1);
            padding: 8px 16px;
            border-radius: 50px;
            margin-bottom: 24px;
            font-size: 0.875rem;
            color: #7c3aed;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.2;
            margin-bottom: 24px;
        }

        .hero-description {
            font-size: 1.125rem;
            margin-bottom: 32px;
            color: #4b5563;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            margin-bottom: 48px;
            flex-wrap: wrap;
        }

        .hero-stats {
            display: flex;
            gap: 48px;
        }

        .stat-item {
            text-align: left;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #7c3aed;
            display: block;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .hero-image-wrapper {
            position: relative;
        }

        .hero-image-wrapper img {
            width: 100%;
            max-width: 500px;
            border-radius: 30px;
        }

        .floating-card {
            position: absolute;
            background: white;
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            animation: float 3s ease-in-out infinite;
        }

        .floating-card i {
            color: #7c3aed;
            font-size: 1rem;
        }

        .card-1 {
            top: 10%;
            left: -20px;
            animation-delay: 0s;
        }

        .card-2 {
            bottom: 20%;
            right: -30px;
            animation-delay: 1s;
        }

        .card-3 {
            bottom: 30%;
            left: -10px;
            animation-delay: 2s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: white;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }

        .section-badge {
            display: inline-block;
            background: rgba(139, 92, 246, 0.1);
            color: #7c3aed;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            margin-bottom: 16px;
        }

        .section-description {
            color: #6b7280;
            font-size: 1.125rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: white;
            padding: 32px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: #c4b5fd;
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .feature-icon i {
            font-size: 2rem;
            color: #7c3aed;
        }

        .feature-card h3 {
            margin-bottom: 16px;
        }

        .feature-card p {
            color: #6b7280;
            margin-bottom: 0;
        }

        /* How It Works Section */
        .how-it-works {
            padding: 80px 0;
            background: #f9fafb;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: #7c3aed;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
        }

        .step-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .step-icon i {
            font-size: 2rem;
            color: #7c3aed;
        }

        .step h3 {
            margin-bottom: 12px;
        }

        .step p {
            color: #6b7280;
        }

        /* Specialists Section */
        .specialists-preview {
            padding: 80px 0;
            background: white;
        }

        .specialists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .specialist-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .specialist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .specialist-avatar {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            border-radius: 50%;
            overflow: hidden;
        }

        .specialist-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-initials {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 600;
            color: white;
        }

        .specialist-card h3 {
            margin-bottom: 4px;
        }

        .specialist-title {
            color: #7c3aed;
            font-size: 0.875rem;
            margin-bottom: 12px;
        }

        .specialist-rating {
            margin-bottom: 12px;
        }

        .specialist-rating i {
            color: #fbbf24;
        }

        .specialist-rating span {
            font-weight: 600;
            margin-left: 4px;
        }

        .reviews {
            color: #6b7280;
            font-weight: normal;
        }

        .specialist-specialties {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .specialist-specialties span {
            background: #f3f4f6;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            color: #374151;
        }

        /* Points Section */
        .points-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
        }

        .points-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .points-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 50px;
            margin-bottom: 24px;
            font-size: 0.875rem;
        }

        .points-section h2 {
            color: white;
            margin-bottom: 16px;
        }

        .points-section p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 32px;
        }

        .points-list {
            margin-bottom: 32px;
        }

        .points-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .points-item i {
            width: 30px;
        }

        .points-item strong {
            color: #fbbf24;
        }

        .points-card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            color: #1f2937;
            text-align: center;
        }

        .points-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .points-header i {
            font-size: 1.5rem;
            color: #fbbf24;
        }

        .points-balance {
            margin-bottom: 24px;
        }

        .balance-number {
            font-size: 3rem;
            font-weight: 800;
            color: #7c3aed;
            display: block;
        }

        .redeem-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 16px;
        }

        .redeem-options span {
            background: #f3f4f6;
            padding: 8px;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 80px 0;
            background: #f9fafb;
        }

        .testimonials-slider {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .testimonial-card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .testimonial-quote {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 4rem;
            color: #ede9fe;
            z-index: 0;
        }

        .testimonial-text {
            margin-bottom: 24px;
            font-style: italic;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #a78bfa, #7c3aed);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .author-info h4 {
            margin-bottom: 4px;
        }

        .author-rating i {
            color: #fbbf24;
            font-size: 0.75rem;
        }

        /* Donor Section */
        .donor-section {
            padding: 80px 0;
            background: white;
        }

        .donor-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .donor-stats {
            display: flex;
            gap: 40px;
            margin: 32px 0;
        }

        .donor-stat {
            text-align: left;
        }

        .donor-stat .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #7c3aed;
            display: block;
        }

        .donor-illustration {
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border-radius: 50%;
            width: 300px;
            height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            animation: float 3s ease-in-out infinite;
        }

        .donor-illustration i {
            font-size: 6rem;
            color: #7c3aed;
            margin-bottom: 20px;
        }

        .donor-circle-text {
            font-size: 1rem;
            color: #6d28d9;
            font-weight: 500;
            text-align: center;
            padding: 0 20px;
        }

        /* Resources Section */
        .resources {
            padding: 80px 0;
            background: #f9fafb;
        }

        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .resource-card {
            background: white;
            padding: 32px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .resource-icon {
            width: 60px;
            height: 60px;
            background: #ede9fe;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .resource-icon i {
            font-size: 1.5rem;
            color: #7c3aed;
        }

        .resource-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            font-weight: 500;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            text-align: center;
        }

        .cta-content h2 {
            color: white;
            margin-bottom: 16px;
        }

        .cta-content p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 32px;
        }

        .cta-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-buttons .btn-outline {
            border-color: white;
            color: white;
        }

        .cta-buttons .btn-outline:hover {
            background: white;
            color: #7c3aed;
        }

        /* Floating Contact Button */
        .floating-contact {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 998;
        }

        .contact-float-btn {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contact-float-btn i {
            font-size: 1.5rem;
            animation: headphonePulse 2s ease-in-out infinite;
        }

        .contact-float-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #6d28d9, #5b21b6);
            color: white;
        }

        .contact-tooltip {
            position: absolute;
            left: 70px;
            background: #1f2937;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            pointer-events: none;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .contact-float-btn:hover .contact-tooltip {
            opacity: 1;
            visibility: visible;
            left: 65px;
        }

        @keyframes headphonePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Responsive */
        @media (max-width: 992px) {

            .hero-grid,
            .points-grid,
            .donor-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-stats {
                justify-content: center;
            }

            .hero-buttons {
                justify-content: center;
            }

            .points-item {
                text-align: left;
            }

            .stat-item {
                text-align: center;
            }

            .floating-card {
                display: none;
            }

            .donor-stats {
                justify-content: center;
            }

            .steps-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .testimonials-slider {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .hero {
                padding: 40px 0;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .hero-buttons .btn {
                width: 100%;
            }

            .hero-image {
                display: none;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .steps-grid {
                grid-template-columns: 1fr;
            }

            .floating-contact {
                bottom: 20px;
                left: 20px;
            }

            .contact-float-btn {
                width: 45px;
                height: 45px;
            }

            .contact-float-btn i {
                font-size: 1.25rem;
            }

            .contact-tooltip {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content reveal">
                    <div class="hero-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>{{ __('100% Private & Secure') }}</span>
                    </div>
                    <h1 class="hero-title">
                        {{ __('Your Mental Health') }}<br>
                        <span class="gradient-text">{{ __('Matters Most') }}</span>
                    </h1>
                    <p class="hero-description">
                        {{ __('Tamman provides a safe, private, and stigma-free platform to connect with licensed mental health professionals. Start your journey to better mental health today.') }}
                    </p>

                    @if(!$isAuthenticated)
                        <div class="hero-buttons">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i> {{ __('Get Started Free') }}
                            </a>
                            <a href="{{ route('specialists.index') }}" class="btn btn-outline btn-lg">
                                <i class="fas fa-search"></i> {{ __('Find a Specialist') }}
                            </a>
                        </div>
                    @else
                        @if($userRole === 'patient')
                            <div class="hero-buttons">
                                <a href="{{ route('patient.dashboard') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('Go to Dashboard') }}
                                </a>
                                <a href="{{ route('patient.sessions') }}" class="btn btn-outline btn-lg">
                                    <i class="fas fa-calendar-check"></i> {{ __('Book a Session') }}
                                </a>
                            </div>
                        @elseif($userRole === 'specialist')
                            <div class="hero-buttons">
                                <a href="{{ route('specialist.dashboard') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('Go to Dashboard') }}
                                </a>
                                <a href="{{ route('specialist.schedule') }}" class="btn btn-outline btn-lg">
                                    <i class="fas fa-calendar-alt"></i> {{ __('Manage Schedule') }}
                                </a>
                            </div>
                        @elseif($userRole === 'admin')
                            <div class="hero-buttons">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('Go to Dashboard') }}
                                </a>
                                <a href="{{ route('admin.users') }}" class="btn btn-outline btn-lg">
                                    <i class="fas fa-users"></i> {{ __('Manage Users') }}
                                </a>
                            </div>
                        @endif
                    @endif

                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number counter" data-target="{{ $stats['total_users'] }}"
                                data-duration="2000">0</span>
                            <span class="stat-label">{{ __('Happy Users') }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number counter" data-target="{{ $stats['total_specialists'] }}"
                                data-duration="2000">0</span>
                            <span class="stat-label">{{ __('Certified Specialists') }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number counter" data-target="{{ $stats['total_sessions'] }}"
                                data-duration="2000">0</span>
                            <span class="stat-label">{{ __('Sessions Completed') }}</span>
                        </div>
                    </div>
                </div>
                <div class="hero-image reveal">
                    <div class="hero-image-wrapper">
                        <img src="{{ asset('images/hero-illustration.jpg') }}" alt="{{ __('Mental Health Support') }}"
                            onerror="this.src='https://placehold.co/500x500/8b5cf6/white?text=Tamman'">
                        <div class="floating-card card-1">
                            <i class="fas fa-smile"></i>
                            <span>{{ __('Mood Tracked') }}</span>
                        </div>
                        <div class="floating-card card-2">
                            <i class="fas fa-calendar-check"></i>
                            <span>{{ __('Session Booked') }}</span>
                        </div>
                        <div class="floating-card card-3">
                            <i class="fas fa-star"></i>
                            <span>{{ __('Points Earned') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">{{ __('Why Choose Us') }}</span>
                <h2 class="section-title">{{ __('Comprehensive Mental Health Support') }}</h2>
                <p class="section-description">
                    {{ __('We provide everything you need to take care of your mental well-being in one secure platform.') }}
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>{{ __('Secure Video Sessions') }}</h3>
                    <p>{{ __('Connect with your therapist through end-to-end encrypted video calls from anywhere, anytime.') }}
                    </p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>{{ __('Tamman Points Rewards') }}</h3>
                    <p>{{ __('Earn points by tracking your mood, attending sessions, and completing activities. Redeem for discounts!') }}
                    </p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>{{ __('Mood & Progress Tracking') }}</h3>
                    <p>{{ __('Track your daily mood and visualize your mental health journey with detailed analytics.') }}
                    </p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>{{ __('Self Assessments') }}</h3>
                    <p>{{ __('Take clinically-validated tests (PHQ-9, GAD-7) to better understand your mental health.') }}
                    </p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>{{ __('Complete Privacy') }}</h3>
                    <p>{{ __('Your data is encrypted and secure. All sessions are confidential and stigma-free.') }}</p>
                </div>
                <div class="feature-card reveal">
                    <div class="feature-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>{{ __('Donor Support System') }}</h3>
                    <p>{{ __('Financial assistance available for those who need it. No one is turned away due to cost.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">{{ __('Simple Process') }}</span>
                <h2 class="section-title">{{ __('How Tamman Works') }}</h2>
                <p class="section-description">
                    {{ __('Getting started with mental health support is easy with our 4-step process.') }}</p>
            </div>
            <div class="steps-grid">
                <div class="step reveal">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>{{ __('Create Account') }}</h3>
                    <p>{{ __('Sign up for free and complete your profile in just a few minutes.') }}</p>
                </div>
                <div class="step reveal">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>{{ __('Find a Specialist') }}</h3>
                    <p>{{ __('Browse our network of licensed specialists and filter by your needs.') }}</p>
                </div>
                <div class="step reveal">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>{{ __('Book a Session') }}</h3>
                    <p>{{ __('Choose a time that works for you and schedule your first session.') }}</p>
                </div>
                <div class="step reveal">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3>{{ __('Start Your Journey') }}</h3>
                    <p>{{ __('Connect with your specialist and begin your path to better mental health.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Specialists Preview Section -->
    <section class="specialists-preview">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">{{ __('Expert Care') }}</span>
                <h2 class="section-title">{{ __('Meet Our Specialists') }}</h2>
                <p class="section-description">
                    {{ __('All our mental health professionals are licensed, verified, and ready to help you.') }}</p>
            </div>
            <div class="specialists-grid" id="featuredSpecialists">
                @foreach($featuredSpecialists as $specialist)
                    @php
                        $specialistImage = $specialist->getProfileImageUrl();
                        $firstLetter = mb_substr($specialist->name, 0, 1, 'UTF-8');
                    @endphp
                    <div class="specialist-card reveal">
                        <div class="specialist-avatar">
                            @if($specialistImage)
                                <img src="{{ $specialistImage }}" alt="{{ $specialist->name }}">
                            @else
                                <div class="avatar-initials">{{ $firstLetter }}</div>
                            @endif
                        </div>
                        <h3>{{ $specialist->name }}</h3>
                        <p class="specialist-title">{{ $specialist->specialistProfile->specialization }}</p>
                        <div class="specialist-rating">
                            <i class="fas fa-star"></i>
                            <span>{{ number_format($specialist->specialistProfile->rating_avg, 1) }}</span>
                            <span class="reviews">({{ $specialist->specialistProfile->total_sessions ?? 0 }}
                                {{ __('sessions') }})</span>
                        </div>
                        <div class="specialist-specialties">
                            @foreach(explode(',', $specialist->specialistProfile->specialization) as $spec)
                                <span>{{ trim($spec) }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('specialists.show', $specialist) }}"
                            class="btn btn-outline btn-sm btn-block">{{ __('View Profile') }}</a>
                    </div>
                @endforeach
            </div>
            <div class="text-center reveal">
                <a href="{{ route('specialists.index') }}" class="btn btn-outline">{{ __('View All Specialists') }} <i
                        class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Tamman Points Section -->
    <section class="points-section">
        <div class="container">
            <div class="points-grid">
                <div class="points-content reveal">
                    <div class="points-badge">
                        <i class="fas fa-star"></i>
                        <span>{{ __('Reward System') }}</span>
                    </div>
                    <h2>{{ __('Earn Tamman Points') }}</h2>
                    <p>{{ __('Stay consistent with your mental health journey and earn rewards. Every positive action earns you points that can be redeemed for session discounts.') }}
                    </p>
                    <div class="points-list">
                        <div class="points-item">
                            <i class="fas fa-smile"></i>
                            <span>{{ __('Daily mood check-in') }}</span>
                            <strong>+5 {{ __('points') }}</strong>
                        </div>
                        <div class="points-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>{{ __('Attend a session') }}</span>
                            <strong>+50 {{ __('points') }}</strong>
                        </div>
                        <div class="points-item">
                            <i class="fas fa-clipboard-list"></i>
                            <span>{{ __('Complete assessment') }}</span>
                            <strong>+25 {{ __('points') }}</strong>
                        </div>
                        <div class="points-item">
                            <i class="fas fa-tasks"></i>
                            <span>{{ __('Complete treatment task') }}</span>
                            <strong>+15 {{ __('points') }}</strong>
                        </div>
                    </div>
                    @if(!$isAuthenticated)
                        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Start Earning Points') }}</a>
                    @elseif($userRole === 'patient')
                        <a href="{{ route('patient.rewards') }}" class="btn btn-primary">{{ __('View My Points') }}</a>
                    @endif
                </div>
                <div class="points-image reveal">
                    <div class="points-card">
                        <div class="points-header">
                            <i class="fas fa-star"></i>
                            <span>{{ __('Your Balance') }}</span>
                        </div>
                        <div class="points-balance">
                            <span
                                class="balance-number">{{ $isAuthenticated && $userRole === 'patient' ? number_format($userPoints) : '1,250' }}</span>
                            <span class="balance-label">{{ __('Tamman Points') }}</span>
                        </div>
                        <div class="points-redeem">
                            <p>{{ __('Redeem for:') }}</p>
                            <div class="redeem-options">
                                <span>500 {{ __('points') }} → 10% {{ __('discount') }}</span>
                                <span>1000 {{ __('points') }} → {{ __('Free session') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">{{ __('Success Stories') }}</span>
                <h2 class="section-title">{{ __('What Our Users Say') }}</h2>
                <p class="section-description">
                    {{ __('Hear from people who have transformed their mental health with Tamman.') }}</p>
            </div>
            <div class="testimonials-slider">
                <div class="testimonial-card reveal">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">
                        {{ __('Tamman changed my life. I was hesitant to seek help due to stigma, but the platform made it easy and private. My therapist is amazing!') }}
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <div class="avatar-placeholder">M</div>
                        </div>
                        <div class="author-info">
                            <h4>{{ __('Mona A.') }}</h4>
                            <div class="author-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">
                        {{ __('The points system keeps me motivated to track my mood daily. I love seeing my progress over time. Highly recommended!') }}
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <div class="avatar-placeholder">A</div>
                        </div>
                        <div class="author-info">
                            <h4>{{ __('Ahmed K.') }}</h4>
                            <div class="author-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">
                        {{ __('As someone who couldn\'t afford therapy, the donor support system was a blessing. Now I\'m getting the help I need. Thank you Tamman!') }}
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <div class="avatar-placeholder">R</div>
                        </div>
                        <div class="author-info">
                            <h4>{{ __('Rania S.') }}</h4>
                            <div class="author-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Donor Support Section -->
    <section class="donor-section">
        <div class="container">
            <div class="donor-grid">
                <div class="donor-content reveal">
                    <h2>{{ __('Support Those in Need') }}</h2>
                    <p>{{ __('Become a donor and help provide mental health support to individuals who cannot afford it. Your contribution makes a real difference in someone\'s life.') }}
                    </p>
                    <div class="donor-stats">
                        <div class="donor-stat">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">{{ __('Users Supported') }}</span>
                        </div>
                        <div class="donor-stat">
                            <span class="stat-number">$50K+</span>
                            <span class="stat-label">{{ __('Donations Given') }}</span>
                        </div>
                    </div>
                    @if(!$isAuthenticated)
                        <a href="{{ route('login') }}" class="btn btn-secondary">{{ __('Become a Donor') }} <i
                                class="fas fa-heart"></i></a>
                    @elseif($userRole === 'patient')
                        <a href="{{ route('donate') }}" class="btn btn-secondary">{{ __('Become a Donor') }} <i
                                class="fas fa-heart"></i></a>
                    @elseif($userRole === 'specialist')
                        <a href="{{ route('donate') }}" class="btn btn-secondary">{{ __('Become a Donor') }} <i
                                class="fas fa-heart"></i></a>
                    @endif
                </div>
                <div class="donor-image reveal">
                    <div class="donor-illustration">
                        <i class="fas fa-hand-holding-heart"
                            style="font-size: 120px !important; color: #7c3aed !important;"></i>
                        <span class="donor-circle-text">{{ __('Supporting Mental Health') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Resources Section -->
    <section class="resources">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">{{ __('Learn More') }}</span>
                <h2 class="section-title">{{ __('Mental Health Resources') }}</h2>
                <p class="section-description">
                    {{ __('Access free educational content to better understand and manage your mental health.') }}</p>
            </div>
            <div class="resources-grid">
                @foreach($recentResources as $resource)
                    <div class="resource-card reveal">
                        <div class="resource-icon">
                            <i
                                class="fas {{ $resource->type == 'article' ? 'fa-newspaper' : ($resource->type == 'video' ? 'fa-video' : 'fa-heart') }}"></i>
                        </div>
                        <h3>{{ $resource->title }}</h3>
                        <p>{{ Str::limit(preg_replace('/<[^>]*>/', '', $resource->body), 100) }}</p>
                        <a href="{{ route('resources.show', $resource) }}" class="resource-link">{{ __('Read More') }} <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section - Show only for guests (not logged in) -->
    @if(!$isAuthenticated)
        <section class="cta-section">
            <div class="container">
                <div class="cta-content reveal">
                    <h2>{{ __('Ready to Start Your Journey?') }}</h2>
                    <p>{{ __('Join thousands of people who have found support and healing through Tamman.') }}</p>
                    <div class="cta-buttons">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Sign Up Free') }}</a>
                        <a href="{{ route('specialist.apply') }}"
                            class="btn btn-outline btn-lg">{{ __('Become a Specialist') }}</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Floating Contact Button - Redirects to Contact Page -->
    <div class="floating-contact">
        <a href="{{ route('contact') }}" class="contact-float-btn" data-bs-toggle="popover" data-bs-trigger="hover"
            data-bs-content="{{ __('Need help? Contact us') }}" data-bs-placement="left"
            data-bs-delay='{"show":300,"hide":100}'>
            <i class="fas fa-headset"></i>
        </a>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Popover initialization
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    trigger: 'hover',
                    delay: { show: 300, hide: 100 }
                });
            });
        });
    </script>
@endpush