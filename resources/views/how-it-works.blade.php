{{-- resources/views/how-it-works.blade.php --}}
@extends('layouts.guest')

@section('title', __('How It Works - Tamman Mental Health Platform'))

@section('description', __('Learn how Tamman works to provide accessible, private, and stigma-free mental health support. Simple 4-step process to start your journey.'))

@section('content')

<!-- Hero Section -->
<section class="how-hero">
    <div class="container">
        <div class="how-hero-content reveal">
            <div class="how-hero-badge">
                <i class="fas fa-play-circle"></i>
                <span>{{ __('Simple & Easy') }}</span>
            </div>
            <h1 class="how-hero-title">
                {{ __('How') }} <span class="gradient-text">{{ __('Tamman Works') }}</span>
            </h1>
            <p class="how-hero-description">
                {{ __('Getting started with mental health support has never been easier. Follow these simple steps to begin your journey toward better mental well-being.') }}
            </p>
            <div class="how-hero-buttons">
                @if(!$isAuthenticated)
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i> {{ __('Get Started Now') }}
                    </a>
                    <a href="{{ route('specialists.index') }}" class="btn btn-outline btn-lg">
                        <i class="fas fa-search"></i> {{ __('Find a Specialist') }}
                    </a>
                @else
                    @if($userRole === 'patient')
                        <a href="{{ route('patient.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt"></i> {{ __('Go to Dashboard') }}
                        </a>
                        <a href="{{ route('patient.sessions') }}" class="btn btn-outline btn-lg">
                            <i class="fas fa-calendar-check"></i> {{ __('Book a Session') }}
                        </a>
                    @elseif($userRole === 'specialist')
                        <a href="{{ route('specialist.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt"></i> {{ __('Go to Dashboard') }}
                        </a>
                        <a href="{{ route('specialist.schedule') }}" class="btn btn-outline btn-lg">
                            <i class="fas fa-calendar-alt"></i> {{ __('Manage Schedule') }}
                        </a>
                    @elseif($userRole === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt"></i> {{ __('Go to Dashboard') }}
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline btn-lg">
                            <i class="fas fa-users"></i> {{ __('Manage Users') }}
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Steps Section -->
<section class="steps-detailed">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">{{ __('4 Simple Steps') }}</span>
            <h2 class="section-title">{{ __('Your Journey to Better Mental Health') }}</h2>
            <p class="section-description">{{ __('We\'ve made the process simple, secure, and supportive every step of the way.') }}</p>
        </div>
        
        <div class="steps-container">
            <!-- Step 1 -->
            <div class="step-item reveal">
                <div class="step-number-wrapper">
                    <div class="step-number">01</div>
                    <div class="step-line"></div>
                </div>
                <div class="step-content">
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>{{ __('Create Your Account') }}</h3>
                    <p>{{ __('Sign up for free in just 2 minutes. Provide basic information and create your secure profile. Your privacy is our priority.') }}</p>
                    <ul class="step-features">
                        <li><i class="fas fa-check-circle"></i> {{ __('Free registration') }}</li>
                        <li><i class="fas fa-check-circle"></i> {{ __('No commitment required') }}</li>
                        <li><i class="fas fa-check-circle"></i> {{ __('Complete privacy protection') }}</li>
                    </ul>
                    <div class="step-stat">
                        <span class="stat-number">{{ number_format($stats['total_users']) }}+</span>
                        <span class="stat-label">{{ __('Users Joined') }}</span>
                    </div>
                    @if(!$isAuthenticated)
                        <div class="step-action">
                            <a href="{{ route('register') }}" class="btn-step">{{ __('Sign Up Free') }} <i class="fas fa-arrow-right"></i></a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="step-item reveal">
                <div class="step-number-wrapper">
                    <div class="step-number">02</div>
                    <div class="step-line"></div>
                </div>
                <div class="step-content">
                    <div class="step-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>{{ __('Find Your Specialist') }}</h3>
                    <p>{{ __('Browse our network of licensed mental health professionals. Filter by specialization, language, gender, price, and availability.') }}</p>
                    <ul class="step-features">
                        <li><i class="fas fa-check-circle"></i> {{ __('200+ verified specialists') }}</li>
                        <li><i class="fas fa-check-circle"></i> {{ __('Multiple specializations') }}</li>
                        <li><i class="fas fa-check-circle"></i> {{ __('Read patient reviews') }}</li>
                    </ul>
                    <div class="step-stat">
                        <span class="stat-number">{{ number_format($stats['total_specialists']) }}+</span>
                        <span class="stat-label">{{ __('Certified Specialists') }}</span>
                    </div>
                    @if($isAuthenticated && $userRole === 'patient')
                        <div class="step-action">
                            <a href="{{ route('specialists.index') }}" class="btn-step">{{ __('Find a Specialist') }} <i class="fas fa-arrow-right"></i></a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="step-item reveal">
                <div class="step-number-wrapper">
                    <div class="step-number">03</div>
                    <div class="step-line"></div>
                </div>
                <div class="step-content">
                    <div class="step-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>{{ __('Book & Attend Sessions') }}</h3>
                    <p>{{ __('Choose a time that works for you. Attend secure video, audio, or text sessions from anywhere, anytime.') }}</p>
                    <ul class="step-features">
                        <li><i class="fas fa-check-circle"></i> {{ __('Flexible scheduling') }}</li>
                        <li><i class="fas fa-check-circle"></i> {{ __('End-to-end encryption') }}</li>
                        <li><i class="fas fa-check-circle"></i> {{ __('Session reminders') }}</li>
                    </ul>
                    <div class="step-stat">
                        <span class="stat-number">{{ number_format($stats['total_sessions']) }}+</span>
                        <span class="stat-label">{{ __('Sessions Completed') }}</span>
                    </div>
                    @if($isAuthenticated && $userRole === 'patient')
                        <div class="step-action">
                            <a href="{{ route('patient.sessions') }}" class="btn-step">{{ __('My Sessions') }} <i class="fas fa-arrow-right"></i></a>
                        </div>
                    @elseif(!$isAuthenticated)
                        <div class="step-action">
                            <a href="{{ route('register') }}" class="btn-step">{{ __('Get Started') }} <i class="fas fa-arrow-right"></i></a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Step 4 -->
            <div class="step-item reveal">
                <div class="step-number-wrapper">
                    <div class="step-number">04</div>
                </div>
                <div class="step-content">
                    <div class="step-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>{{ __('Track & Grow') }}</h3>
                    <p>{{ __('Monitor your progress with mood tracking, self-assessments, and earn Tamman Points for staying consistent.') }}</p>
                    <ul class="step-features">
                        <li><i class="fas fa-check-circle"></i> {{ __('Daily mood tracking') }}</li>
                        <li><i class="fas fa-check-circle"></i> {{ __('Psychological assessments') }}</li>
                        <li><i class="fas fa-check-circle"></i> {{ __('Earn rewards points') }}</li>
                    </ul>
                    <div class="step-stat">
                        <span class="stat-number">{{ number_format($stats['total_points_awarded'] ?? 10000) }}+</span>
                        <span class="stat-label">{{ __('Points Awarded') }}</span>
                    </div>
                    @if($isAuthenticated && $userRole === 'patient')
                        <div class="step-action">
                            <a href="{{ route('patient.mood-tracker') }}" class="btn-step">{{ __('Track Mood') }} <i class="fas fa-arrow-right"></i></a>
                        </div>
                    @elseif(!$isAuthenticated)
                        <div class="step-action">
                            <a href="{{ route('register') }}" class="btn-step">{{ __('Start Tracking') }} <i class="fas fa-arrow-right"></i></a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid Section -->
<section class="features-showcase">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">{{ __('Why Choose Us') }}</span>
            <h2 class="section-title">{{ __('What Makes Tamman Different') }}</h2>
            <p class="section-description">{{ __('We combine professional care with modern technology to provide the best experience.') }}</p>
        </div>
        
        <div class="features-showcase-grid">
            <div class="feature-showcase-card reveal">
                <div class="feature-showcase-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>{{ __('100% Private & Secure') }}</h3>
                <p>{{ __('End-to-end encryption for all communications. Your data is protected and never shared.') }}</p>
            </div>
            <div class="feature-showcase-card reveal">
                <div class="feature-showcase-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>{{ __('Reward System') }}</h3>
                <p>{{ __('Earn Tamman Points for healthy habits. Redeem for discounts on sessions.') }}</p>
            </div>
            <div class="feature-showcase-card reveal">
                <div class="feature-showcase-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3>{{ __('Donor Support') }}</h3>
                <p>{{ __('Financial assistance available for those who need it. No one is turned away.') }}</p>
            </div>
            <div class="feature-showcase-card reveal">
                <div class="feature-showcase-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>{{ __('24/7 Access') }}</h3>
                <p>{{ __('Access resources and support anytime, anywhere. Book sessions at your convenience.') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Session Types Section -->
<section class="session-types-showcase">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">{{ __('Flexible Options') }}</span>
            <h2 class="section-title">{{ __('Choose Your Session Type') }}</h2>
            <p class="section-description">{{ __('We offer multiple ways to connect with your specialist. Choose what works best for you.') }}</p>
        </div>
        
        <div class="session-types-grid">
            <div class="session-type-card reveal">
                <div class="session-type-icon animated-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h3>{{ __('Video Sessions') }}</h3>
                <p>{{ __('Face-to-face therapy from anywhere. High-quality encrypted video calls.') }}</p>
                <div class="session-type-tags">
                    <span><i class="fas fa-check"></i> {{ __('HD Quality') }}</span>
                    <span><i class="fas fa-check"></i> {{ __('Screen Share') }}</span>
                    <span><i class="fas fa-check"></i> {{ __('Recording Available') }}</span>
                </div>
            </div>
            <div class="session-type-card reveal">
                <div class="session-type-icon animated-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h3>{{ __('Audio Sessions') }}</h3>
                <p>{{ __('Perfect for those who prefer voice-only conversations. Private and convenient.') }}</p>
                <div class="session-type-tags">
                    <span><i class="fas fa-check"></i> {{ __('Crystal Clear') }}</span>
                    <span><i class="fas fa-check"></i> {{ __('Low Bandwidth') }}</span>
                    <span><i class="fas fa-check"></i> {{ __('Mobile Friendly') }}</span>
                </div>
            </div>
            <div class="session-type-card reveal">
                <div class="session-type-icon animated-icon">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <h3>{{ __('Text Chat') }}</h3>
                <p>{{ __('Real-time messaging with your specialist. Great for quick check-ins.') }}</p>
                <div class="session-type-tags">
                    <span><i class="fas fa-check"></i> {{ __('Real-time') }}</span>
                    <span><i class="fas fa-check"></i> {{ __('Chat History') }}</span>
                    <span><i class="fas fa-check"></i> {{ __('Media Sharing') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-showcase">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">{{ __('Common Questions') }}</span>
            <h2 class="section-title">{{ __('Frequently Asked Questions') }}</h2>
            <p class="section-description">{{ __('Find answers to common questions about Tamman.') }}</p>
        </div>
        
        <div class="faq-container" id="faqContainer">
            @foreach($faqs as $index => $faq)
            <div class="faq-item">
                <div class="faq-question" data-faq-id="{{ $index }}">
                    <h3>{{ __($faq['question']) }}</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>{{ __($faq['answer']) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section - Show only for guests -->
@if(!$isAuthenticated)
<section class="cta-section">
    <div class="container">
        <div class="cta-content reveal">
            <h2>{{ __('Ready to Start Your Journey?') }}</h2>
            <p>{{ __('Join thousands of people who have found support and healing through Tamman.') }}</p>
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Sign Up Free') }}</a>
                <a href="{{ route('specialist.apply') }}" class="btn btn-outline btn-lg">{{ __('Become a Specialist') }}</a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Spacer between CTA and Footer -->
<div style="height: 40px;"></div>

@endsection

@push('styles')
<style>
    /* ========== HOW IT WORKS PAGE STYLES ========== */
    
    /* Hero Section */
    .how-hero {
        padding: 80px 0 60px;
        background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
        text-align: center;
    }
    
    .how-hero-content {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .how-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(139, 92, 246, 0.1);
        padding: 8px 20px;
        border-radius: 50px;
        margin-bottom: 24px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #7c3aed;
    }
    
    .how-hero-title {
        font-size: clamp(2rem, 5vw, 3.5rem);
        margin-bottom: 20px;
        line-height: 1.2;
    }
    
    .how-hero-description {
        font-size: 1.125rem;
        color: #4b5563;
        margin-bottom: 32px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .how-hero-buttons {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    /* Section Header */
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
    
    /* Steps Section */
    .steps-detailed {
        padding: 80px 0;
        background: white;
    }
    
    .steps-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .step-item {
        display: flex;
        margin-bottom: 50px;
        position: relative;
    }
    
    .step-item:last-child {
        margin-bottom: 0;
    }
    
    .step-number-wrapper {
        flex-shrink: 0;
        width: 100px;
        text-align: center;
        position: relative;
    }
    
    .step-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.3);
    }
    
    .step-line {
        position: absolute;
        top: 60px;
        left: 50%;
        width: 2px;
        height: calc(100% + 50px);
        background: linear-gradient(to bottom, #c4b5fd, transparent);
        transform: translateX(-50%);
        z-index: 1;
    }
    
    .step-item:last-child .step-line {
        display: none;
    }
    
    .step-content {
        flex: 1;
        padding-left: 30px;
        padding-bottom: 20px;
    }
    
    .step-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    
    .step-icon i {
        font-size: 2rem;
        color: #7c3aed;
    }
    
    .step-content h3 {
        font-size: 1.5rem;
        margin-bottom: 12px;
        color: #1f2937;
    }
    
    .step-content > p {
        color: #6b7280;
        margin-bottom: 16px;
        line-height: 1.6;
    }
    
    .step-features {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .step-features li {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .step-features li i {
        color: #10b981;
        font-size: 0.875rem;
    }
    
    .step-stat {
        display: inline-flex;
        align-items: baseline;
        gap: 8px;
        background: #f9fafb;
        padding: 8px 20px;
        border-radius: 50px;
        margin-bottom: 16px;
    }
    
    .step-stat .stat-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: #7c3aed;
    }
    
    .step-stat .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .step-action {
        margin-top: 16px;
    }
    
    .btn-step {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        background: #7c3aed;
        color: white;
        border-radius: 40px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-step:hover {
        background: #6d28d9;
        transform: translateY(-2px);
        color: white;
    }
    
    /* Features Showcase */
    .features-showcase {
        padding: 80px 0;
        background: #f9fafb;
    }
    
    .features-showcase-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }
    
    .feature-showcase-card {
        background: white;
        padding: 32px 24px;
        border-radius: 20px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .feature-showcase-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: #c4b5fd;
    }
    
    .feature-showcase-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .feature-showcase-icon i {
        font-size: 2rem;
        color: #7c3aed;
    }
    
    .feature-showcase-card h3 {
        font-size: 1.125rem;
        margin-bottom: 12px;
    }
    
    .feature-showcase-card p {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.6;
        margin: 0;
    }
    
    /* Session Types */
    .session-types-showcase {
        padding: 80px 0;
        background: white;
    }
    
    .session-types-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
    
    .session-type-card {
        background: white;
        padding: 32px 24px;
        border-radius: 20px;
        text-align: center;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .session-type-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: #c4b5fd;
    }
    
    .session-type-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.3);
        transition: all 0.3s ease;
    }
    
    /* Animated Icon */
    .animated-icon {
        animation: iconPulse 2s ease-in-out infinite;
    }
    
    .animated-icon:hover {
        animation: iconBounce 0.5s ease-in-out;
    }
    
    @keyframes iconPulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.3);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 15px 35px -5px rgba(139, 92, 246, 0.5);
        }
    }
    
    @keyframes iconBounce {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    .session-type-icon i {
        font-size: 2.5rem;
        color: white;
        transition: transform 0.3s ease;
    }
    
    .session-type-card:hover .session-type-icon i {
        transform: scale(1.1);
    }
    
    .session-type-card h3 {
        font-size: 1.25rem;
        margin-bottom: 12px;
    }
    
    .session-type-card > p {
        color: #6b7280;
        margin-bottom: 20px;
        font-size: 0.875rem;
        line-height: 1.6;
    }
    
    .session-type-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }
    
    .session-type-tags span {
        background: #f3f4f6;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.7rem;
        color: #374151;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .session-type-tags span i {
        color: #10b981;
        font-size: 0.65rem;
    }
    
    /* FAQ Section */
    .faq-showcase {
        padding: 80px 0;
        background: #f9fafb;
    }
    
    .faq-container {
        max-width: 800px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .faq-item {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .faq-item:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .faq-question {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .faq-question:hover {
        background: #f9fafb;
    }
    
    .faq-question h3 {
        font-size: 1.125rem;
        margin: 0;
        color: #1f2937;
        font-weight: 600;
        flex: 1;
    }
    
    .faq-question i {
        color: #7c3aed;
        transition: transform 0.3s ease;
        font-size: 1rem;
        margin-left: 16px;
    }
    
    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }
    
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 0 24px;
    }
    
    .faq-item.active .faq-answer {
        max-height: 300px;
        padding: 0 24px 20px 24px;
    }
    
    .faq-answer p {
        color: #6b7280;
        line-height: 1.6;
        margin: 0;
    }
    
    /* CTA Section */
    .cta-section {
        padding: 60px 0;
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        color: white;
        text-align: center;
        margin-top: 0;
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
    
    /* RTL Support */
    body.rtl .step-content {
        padding-left: 0;
        padding-right: 30px;
    }
    
    body.rtl .step-features li {
        flex-direction: row-reverse;
    }
    
    body.rtl .faq-question {
        flex-direction: row;
    }
    
    body.rtl .faq-question i {
        margin-left: 0;
        margin-right: 16px;
    }
    
    body.rtl .btn-step i {
        transform: rotate(180deg);
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .features-showcase-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .session-types-grid {
            grid-template-columns: 1fr;
            max-width: 450px;
            margin: 0 auto;
        }
        
        .step-item {
            flex-direction: column;
            text-align: center;
        }
        
        .step-number-wrapper {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .step-line {
            display: none;
        }
        
        .step-content {
            padding-left: 0;
            text-align: center;
        }
        
        .step-icon {
            margin: 0 auto 20px;
        }
        
        .step-features {
            justify-content: center;
        }
        
        .step-stat {
            justify-content: center;
        }
        
        body.rtl .step-content {
            padding-right: 0;
        }
    }
    
    @media (max-width: 768px) {
        .how-hero {
            padding: 40px 0;
        }
        
        .how-hero-buttons {
            flex-direction: column;
        }
        
        .how-hero-buttons .btn {
            width: 100%;
        }
        
        .features-showcase-grid {
            grid-template-columns: 1fr;
        }
        
        .faq-question h3 {
            font-size: 1rem;
        }
        
        .step-content h3 {
            font-size: 1.25rem;
        }
        
        .step-features {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .faq-question {
            padding: 16px;
        }
    }
    
    @media (max-width: 480px) {
        .step-number {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
        
        .step-icon {
            width: 55px;
            height: 55px;
        }
        
        .step-icon i {
            font-size: 1.5rem;
        }
        
        .faq-question h3 {
            font-size: 0.875rem;
        }
    }
</style>

<script>
    // FAQ Accordion Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(function(item) {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', function() {
                const isActive = item.classList.contains('active');
                
                faqItems.forEach(function(otherItem) {
                    otherItem.classList.remove('active');
                });
                
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    });
</script>
@endpush