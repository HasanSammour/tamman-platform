{{-- resources/views/help-center.blade.php --}}
@extends('layouts.guest')

@section('title', __('Help Center') . ' - ' . __('Tamman'))

@section('description', __('Find answers to common questions, guides, and tutorials about Tamman platform.'))

@section('content')
    <div class="help-center">
        <!-- Hero Section -->
        <div class="help-hero">
            <div class="container">
                <div class="hero-content animate-fade-in-up">
                    <div class="hero-badge">
                        <i class="fas fa-headset"></i>
                        <span>{{ __('24/7 Support Available') }}</span>
                    </div>
                    <h1>{{ __('How can we help you?') }}</h1>
                    <p>{{ __('Find answers to common questions, browse our guides, or contact our support team.') }}</p>
                </div>
            </div>
            <div class="hero-wave-container">
                <svg class="hero-wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120"
                    preserveAspectRatio="none">
                    <path class="wave-path" fill="#ffffff"
                        d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,53.3C1120,53,1280,75,1360,85.3L1440,96L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z">
                    </path>
                </svg>
            </div>
        </div>

        <!-- Quick Guides Section -->
        <section class="quick-guides">
            <div class="container">
                <div class="section-header animate-fade-in-up">
                    <span class="section-badge">{{ __('Quick Guides') }}</span>
                    <h2>{{ __('Get Started Quickly') }}</h2>
                    <p>{{ __('Step-by-step guides to help you make the most of Tamman') }}</p>
                </div>
                <div class="guides-grid">
                    <div class="guide-card animate-scale-in" data-guide="getting-started">
                        <div class="guide-icon" style="background: #7c3aed20; color: #7c3aed">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3>{{ __('Getting Started Guide') }}</h3>
                        <p>{{ __('Learn how to create your account and start your mental health journey.') }}</p>
                        <button class="guide-link guide-modal-btn" data-guide="getting-started">
                            {{ __('Learn More') }} <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="guide-card animate-scale-in" data-guide="booking">
                        <div class="guide-icon" style="background: #10b98120; color: #10b981">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <h3>{{ __('How to Book a Session') }}</h3>
                        <p>{{ __('Step-by-step guide to finding and booking the right specialist for you.') }}</p>
                        <button class="guide-link guide-modal-btn" data-guide="booking">
                            {{ __('Learn More') }} <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="guide-card animate-scale-in" data-guide="points">
                        <div class="guide-icon" style="background: #f59e0b20; color: #f59e0b">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>{{ __('Using Tamman Points') }}</h3>
                        <p>{{ __('Everything you need to know about earning and redeeming points.') }}</p>
                        <button class="guide-link guide-modal-btn" data-guide="points">
                            {{ __('Learn More') }} <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="guide-card animate-scale-in" data-guide="privacy">
                        <div class="guide-icon" style="background: #ef444420; color: #ef4444">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>{{ __('Privacy & Security') }}</h3>
                        <p>{{ __('Learn how we protect your data and ensure your privacy.') }}</p>
                        <button class="guide-link guide-modal-btn" data-guide="privacy">
                            {{ __('Learn More') }} <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section" id="faq">
            <div class="container">
                <div class="section-header animate-fade-in-up">
                    <span class="section-badge">{{ __('FAQ') }}</span>
                    <h2>{{ __('Frequently Asked Questions') }}</h2>
                    <p>{{ __('Find answers to the most common questions about Tamman') }}</p>
                </div>

                <div class="faq-categories animate-fade-in-up">
                    @foreach($faqs as $key => $category)
                        <button class="category-btn {{ $loop->first ? 'active' : '' }}" data-category="{{ $key }}">
                            <i class="{{ $category['icon'] }}"></i>
                            <span>{{ $category['title'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="faq-content">
                    @foreach($faqs as $key => $category)
                        <div class="faq-category-content {{ $loop->first ? 'active' : '' }}" data-category="{{ $key }}">
                            @foreach($category['questions'] as $index => $faq)
                                <div class="faq-item animate-fade-in-up" style="animation-delay: {{ $index * 0.05 }}s">
                                    <div class="faq-question">
                                        <h3>{{ $faq['question'] }}</h3>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="faq-answer">
                                        <p>{{ $faq['answer'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Video Tutorials Section -->
        <section class="video-tutorials">
            <div class="container">
                <div class="section-header animate-fade-in-up">
                    <span class="section-badge">{{ __('Video Tutorials') }}</span>
                    <h2>{{ __('Watch & Learn') }}</h2>
                    <p>{{ __('Visual guides to help you navigate the platform') }}</p>
                </div>
                <div class="videos-grid">
                    @foreach($videoTutorials as $index => $video)
                        <div class="video-card animate-scale-in" data-video-index="{{ $index }}">
                            <div class="video-thumbnail">
                                <div class="thumbnail-placeholder"
                                    style="background: linear-gradient(135deg, #7c3aed, #a78bfa);">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                                <span class="video-duration">{{ $video['duration'] }}</span>
                            </div>
                            <h3>{{ $video['title'] }}</h3>
                            <button class="video-link watch-video-btn" data-video-index="{{ $index }}">
                                {{ __('Watch Tutorial') }} <i class="fas fa-play"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Still Need Help Section -->
        <section class="still-need-help">
            <div class="container">
                <div class="help-card animate-scale-in">
                    <div class="help-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h2>{{ __('Still Need Help?') }}</h2>
                    <p>{{ __('Our support team is here to assist you. Reach out to us anytime.') }}</p>
                    <div class="help-buttons">
                        <a href="{{ route('contact') }}" class="btn btn-primary">
                            <i class="fas fa-envelope"></i> {{ __('Contact Us') }}
                        </a>
                        <a href="mailto:support@tamman.ps" class="btn btn-outline">
                            <i class="fas fa-envelope"></i> support@tamman.ps
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Getting Started Modal -->
    <div id="modal-getting-started" class="guide-modal">
        <div class="guide-modal-overlay"></div>
        <div class="guide-modal-container">
            <div class="guide-modal-header">
                <div class="modal-icon" style="background: #7c3aed20; color: #7c3aed">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3>{{ __('Getting Started Guide') }}</h3>
                <button class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="guide-modal-body">
                <div class="guide-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>{{ __('Create Your Account') }}</h4>
                        <p>{{ __('Click the "Sign Up" button on the homepage. Fill in your name, email, and password. The process takes less than 2 minutes.') }}
                        </p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>{{ __('Verify Your Email') }}</h4>
                        <p>{{ __('Check your inbox for a verification email. Click the link to verify your account and unlock all features.') }}
                        </p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>{{ __('Complete Your Profile') }}</h4>
                        <p>{{ __('Add your profile picture, phone number, and other details to help specialists understand your needs better.') }}
                        </p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>{{ __('Start Exploring') }}</h4>
                        <p>{{ __('Browse specialists, take self-assessments, track your mood, or book your first therapy session.') }}
                        </p>
                    </div>
                </div>
                <div class="guide-tip">
                    <i class="fas fa-lightbulb"></i>
                    <p>{{ __('Tip: Complete your profile to get personalized specialist recommendations!') }}</p>
                </div>
            </div>
            <div class="guide-modal-footer">
                <button class="btn-close-modal">{{ __('Got it!') }}</button>
            </div>
        </div>
    </div>

    <!-- Booking Session Modal -->
    <div id="modal-booking" class="guide-modal">
        <div class="guide-modal-overlay"></div>
        <div class="guide-modal-container">
            <div class="guide-modal-header">
                <div class="modal-icon" style="background: #10b98120; color: #10b981">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3>{{ __('How to Book a Session') }}</h3>
                <button class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="guide-modal-body">
                <div class="guide-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>{{ __('Find a Specialist') }}</h4>
                        <p>{{ __('Go to the "Find a Specialist" page. Use filters like specialization, language, gender, price, and availability to narrow down your search.') }}
                        </p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>{{ __('View Specialist Profile') }}</h4>
                        <p>{{ __('Click on a specialist to view their full profile, including qualifications, experience, patient reviews, and session types offered.') }}
                        </p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>{{ __('Choose Session Type') }}</h4>
                        <p>{{ __('Select your preferred session type: Video (face-to-face), Audio (voice-only), or Text Chat (real-time messaging).') }}
                        </p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>{{ __('Select Date & Time') }}</h4>
                        <p>{{ __('Choose an available date and time slot from the specialist\'s schedule. Slots are shown in your local timezone.') }}
                        </p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h4>{{ __('Confirm & Pay') }}</h4>
                        <p>{{ __('Review your booking details, choose your payment method (credit, points, or cash), and confirm your booking.') }}
                        </p>
                    </div>
                </div>
                <div class="guide-tip">
                    <i class="fas fa-lightbulb"></i>
                    <p>{{ __('Tip: Book at least 24 hours in advance to secure your preferred time slot!') }}</p>
                </div>
            </div>
            <div class="guide-modal-footer">
                <button class="btn-close-modal">{{ __('Got it!') }}</button>
            </div>
        </div>
    </div>

    <!-- Tamman Points Modal -->
    <div id="modal-points" class="guide-modal">
        <div class="guide-modal-overlay"></div>
        <div class="guide-modal-container">
            <div class="guide-modal-header">
                <div class="modal-icon" style="background: #f59e0b20; color: #f59e0b">
                    <i class="fas fa-star"></i>
                </div>
                <h3>{{ __('Using Tamman Points') }}</h3>
                <button class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="guide-modal-body">
                <div class="points-summary">
                    <div class="points-earn">
                        <h4><i class="fas fa-plus-circle"></i> {{ __('How to Earn Points') }}</h4>
                        <ul>
                            <li><strong>+5 {{ __('points') }}</strong> - {{ __('Daily mood check-in') }}</li>
                            <li><strong>+50 {{ __('points') }}</strong> - {{ __('Attend a session') }}</li>
                            <li><strong>+25 {{ __('points') }}</strong> - {{ __('Complete psychological assessment') }}</li>
                            <li><strong>+15 {{ __('points') }}</strong> - {{ __('Finish treatment task') }}</li>
                            <li><strong>+3 {{ __('points') }}</strong> - {{ __('Rate a specialist') }}</li>
                            <li><strong>+100 {{ __('points') }}</strong> - {{ __('Refer a friend') }}</li>
                            <li><strong>+50 {{ __('points') }}</strong> - {{ __('7-day streak bonus') }}</li>
                            <li><strong>+100 {{ __('points') }}</strong> - {{ __('30-day streak bonus') }}</li>
                        </ul>
                    </div>
                    <div class="points-redeem">
                        <h4><i class="fas fa-gift"></i> {{ __('How to Redeem Points') }}</h4>
                        <ul>
                            <li><strong>500 {{ __('points') }}</strong> → 10% {{ __('discount on next session') }}</li>
                            <li><strong>1000 {{ __('points') }}</strong> → {{ __('Free session') }}</li>
                            <li><strong>2000 {{ __('points') }}</strong> → {{ __('Two free sessions') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="guide-tip">
                    <i class="fas fa-lightbulb"></i>
                    <p>{{ __('Tip: Track your mood daily to build streaks and earn bonus points!') }}</p>
                </div>
            </div>
            <div class="guide-modal-footer">
                <button class="btn-close-modal">{{ __('Got it!') }}</button>
            </div>
        </div>
    </div>

    <!-- Privacy & Security Modal -->
    <div id="modal-privacy" class="guide-modal">
        <div class="guide-modal-overlay"></div>
        <div class="guide-modal-container">
            <div class="guide-modal-header">
                <div class="modal-icon" style="background: #ef444420; color: #ef4444">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>{{ __('Privacy & Security') }}</h3>
                <button class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="guide-modal-body">
                <div class="privacy-section">
                    <div class="privacy-item">
                        <i class="fas fa-lock"></i>
                        <div>
                            <h4>{{ __('End-to-End Encryption') }}</h4>
                            <p>{{ __('All communications between you and your specialist are fully encrypted, ensuring complete privacy.') }}
                            </p>
                        </div>
                    </div>
                    <div class="privacy-item">
                        <i class="fas fa-database"></i>
                        <div>
                            <h4>{{ __('Data Protection') }}</h4>
                            <p>{{ __('Your personal information is stored securely and never shared with third parties without your consent.') }}
                            </p>
                        </div>
                    </div>
                    <div class="privacy-item">
                        <i class="fas fa-user-secret"></i>
                        <div>
                            <h4>{{ __('Anonymous Options') }}</h4>
                            <p>{{ __('You can choose to use a nickname and keep your identity hidden from other users.') }}
                            </p>
                        </div>
                    </div>
                    <div class="privacy-item">
                        <i class="fas fa-file-contract"></i>
                        <div>
                            <h4>{{ __('HIPAA Compliant') }}</h4>
                            <p>{{ __('Our platform follows strict healthcare data protection standards to keep your information safe.') }}
                            </p>
                        </div>
                    </div>
                    <div class="privacy-item">
                        <i class="fas fa-trash-alt"></i>
                        <div>
                            <h4>{{ __('Data Deletion') }}</h4>
                            <p>{{ __('You can request complete deletion of your account and all associated data at any time.') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="guide-tip">
                    <i class="fas fa-lightbulb"></i>
                    <p>{{ __('Tip: Always log out after using shared devices for extra security!') }}</p>
                </div>
            </div>
            <div class="guide-modal-footer">
                <button class="btn-close-modal">{{ __('Got it!') }}</button>
            </div>
        </div>
    </div>

    <!-- Video Player Modal - Fixed Size No Scrollbars -->
    <div id="video-player-modal" class="guide-modal">
        <div class="guide-modal-overlay"></div>
        <div class="video-modal-container">
            <div class="video-modal-header">
                <div class="video-modal-icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <h3 id="videoModalTitle">{{ __('Watch Tutorial') }}</h3>
                <button class="video-modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="video-modal-body">
                <div class="video-player-wrapper">
                    <video id="videoPlayer" class="video-player" controls preload="metadata" controlsList="nodownload">
                        <source id="videoSource" src="" type="video/mp4">
                        {{ __('Your browser does not support the video tag.') }}
                    </video>
                </div>
                <div class="video-controls-note">
                    <i class="fas fa-info-circle"></i>
                    <p>{{ __('You can pause, play, and adjust volume using the video controls. For best experience, use a stable internet connection.') }}
                    </p>
                </div>
            </div>
            <div class="video-modal-footer">
                <button class="btn-close-video-modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .help-center {
                background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
                min-height: 100vh;
            }

            /* Hero Section */
            .help-hero {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                padding: 80px 0 0;
                position: relative;
                overflow: hidden;
            }

            .hero-content {
                text-align: center;
                max-width: 700px;
                margin: 0 auto;
                padding-bottom: 60px;
                position: relative;
                z-index: 3;
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: rgba(255, 255, 255, 0.2);
                padding: 6px 16px;
                border-radius: 50px;
                margin-bottom: 24px;
                font-size: 0.875rem;
                color: white;
            }

            .help-hero h1 {
                font-size: 3rem;
                font-weight: 700;
                color: white;
                margin-bottom: 16px;
            }

            .help-hero p {
                font-size: 1.125rem;
                color: rgba(255, 255, 255, 0.9);
                margin-bottom: 0;
            }

            /* SVG Wave */
            .hero-wave-container {
                position: absolute;
                bottom: -1px;
                left: 0;
                right: 0;
                width: 100%;
                z-index: 2;
                line-height: 0;
            }

            .hero-wave-svg {
                width: 100%;
                height: 80px;
                display: block;
            }

            @media (max-width: 768px) {
                .hero-wave-svg {
                    height: 50px;
                }

                .hero-content {
                    padding-bottom: 40px;
                }
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

            .section-header h2 {
                font-size: 2rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 16px;
            }

            .section-header p {
                color: #6b7280;
                font-size: 1rem;
            }

            /* Quick Guides */
            .quick-guides {
                padding: 40px 0;
                background: white;
                position: relative;
                z-index: 3;
            }

            .guides-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 30px;
            }

            .guide-card {
                background: white;
                padding: 32px;
                border-radius: 24px;
                text-align: center;
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
            }

            .guide-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                border-color: #c4b5fd;
            }

            .guide-icon {
                width: 70px;
                height: 70px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
            }

            .guide-icon i {
                font-size: 2rem;
            }

            .guide-card h3 {
                font-size: 1.1rem;
                margin-bottom: 12px;
                color: #1f2937;
            }

            .guide-card p {
                font-size: 0.85rem;
                color: #6b7280;
                margin-bottom: 16px;
                line-height: 1.5;
            }

            .guide-link {
                background: none;
                border: none;
                color: #7c3aed;
                font-weight: 500;
                font-size: 0.85rem;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: all 0.3s ease;
                cursor: pointer;
                font-family: inherit;
            }

            .guide-link:hover {
                gap: 10px;
                color: #6d28d9;
            }

            /* FAQ Section */
            .faq-section {
                padding: 80px 0;
                background: #f9fafb;
            }

            .faq-categories {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
                margin-bottom: 40px;
            }

            .category-btn {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 24px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 500;
                color: #4b5563;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .category-btn i {
                font-size: 0.9rem;
            }

            .category-btn:hover {
                background: #f5f3ff;
                border-color: #c4b5fd;
            }

            .category-btn.active {
                background: #7c3aed;
                color: white;
                border-color: #7c3aed;
            }

            .faq-category-content {
                display: none;
                max-width: 800px;
                margin: 0 auto;
            }

            .faq-category-content.active {
                display: block;
                animation: fadeIn 0.4s ease;
            }

            .faq-item {
                background: white;
                border-radius: 16px;
                margin-bottom: 16px;
                overflow: hidden;
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
            }

            .faq-item:hover {
                border-color: #c4b5fd;
            }

            .faq-question {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 24px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .faq-question h3 {
                font-size: 1rem;
                font-weight: 600;
                color: #1f2937;
                margin: 0;
            }

            .faq-question i {
                color: #9ca3af;
                transition: transform 0.3s ease;
            }

            .faq-item.active .faq-question i {
                transform: rotate(180deg);
            }

            .faq-answer {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                padding: 0 24px;
            }

            .faq-item.active .faq-answer {
                max-height: 300px;
                padding: 0 24px 20px 24px;
            }

            .faq-answer p {
                color: #6b7280;
                font-size: 0.9rem;
                line-height: 1.6;
                margin: 0;
            }

            /* Video Tutorials */
            .video-tutorials {
                padding: 80px 0;
                background: white;
            }

            .videos-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }

            .video-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
                cursor: pointer;
            }

            .video-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .video-thumbnail {
                position: relative;
                height: 180px;
                overflow: hidden;
            }

            .thumbnail-placeholder {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            .thumbnail-placeholder i {
                font-size: 3rem;
                color: white;
                opacity: 0.9;
                transition: all 0.3s ease;
            }

            .video-card:hover .thumbnail-placeholder i {
                transform: scale(1.1);
            }

            .video-duration {
                position: absolute;
                bottom: 10px;
                right: 10px;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 0.7rem;
            }

            .video-card h3 {
                padding: 16px 20px 8px;
                font-size: 1rem;
                color: #1f2937;
            }

            .video-link {
                background: none;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 20px 20px;
                color: #7c3aed;
                font-weight: 500;
                font-size: 0.85rem;
                transition: all 0.3s ease;
                cursor: pointer;
                font-family: inherit;
            }

            .video-link:hover {
                gap: 10px;
                color: #6d28d9;
            }

            /* Still Need Help */
            .still-need-help {
                padding: 80px 0;
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
            }

            .help-card {
                background: white;
                border-radius: 32px;
                padding: 50px;
                text-align: center;
                max-width: 700px;
                margin: 0 auto;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            }

            .help-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 24px;
            }

            .help-icon i {
                font-size: 2.5rem;
                color: #7c3aed;
            }

            .help-card h2 {
                font-size: 1.8rem;
                color: #1f2937;
                margin-bottom: 16px;
            }

            .help-card p {
                color: #6b7280;
                margin-bottom: 32px;
            }

            .help-buttons {
                display: flex;
                gap: 16px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .help-buttons .btn-outline {
                border-color: #e5e7eb;
                background: white;
            }

            .help-buttons .btn-outline:hover {
                border-color: #7c3aed;
                background: #f5f3ff;
                color: #7c3aed;
            }

            /* Guide Modals */
            .guide-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
            }

            .guide-modal.active {
                display: block;
            }

            .guide-modal-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(4px);
            }

            .guide-modal-container {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 600px;
                max-height: 85vh;
                background: white;
                border-radius: 24px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
                animation: modalFadeIn 0.3s ease;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            .guide-modal-header {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 20px 24px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                flex-shrink: 0;
            }

            .modal-icon {
                width: 45px;
                height: 45px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.2);
            }

            .modal-icon i {
                font-size: 1.2rem;
            }

            .guide-modal-header h3 {
                flex: 1;
                margin: 0;
                font-size: 1.1rem;
                font-weight: 600;
            }

            .modal-close-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                color: white;
                font-size: 0.9rem;
            }

            .modal-close-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .guide-modal-body {
                padding: 20px 24px;
                overflow-y: auto;
                flex: 1;
            }

            .guide-step {
                display: flex;
                gap: 15px;
                margin-bottom: 20px;
                padding-bottom: 16px;
                border-bottom: 1px solid #e5e7eb;
            }

            .guide-step:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .step-number {
                width: 32px;
                height: 32px;
                background: #7c3aed;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 0.9rem;
                flex-shrink: 0;
            }

            .step-content h4 {
                font-size: 0.95rem;
                margin-bottom: 4px;
                color: #1f2937;
            }

            .step-content p {
                font-size: 0.8rem;
                color: #6b7280;
                line-height: 1.5;
                margin: 0;
            }

            .points-summary {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 16px;
            }

            .points-earn h4,
            .points-redeem h4 {
                font-size: 0.9rem;
                margin-bottom: 12px;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .points-earn ul,
            .points-redeem ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .points-earn li,
            .points-redeem li {
                padding: 6px 0;
                border-bottom: 1px solid #f3f4f6;
                font-size: 0.8rem;
                color: #4b5563;
            }

            .points-earn li:last-child,
            .points-redeem li:last-child {
                border-bottom: none;
            }

            .privacy-section {
                display: flex;
                flex-direction: column;
                gap: 16px;
                margin-bottom: 16px;
            }

            .privacy-item {
                display: flex;
                gap: 12px;
                align-items: flex-start;
            }

            .privacy-item i {
                width: 36px;
                height: 36px;
                background: #f3f4f6;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #7c3aed;
                font-size: 0.9rem;
                flex-shrink: 0;
            }

            .privacy-item h4 {
                font-size: 0.85rem;
                margin-bottom: 2px;
                color: #1f2937;
            }

            .privacy-item p {
                font-size: 0.75rem;
                color: #6b7280;
                line-height: 1.5;
                margin: 0;
            }

            .guide-tip {
                background: #fef3c7;
                border-radius: 12px;
                padding: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 16px;
            }

            .guide-tip i {
                color: #f59e0b;
                font-size: 1rem;
            }

            .guide-tip p {
                margin: 0;
                font-size: 0.75rem;
                color: #92400e;
            }

            .guide-modal-footer {
                padding: 16px 24px;
                background: #f9fafb;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
                flex-shrink: 0;
            }

            .btn-close-modal {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-close-modal:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            /* Video Modal - FIXED SIZE, NO SCROLLBARS */
            .video-modal-container {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 700px;
                background: white;
                border-radius: 20px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
                animation: modalFadeIn 0.3s ease;
                overflow: hidden;
            }

            .video-modal-header {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 16px 20px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
            }

            .video-modal-icon {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.2);
            }

            .video-modal-icon i {
                font-size: 1.1rem;
            }

            .video-modal-header h3 {
                flex: 1;
                margin: 0;
                font-size: 1rem;
                font-weight: 600;
            }

            .video-modal-close-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                color: white;
                font-size: 0.9rem;
            }

            .video-modal-close-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .video-modal-body {
                padding: 20px;
            }

            .video-player-wrapper {
                margin-bottom: 12px;
                border-radius: 12px;
                overflow: hidden;
                background: #1f2937;
            }

            .video-player {
                width: 100%;
                display: block;
                border-radius: 12px;
                outline: none;
            }

            .video-controls-note {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                background: #f3f4f6;
                border-radius: 10px;
            }

            .video-controls-note i {
                color: #7c3aed;
                font-size: 0.9rem;
            }

            .video-controls-note p {
                margin: 0;
                font-size: 0.7rem;
                color: #6b7280;
            }

            .video-modal-footer {
                padding: 16px 20px;
                background: #f9fafb;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
            }

            .btn-close-video-modal {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-close-video-modal:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            /* Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            @keyframes modalFadeIn {
                from {
                    opacity: 0;
                    transform: translate(-50%, -48%);
                }

                to {
                    opacity: 1;
                    transform: translate(-50%, -50%);
                }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease forwards;
            }

            .animate-scale-in {
                animation: scaleIn 0.5s ease forwards;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .guides-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .videos-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .points-summary {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .help-hero {
                    padding: 60px 0 0;
                }

                .help-hero h1 {
                    font-size: 2rem;
                }

                .guides-grid {
                    grid-template-columns: 1fr;
                }

                .videos-grid {
                    grid-template-columns: 1fr;
                }

                .faq-categories {
                    gap: 10px;
                }

                .category-btn {
                    padding: 8px 16px;
                    font-size: 0.75rem;
                }

                .help-card {
                    padding: 30px 20px;
                }

                .help-buttons {
                    flex-direction: column;
                }

                .help-buttons .btn {
                    width: 100%;
                }

                .guide-modal-container {
                    width: 95%;
                    max-height: 90vh;
                }

                .guide-modal-header {
                    padding: 16px 20px;
                }

                .guide-modal-body {
                    padding: 16px 20px;
                }

                .guide-modal-footer {
                    padding: 12px 20px;
                }

                .modal-icon {
                    width: 38px;
                    height: 38px;
                }

                .video-modal-container {
                    width: 95%;
                    max-width: none;
                }

                .video-modal-header {
                    padding: 12px 16px;
                }

                .video-modal-header h3 {
                    font-size: 0.9rem;
                }

                .video-modal-body {
                    padding: 16px;
                }
            }

            /* RTL Support */
            body.rtl .guide-link:hover {
                gap: 6px;
            }

            body.rtl .video-link:hover {
                gap: 6px;
            }

            body.rtl .faq-question {
                flex-direction: row;
            }

            body.rtl .step-number {
                margin-left: 0;
                margin-right: 0;
            }

            body.rtl .guide-modal-header {
                flex-direction: row;
            }

            body.rtl .video-modal-header {
                flex-direction: row;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Category switching
                const categoryBtns = document.querySelectorAll('.category-btn');
                const categoryContents = document.querySelectorAll('.faq-category-content');

                categoryBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const category = this.dataset.category;

                        categoryBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        categoryContents.forEach(content => {
                            if (content.dataset.category === category) {
                                content.classList.add('active');
                            } else {
                                content.classList.remove('active');
                            }
                        });
                    });
                });

                // FAQ accordion
                const faqItems = document.querySelectorAll('.faq-item');

                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question');

                    question.addEventListener('click', () => {
                        const isActive = item.classList.contains('active');

                        faqItems.forEach(other => {
                            if (other !== item && other.classList.contains('active')) {
                                other.classList.remove('active');
                            }
                        });

                        if (!isActive) {
                            item.classList.add('active');
                        } else {
                            item.classList.remove('active');
                        }
                    });
                });

                // Video sources - update these paths when you add videos to public/videos/
                const videoSources = {
                    0: '{{ asset("videos/getting-started.mp4") }}',
                    1: '{{ asset("videos/how-to-book.mp4") }}',
                    2: '{{ asset("videos/navigating-dashboard.mp4") }}'
                };

                const videoTitles = {
                    0: '{{ __("Getting Started Tutorial") }}',
                    1: '{{ __("How to Book a Session") }}',
                    2: '{{ __("Navigating Your Dashboard") }}'
                };

                // Guide Modals
                const modalBtns = document.querySelectorAll('.guide-modal-btn');
                const watchVideoBtns = document.querySelectorAll('.watch-video-btn');
                const closeBtns = document.querySelectorAll('.modal-close-btn, .btn-close-modal');
                const videoCloseBtns = document.querySelectorAll('.video-modal-close-btn, .btn-close-video-modal');

                function openModal(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    }
                }

                function closeModal(modal) {
                    if (modal) {
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                        // Stop video when closing video modal
                        if (modal.id === 'video-player-modal' && videoPlayer) {
                            videoPlayer.pause();
                        }
                    }
                }

                modalBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const guide = this.dataset.guide;
                        const modalId = `modal-${guide}`;
                        openModal(modalId);
                    });
                });

                // Video Modal
                const videoModal = document.getElementById('video-player-modal');
                const videoPlayer = document.getElementById('videoPlayer');
                const videoSource = document.getElementById('videoSource');
                const videoModalTitle = document.getElementById('videoModalTitle');

                watchVideoBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const videoIndex = this.dataset.videoIndex;
                        const videoUrl = videoSources[videoIndex];
                        const videoTitle = videoTitles[videoIndex];

                        if (videoSource && videoPlayer) {
                            videoSource.src = videoUrl;
                            videoPlayer.load();
                            videoModalTitle.textContent = videoTitle || '{{ __("Watch Tutorial") }}';
                            openModal('video-player-modal');
                        }
                    });
                });

                // Close buttons
                closeBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const modal = this.closest('.guide-modal');
                        closeModal(modal);
                    });
                });

                videoCloseBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        closeModal(videoModal);
                        if (videoPlayer) {
                            videoPlayer.pause();
                        }
                    });
                });

                // Close modals when clicking overlay
                const overlays = document.querySelectorAll('.guide-modal-overlay');
                overlays.forEach(overlay => {
                    overlay.addEventListener('click', function () {
                        const modal = this.closest('.guide-modal');
                        closeModal(modal);
                        if (modal.id === 'video-player-modal' && videoPlayer) {
                            videoPlayer.pause();
                        }
                    });
                });

                // Close on Escape key
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        const activeModals = document.querySelectorAll('.guide-modal.active');
                        activeModals.forEach(modal => {
                            closeModal(modal);
                            if (modal.id === 'video-player-modal' && videoPlayer) {
                                videoPlayer.pause();
                            }
                        });
                    }
                });
            });
        </script>
    @endpush

@endsection