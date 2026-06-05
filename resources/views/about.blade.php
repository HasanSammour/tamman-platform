{{-- resources/views/about.blade.php --}}
@extends('layouts.guest')

@section('title', __('About Us - Tamman Mental Health Platform'))

@section('description', __('Tamman is a secure digital mental health platform dedicated to providing accessible, private, and stigma-free psychological support to the Gaza community and beyond.'))

@section('content')

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="about-hero-content reveal">
            <div class="about-hero-badge">
                <i class="fas fa-heart"></i>
                <span>{{ __('Our Story') }}</span>
            </div>
            <h1 class="about-hero-title">
                {{ __('About') }} <span class="gradient-text">{{ __('Tamman') }}</span>
            </h1>
            <p class="about-hero-description">
                {{ __('Tamman is a secure digital mental health platform dedicated to providing accessible, private, and stigma-free psychological support to the Gaza community and beyond.') }}
            </p>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="mission-vision">
    <div class="container">
        <div class="mission-vision-grid">
            <div class="mission-card reveal">
                <div class="mission-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>{{ __('Our Mission') }}</h3>
                <p>{{ __('To provide accessible, private, and stigma-free mental health support to everyone who needs it, using innovative technology and compassionate care.') }}</p>
            </div>
            <div class="vision-card reveal">
                <div class="vision-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>{{ __('Our Vision') }}</h3>
                <p>{{ __('A world where mental health care is accessible to all, free from stigma, and integrated into everyday life.') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-showcase">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card reveal">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number counter" data-target="{{ $stats['total_users'] }}" data-duration="2000">0</div>
                <div class="stat-label">{{ __('Happy Users') }}</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-number counter" data-target="{{ $stats['total_specialists'] }}" data-duration="2000">0</div>
                <div class="stat-label">{{ __('Certified Specialists') }}</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-number counter" data-target="{{ $stats['total_sessions'] }}" data-duration="2000">0</div>
                <div class="stat-label">{{ __('Sessions Completed') }}</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <span class="percent-sign">%</span> <div class="stat-number counter" data-target="{{ $stats['satisfaction_rate'] }}" data-duration="2000">0</div>
                <div class="stat-label">{{ __('Satisfaction Rate') }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="core-values">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">{{ __('Our Principles') }}</span>
            <h2 class="section-title">{{ __('Core Values') }}</h2>
            <p class="section-description">{{ __('The principles that guide everything we do at Tamman.') }}</p>
        </div>
        
        <div class="values-grid">
            @foreach($coreValues as $value)
            <div class="value-card reveal">
                <div class="value-icon">
                    <i class="{{ $value['icon'] }}"></i>
                </div>
                <h3>{{ __($value['title']) }}</h3>
                <p>{{ __($value['description']) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="our-story">
    <div class="container">
        <div class="story-grid">
            <div class="story-content reveal">
                <div class="story-badge">
                    <i class="fas fa-history"></i>
                    <span>{{ __('Our Journey') }}</span>
                </div>
                <h2>{{ __('The Story Behind Tamman') }}</h2>
                <p>{{ __('Tamman was born from a simple yet powerful idea: everyone deserves access to quality mental health care, regardless of their circumstances.') }}</p>
                <p>{{ __('In Gaza, where ongoing conflicts, economic instability, and social stigma create immense psychological pressure, traditional therapy is often out of reach. Many suffer in silence, afraid to seek help due to fear of judgment or lack of privacy.') }}</p>
                <p>{{ __('Tamman bridges this gap by providing a secure, digital platform where individuals can connect with licensed mental health professionals from the comfort and privacy of their own homes.') }}</p>
                <p>{{ __('Today, Tamman serves thousands of users, offering hope, healing, and support to those who need it most.') }}</p>
                <div class="story-stats-enhanced">
                    <div class="story-stat-enhanced">
                        <div class="stat-icon-small">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number-enhanced">{{ $stats['years_experience'] }}+</span>
                            <span class="stat-label-enhanced">{{ __('Years of Impact') }}</span>
                        </div>
                    </div>
                    <div class="story-stat-enhanced">
                        <div class="stat-icon-small">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number-enhanced">{{ $stats['countries_served'] }}</span>
                            <span class="stat-label-enhanced">{{ __('Countries Served') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="story-image reveal">
                <div class="story-illustration">
                    <img src="{{ asset('images/logo.png') }}" alt="Tamman" class="story-logo">
                    <div class="floating-elements">
                        <div class="float-element elem-1"><i class="fas fa-heart"></i></div>
                        <div class="float-element elem-2"><i class="fas fa-smile"></i></div>
                        <div class="float-element elem-3"><i class="fas fa-hand-holding-heart"></i></div>
                        <div class="float-element elem-4"><i class="fas fa-brain"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Milestones Section -->
<section class="milestones">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">{{ __('Achievements') }}</span>
            <h2 class="section-title">{{ __('Our Milestones') }}</h2>
            <p class="section-description">{{ __('Key moments in our journey to make mental health care accessible to all.') }}</p>
        </div>
        
        <div class="timeline">
            @foreach($milestones as $index => $milestone)
            <div class="timeline-item reveal {{ $index % 2 == 0 ? 'timeline-left' : 'timeline-right' }}">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">{{ $milestone['year'] }}</div>
                    <div class="timeline-icon">
                        <i class="{{ $milestone['icon'] }}"></i>
                    </div>
                    <h3>{{ __($milestone['title']) }}</h3>
                    <p>{{ __($milestone['description']) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="our-team">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">{{ __('Meet the Team') }}</span>
            <h2 class="section-title">{{ __('Our Dedicated Professionals') }}</h2>
            <p class="section-description">{{ __('Passionate experts committed to your mental well-being.') }}</p>
        </div>
        
        <div class="team-grid">
            @foreach($teamMembers as $member)
            <div class="team-card reveal">
                <div class="team-image">
                    @if($member->profile_image)
                        <img src="{{ asset('storage/' . $member->profile_image) }}" alt="{{ $member->name }}">
                    @else
                        <div class="team-placeholder">
                            <i class="fas fa-user-circle"></i>
                        </div>
                    @endif
                </div>
                <div class="team-info">
                    <h3>{{ $member->name }}</h3>
                    <p class="team-role">
                        @if($member->hasRole('admin'))
                            {{ __('Platform Administrator') }}
                        @elseif($member->hasRole('specialist'))
                            {{ $member->specialistProfile->specialization ?? __('Mental Health Specialist') }}
                        @endif
                    </p>
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="about-testimonials">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">{{ __('Testimonials') }}</span>
            <h2 class="section-title">{{ __('What Our Community Says') }}</h2>
            <p class="section-description">{{ __('Real stories from real people who found hope through Tamman.') }}</p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-item reveal">
                <div class="testimonial-quote">
                    <i class="fas fa-quote-left"></i>
                </div>
                <p>{{ __('Tamman gave me the courage to seek help. The platform is easy to use, and my therapist is wonderful. I feel heard and supported.') }}</p>
                <div class="testimonial-author">
                    <div class="author-avatar">S</div>
                    <div class="author-info">
                        <h4>{{ __('Sarah M.') }}</h4>
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
            <div class="testimonial-item reveal">
                <div class="testimonial-quote">
                    <i class="fas fa-quote-left"></i>
                </div>
                <p>{{ __('I was hesitant at first, but the privacy and convenience of Tamman made it possible for me to finally get the help I needed.') }}</p>
                <div class="testimonial-author">
                    <div class="author-avatar">A</div>
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
            <div class="testimonial-item reveal">
                <div class="testimonial-quote">
                    <i class="fas fa-quote-left"></i>
                </div>
                <p>{{ __('The donor support system changed my life. I couldn\'t afford therapy, but now I\'m getting the help I deserve. Thank you Tamman!') }}</p>
                <div class="testimonial-author">
                    <div class="author-avatar">R</div>
                    <div class="author-info">
                        <h4>{{ __('Rania H.') }}</h4>
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

<!-- CTA Section - Show only for guests -->
@if(!$isAuthenticated)
<section class="cta-section">
    <div class="container">
        <div class="cta-content reveal">
            <h2>{{ __('Join Our Community') }}</h2>
            <p>{{ __('Be part of a movement that\'s making mental health care accessible to everyone.') }}</p>
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Sign Up Free') }}</a>
                <a href="{{ route('specialist.apply') }}" class="btn btn-outline btn-lg">{{ __('Become a Specialist') }}</a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Small spacer -->
<div style="height: 30px;"></div>

@endsection

@push('styles')
<style>
    /* ========== ABOUT PAGE STYLES ========== */
    
    /* Hero Section */
    .about-hero {
        padding: 80px 0 60px;
        background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
        text-align: center;
    }
    
    .about-hero-content {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .about-hero-badge {
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
    
    .about-hero-title {
        font-size: clamp(2rem, 5vw, 3.5rem);
        margin-bottom: 20px;
        line-height: 1.2;
    }
    
    .about-hero-description {
        font-size: 1.125rem;
        color: #4b5563;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Mission & Vision */
    .mission-vision {
        padding: 80px 0;
        background: white;
    }
    
    .mission-vision-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .mission-card, .vision-card {
        text-align: center;
        padding: 40px 30px;
        border-radius: 24px;
        transition: all 0.3s ease;
    }
    
    .mission-card {
        background: linear-gradient(135deg, #f5f3ff, white);
        border: 1px solid #ddd6fe;
    }
    
    .vision-card {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
    }
    
    .mission-icon, .vision-icon {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .mission-icon {
        background: #ede9fe;
        color: #7c3aed;
    }
    
    .mission-icon i, .vision-icon i {
        font-size: 2rem;
    }
    
    .vision-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    .mission-card h3, .vision-card h3 {
        margin-bottom: 16px;
    }
    
    .vision-card h3, .vision-card p {
        color: white;
    }
    
    .mission-card p, .vision-card p {
        line-height: 1.6;
    }
    
    /* Statistics Section */
    .stats-showcase {
        padding: 80px 0;
        background: #f9fafb;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }
    
    .stat-card {
        text-align: center;
        padding: 30px;
        background: white;
        border-radius: 20px;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .stat-icon i {
        font-size: 1.75rem;
        color: #7c3aed;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #7c3aed;
        display: inline-block;
    }
    
    .percent-sign {
        font-size: 2rem;
        font-weight: 700;
        color: #7c3aed;
        margin-left: 2px;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 8px;
    }
    
    /* Core Values */
    .core-values {
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
    
    .values-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }
    
    .value-card {
        text-align: center;
        padding: 32px 24px;
        background: #f9fafb;
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    
    .value-card:hover {
        transform: translateY(-5px);
        background: white;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .value-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .value-icon i {
        font-size: 1.75rem;
        color: white;
    }
    
    .value-card h3 {
        font-size: 1.125rem;
        margin-bottom: 12px;
    }
    
    .value-card p {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.6;
    }
    
    /* Story Section */
    .our-story {
        padding: 80px 0;
        background: #f9fafb;
    }
    
    .story-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }
    
    .story-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(139, 92, 246, 0.1);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #7c3aed;
        margin-bottom: 20px;
    }
    
    .story-content h2 {
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        margin-bottom: 20px;
    }
    
    .story-content p {
        color: #6b7280;
        margin-bottom: 16px;
        line-height: 1.6;
    }
    
    /* Enhanced Story Stats */
    .story-stats-enhanced {
        display: flex;
        gap: 40px;
        margin-top: 30px;
        padding: 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .story-stat-enhanced {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }
    
    .stat-icon-small {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stat-icon-small i {
        font-size: 1.5rem;
        color: #7c3aed;
    }
    
    .stat-info {
        display: flex;
        flex-direction: column;
    }
    
    .stat-number-enhanced {
        font-size: 1.75rem;
        font-weight: 800;
        color: #7c3aed;
        line-height: 1.2;
    }
    
    .stat-label-enhanced {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    /* Story Illustration */
    .story-illustration {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        border-radius: 40px;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .story-logo {
        width: 150px;
        height: auto;
        z-index: 2;
        opacity: 0.8;
    }
    
    .floating-elements {
        position: absolute;
        width: 100%;
        height: 100%;
    }
    
    .float-element {
        position: absolute;
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        animation: float 3s ease-in-out infinite;
    }
    
    .float-element i {
        font-size: 1.25rem;
        color: #7c3aed;
        margin: 0;
    }
    
    .elem-1 {
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }
    
    .elem-2 {
        bottom: 20%;
        right: 10%;
        animation-delay: 1s;
    }
    
    .elem-3 {
        top: 50%;
        right: 20%;
        animation-delay: 2s;
    }
    
    .elem-4 {
        bottom: 30%;
        left: 20%;
        animation-delay: 1.5s;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    
    /* Milestones Timeline */
    .milestones {
        padding: 80px 0;
        background: white;
    }
    
    .timeline {
        max-width: 1000px;
        margin: 0 auto;
        position: relative;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 100%;
        background: linear-gradient(to bottom, #ddd6fe, #c4b5fd);
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 50px;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-dot {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 16px;
        height: 16px;
        background: #7c3aed;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 3px #ddd6fe;
        z-index: 2;
    }
    
    .timeline-content {
        width: calc(50% - 40px);
        padding: 24px;
        background: #f9fafb;
        border-radius: 20px;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .timeline-content:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .timeline-left .timeline-content {
        margin-left: 0;
        margin-right: auto;
    }
    
    .timeline-right .timeline-content {
        margin-left: auto;
        margin-right: 0;
    }
    
    .timeline-year {
        display: inline-block;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 12px;
    }
    
    .timeline-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    
    .timeline-icon i {
        font-size: 1.5rem;
        color: #7c3aed;
    }
    
    .timeline-content h3 {
        font-size: 1.125rem;
        margin-bottom: 8px;
    }
    
    .timeline-content p {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.6;
    }
    
    /* Team Section */
    .our-team {
        padding: 80px 0;
        background: #f9fafb;
    }
    
    .team-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
    
    .team-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .team-image {
        height: 250px;
        overflow: hidden;
    }
    
    .team-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .team-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .team-placeholder i {
        font-size: 5rem;
        color: #7c3aed;
    }
    
    .team-info {
        padding: 20px;
    }
    
    .team-info h3 {
        font-size: 1.125rem;
        margin-bottom: 4px;
    }
    
    .team-role {
        font-size: 0.75rem;
        color: #7c3aed;
        margin-bottom: 12px;
    }
    
    .team-social {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .team-social a {
        width: 32px;
        height: 32px;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        transition: all 0.3s ease;
    }
    
    .team-social a:hover {
        background: #7c3aed;
        color: white;
    }
    
    /* Testimonials */
    .about-testimonials {
        padding: 80px 0;
        background: white;
    }
    
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
    
    .testimonial-item {
        background: #f9fafb;
        padding: 32px;
        border-radius: 20px;
        position: relative;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .testimonial-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .testimonial-quote {
        position: absolute;
        bottom: -10px;
        right: -10px;
        font-size: 5rem;
        color: #ede9fe;
        z-index: 0;
        opacity: 0.6;
    }
    
    .testimonial-item p {
        margin-bottom: 20px;
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
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .author-info h4 {
        font-size: 1rem;
        margin-bottom: 4px;
    }
    
    .author-rating i {
        color: #fbbf24;
        font-size: 0.75rem;
    }
    
    /* CTA Section */
    .cta-section {
        padding: 70px 0 50px;
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
    
    /* RTL Support */
    body.rtl .story-stat-enhanced {
        flex-direction: row-reverse;
    }
    
    body.rtl .stat-icon-small {
        margin-left: 15px;
        margin-right: 0;
    }
    
    body.rtl .testimonial-quote {
        right: auto;
        left: -10px;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .stats-grid,
        .values-grid,
        .team-grid,
        .testimonials-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .mission-vision-grid {
            grid-template-columns: 1fr;
        }
        
        .story-grid {
            grid-template-columns: 1fr;
            text-align: center;
        }
        
        .story-stats-enhanced {
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }
        
        .story-stat-enhanced {
            justify-content: center;
        }
        
        .timeline::before {
            left: 20px;
        }
        
        .timeline-dot {
            left: 20px;
        }
        
        .timeline-left .timeline-content,
        .timeline-right .timeline-content {
            width: calc(100% - 60px);
            margin-left: 60px !important;
            margin-right: 0 !important;
        }
    }
    
    @media (max-width: 768px) {
        .about-hero {
            padding: 40px 0;
        }
        
        .stats-grid,
        .values-grid,
        .team-grid,
        .testimonials-grid {
            grid-template-columns: 1fr;
        }
        
        .mission-card, .vision-card {
            padding: 30px 20px;
        }
        
        .story-illustration {
            height: 300px;
        }
        
        .story-logo {
            width: 100px;
        }
        
        .float-element {
            width: 40px;
            height: 40px;
        }
        
        .float-element i {
            font-size: 1rem;
        }
        
        .cta-section {
            padding: 50px 0 40px;
        }
        
        .stat-number {
            font-size: 2rem;
        }
        
        .percent-sign {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .story-stats-enhanced {
            gap: 20px;
        }
        
        .stat-number-enhanced {
            font-size: 1.25rem;
        }
        
        .timeline-content {
            padding: 16px;
        }
    }
</style>
@endpush