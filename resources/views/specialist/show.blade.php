{{-- resources/views/specialist/show.blade.php --}}
@extends('layouts.guest')

@section('title', $specialist->name . ' - ' . __('Tamman'))

@section('content')

<!-- Hero Section -->
<section class="profile-hero">
    <div class="container">
        <div class="profile-hero-content">
            <div class="profile-avatar">
                @php
                    $profileImage = $specialist->getProfileImageUrl();
                    $firstLetter = mb_substr($specialist->name, 0, 1, 'UTF-8');
                @endphp
                
                @if($profileImage)
                    <img src="{{ $profileImage }}" alt="{{ $specialist->name }}">
                @else
                    <div class="avatar-placeholder">
                        <div class="avatar-initials">{{ $firstLetter }}</div>
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <h1>{{ $specialist->name }}</h1>
                <div class="profile-specialty">
                    <i class="fas fa-stethoscope"></i>
                    <span>{{ $profile->specialization }}</span>
                </div>
                <div class="profile-rating">
                    <div class="stars">
                        @php
                            $rating = $profile->rating_avg ?? 0;
                            $fullStars = floor($rating);
                            $halfStar = $rating - $fullStars >= 0.5;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $fullStars)
                                <i class="fas fa-star"></i>
                            @elseif($i == $fullStars + 1 && $halfStar)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="rating-value">{{ number_format($rating, 1) }}</span>
                    <span class="reviews-count">({{ $totalReviews }} {{ __('reviews') }})</span>
                </div>
                <div class="profile-badge">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ __('Verified Specialist') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="profile-section">
    <div class="container">
        <div class="profile-layout">
            
            <!-- Left Column - About & Reviews -->
            <div class="profile-main">
                <!-- About Section -->
                <div class="profile-card">
                    <h3><i class="fas fa-user-md"></i> {{ __('About Me') }}</h3>
                    <div class="about-content">
                        <p>{{ $profile->bio ?? __('No bio available') }}</p>
                    </div>
                </div>
                
                <!-- Qualifications Section -->
                <div class="profile-card">
                    <h3><i class="fas fa-graduation-cap"></i> {{ __('Qualifications & Education') }}</h3>
                    <div class="qualifications-content">
                        <p>{{ $profile->qualifications ?? __('No qualifications listed') }}</p>
                    </div>
                </div>
                
                <!-- Reviews Section -->
                <div class="profile-card">
                    <h3><i class="fas fa-star"></i> {{ __('Patient Reviews') }} ({{ $totalReviews }})</h3>
                    
                    @if($totalReviews > 0)
                        <div class="rating-summary">
                            <div class="rating-average">
                                <div class="average-number">{{ number_format($rating, 1) }}</div>
                                <div class="average-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($rating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i == floor($rating) + 1 && ($rating - floor($rating)) >= 0.5)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="average-text">{{ __('Based on') }} {{ $totalReviews }} {{ __('reviews') }}</div>
                            </div>
                            <div class="rating-bars">
                                @for($i = 5; $i >= 1; $i--)
                                    <div class="rating-bar-item">
                                        <span class="rating-star">{{ $i }} <i class="fas fa-star"></i></span>
                                        <div class="rating-bar">
                                            <div class="rating-bar-fill" style="width: {{ $totalReviews > 0 ? ($ratingDistribution[$i] / $totalReviews) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="rating-count">{{ $ratingDistribution[$i] }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        
                        <div class="reviews-list" id="reviewsList">
                            @foreach($reviews->take(3) as $review)
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-avatar">
                                            @php
                                                $reviewerImage = null;
                                                $reviewerInitial = 'U';
                                                if ($review->reviewer) {
                                                    $reviewerImage = $review->reviewer->getProfileImageUrl();
                                                    $reviewerInitial = mb_substr($review->reviewer->name, 0, 1, 'UTF-8');
                                                }
                                            @endphp
                                            
                                            @if($reviewerImage)
                                                <img src="{{ $reviewerImage }}" alt="{{ $review->reviewer->name ?? __('Anonymous') }}">
                                            @else
                                                <div class="reviewer-placeholder">{{ $reviewerInitial }}</div>
                                            @endif
                                        </div>
                                        <div class="reviewer-info">
                                            <h4>{{ $review->reviewer ? $review->reviewer->name : __('Anonymous') }}</h4>
                                            <div class="review-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="review-comment">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        @if($totalReviews > 3)
                            <div class="view-all-reviews">
                                <button class="btn-view-all" onclick="openReviewsModal()">{{ __('View All Reviews') }}</button>
                            </div>
                        @endif
                    @else
                        <div class="no-reviews">
                            <i class="fas fa-comment-dots"></i>
                            <p>{{ __('No reviews yet. Be the first to leave a review after your session.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Right Column - Info & Booking -->
            <div class="profile-sidebar">
                <!-- Info Card -->
                <div class="info-card">
                    <h3><i class="fas fa-info-circle"></i> {{ __('Professional Info') }}</h3>
                    <div class="info-list">
                        <div class="info-row">
                            <i class="fas fa-stethoscope"></i>
                            <div>
                                <strong>{{ __('Specialization') }}</strong>
                                <span>{{ $profile->specialization }}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-id-card"></i>
                            <div>
                                <strong>{{ __('License Number') }}</strong>
                                <span>{{ $profile->license_number }}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <strong>{{ __('Experience') }}</strong>
                                <span>{{ $profile->experience_years ?? 0 }}+ {{ __('years') }}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-language"></i>
                            <div>
                                <strong>{{ __('Languages') }}</strong>
                                <span>{{ $profile->languages ?? __('Arabic') }}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-dollar-sign"></i>
                            <div>
                                <strong>{{ __('Fee per Session') }}</strong>
                                <span>${{ number_format($profile->consultation_fee, 2) }} USD</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>{{ __('Session Duration') }}</strong>
                                <span>{{ __('60 minutes') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Card -->
                <div class="booking-card" id="booking">
                    <h3><i class="fas fa-calendar-check"></i> {{ __('Book a Session') }}</h3>
                    
                    @auth
                        @if(Auth::user()->hasRole('patient'))
                            <div class="session-types">
                                <div class="session-type">
                                    <input type="radio" name="session_type" id="video" value="video" checked>
                                    <label for="video">
                                        <i class="fas fa-video"></i>
                                        <span>{{ __('Video Session') }}</span>
                                        <small>${{ number_format($profile->consultation_fee, 2) }}</small>
                                    </label>
                                </div>
                                <div class="session-type">
                                    <input type="radio" name="session_type" id="audio" value="audio">
                                    <label for="audio">
                                        <i class="fas fa-phone-alt"></i>
                                        <span>{{ __('Audio Session') }}</span>
                                        <small>${{ number_format($profile->consultation_fee * 0.8, 2) }}</small>
                                    </label>
                                </div>
                                <div class="session-type">
                                    <input type="radio" name="session_type" id="text" value="text">
                                    <label for="text">
                                        <i class="fas fa-comment-dots"></i>
                                        <span>{{ __('Text Chat') }}</span>
                                        <small>${{ number_format($profile->consultation_fee * 0.6, 2) }}</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="booking-note">
                                <i class="fas fa-info-circle"></i>
                                <p>{{ __('Sessions are 60 minutes. You will receive a meeting link after booking.') }}</p>
                            </div>
                            
                            <button class="btn-book-now" id="bookNowBtn">
                                <i class="fas fa-calendar-plus"></i> {{ __('Book Now') }}
                            </button>
                        @elseif(Auth::user()->hasRole('specialist'))
                            <div class="booking-message info">
                                <i class="fas fa-user-md"></i>
                                <p>{{ __('You are logged in as a specialist. Please switch to patient account to book sessions.') }}</p>
                            </div>
                            <div class="booking-buttons">
                                <a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('Switch Account') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                            </div>
                        @elseif(Auth::user()->hasRole('admin'))
                            <div class="booking-message info">
                                <i class="fas fa-shield-alt"></i>
                                <p>{{ __('You are logged in as admin. Please use a patient account to book sessions.') }}</p>
                            </div>
                            <div class="booking-buttons">
                                <a href="{{ route('logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('Switch Account') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                            </div>
                        @endif
                    @else
                        <div class="booking-message warning">
                            <i class="fas fa-lock"></i>
                            <p>{{ __('Please login or register to book a session with this specialist.') }}</p>
                        </div>
                        <div class="booking-buttons">
                            <a href="{{ route('login') }}" class="btn-login">
                                <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
                            </a>
                            <a href="{{ route('register') }}" class="btn-register">
                                <i class="fas fa-user-plus"></i> {{ __('Register') }}
                            </a>
                        </div>
                    @endauth
                </div>
                
                <!-- Contact Card -->
                <div class="contact-card">
                    <h3><i class="fas fa-envelope"></i> {{ __('Contact') }}</h3>
                    <div class="contact-buttons">
                        @auth
                            @if(Auth::user()->hasRole('patient'))
                                <button class="btn-contact" id="contactBtn">
                                    <i class="fas fa-envelope"></i> {{ __('Send Message') }}
                                </button>
                            @else
                                <button class="btn-contact" id="contactBtn" disabled style="opacity:0.6; cursor:not-allowed;">
                                    <i class="fas fa-envelope"></i> {{ __('Send Message') }}
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-contact">
                                <i class="fas fa-envelope"></i> {{ __('Login to Contact') }}
                            </a>
                        @endauth
                    </div>
                    <div class="contact-note">
                        <small>{{ __('Messages are private and secure. The specialist will respond within 24 hours.') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Modal -->
<div id="reviewsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 700px; max-height: 80vh; border-radius: 20px; overflow: hidden; position: relative;">
        <div style="padding: 20px; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;"><i class="fas fa-star"></i> {{ __('All Reviews') }} ({{ $totalReviews }})</h3>
            <button onclick="closeReviewsModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            @foreach($reviews as $review)
                <div class="review-item" style="padding: 15px 0; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; gap: 15px; margin-bottom: 10px;">
                        <div style="width: 50px; height: 50px; flex-shrink: 0;">
                            @php
                                $modalReviewerImage = null;
                                $modalReviewerInitial = 'U';
                                if ($review->reviewer) {
                                    $modalReviewerImage = $review->reviewer->getProfileImageUrl();
                                    $modalReviewerInitial = mb_substr($review->reviewer->name, 0, 1, 'UTF-8');
                                }
                            @endphp
                            
                            @if($modalReviewerImage)
                                <img src="{{ $modalReviewerImage }}" alt="{{ $review->reviewer->name ?? __('Anonymous') }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #7c3aed, #6d28d9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.2rem;">
                                    {{ $modalReviewerInitial }}
                                </div>
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 5px 0;">{{ $review->reviewer ? $review->reviewer->name : __('Anonymous') }}</h4>
                            <div style="color: #fbbf24; font-size: 0.75rem; margin-bottom: 3px;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span style="font-size: 0.7rem; color: #9ca3af;">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if($review->comment)
                        <p style="margin: 0; color: #4b5563; font-size: 0.875rem; line-height: 1.5; margin-left: 65px;">{{ $review->comment }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
/* Hero Section */
.profile-hero {
    background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);
    padding: 60px 0 40px;
}

.profile-hero-content {
    display: flex;
    gap: 40px;
    align-items: center;
    flex-wrap: wrap;
}

.profile-avatar {
    width: 150px;
    height: 150px;
    flex-shrink: 0;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initials {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 600;
    color: white;
}

.profile-info {
    flex: 1;
}

.profile-info h1 {
    font-size: 2rem;
    margin-bottom: 10px;
}

.profile-specialty {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #7c3aed;
    margin-bottom: 10px;
}

.profile-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #d1fae5;
    color: #065f46;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
}

/* Main Layout */
.profile-section {
    padding: 40px 0 80px;
    background: #f9fafb;
}

.profile-layout {
    display: flex;
    gap: 40px;
}

.profile-main {
    flex: 2;
}

.profile-sidebar {
    flex: 1;
}

/* Cards */
.profile-card, .info-card, .booking-card, .contact-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.profile-card h3, .info-card h3, .booking-card h3, .contact-card h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 1.2rem;
}

.about-content p, .qualifications-content p {
    color: #4b5563;
    line-height: 1.6;
}

/* Rating Summary */
.rating-summary {
    display: flex;
    gap: 30px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.rating-average {
    text-align: center;
    min-width: 120px;
}

.average-number {
    font-size: 3rem;
    font-weight: 700;
    color: #f59e0b;
}

.average-stars {
    color: #fbbf24;
    margin: 5px 0;
}

.average-text {
    font-size: 0.75rem;
    color: #6b7280;
}

.rating-bars {
    flex: 1;
}

.rating-bar-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.rating-star {
    width: 45px;
    font-size: 0.8rem;
}

.rating-bar {
    flex: 1;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.rating-bar-fill {
    height: 100%;
    background: #fbbf24;
    border-radius: 4px;
}

.rating-count {
    width: 30px;
    font-size: 0.75rem;
    color: #6b7280;
}

/* Reviews List */
.reviews-list {
    margin-top: 20px;
}

.review-item {
    padding: 20px 0;
    border-bottom: 1px solid #e5e7eb;
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
}

.reviewer-avatar {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.reviewer-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.reviewer-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
}

.reviewer-info h4 {
    font-size: 1rem;
    margin-bottom: 5px;
}

.review-stars {
    color: #fbbf24;
    font-size: 0.75rem;
    margin-bottom: 3px;
}

.review-date {
    font-size: 0.7rem;
    color: #9ca3af;
}

.review-comment {
    color: #4b5563;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-left: 65px;
}

.no-reviews {
    text-align: center;
    padding: 40px;
    color: #9ca3af;
}

.no-reviews i {
    font-size: 3rem;
    margin-bottom: 10px;
}

.view-all-reviews {
    text-align: center;
    margin-top: 20px;
}

.btn-view-all {
    background: none;
    border: none;
    color: #7c3aed;
    cursor: pointer;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-view-all:hover {
    background: #f3f4f6;
}

/* Info List */
.info-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-row {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.info-row i {
    width: 20px;
    color: #7c3aed;
    margin-top: 2px;
}

.info-row div {
    flex: 1;
}

.info-row strong {
    display: block;
    font-size: 0.7rem;
    color: #9ca3af;
    font-weight: 500;
    margin-bottom: 2px;
}

.info-row span {
    font-size: 0.875rem;
    color: #374151;
}

/* Booking Card */
.session-types {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.session-type {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.session-type:has(input:checked) {
    border-color: #7c3aed;
    background: #f5f3ff;
}

.session-type input {
    display: none;
}

.session-type label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    cursor: pointer;
    width: 100%;
}

.session-type label i {
    width: 30px;
    color: #7c3aed;
}

.session-type label span {
    flex: 1;
    font-weight: 500;
}

.session-type label small {
    color: #10b981;
    font-weight: 600;
}

.booking-note {
    background: #fef3c7;
    padding: 12px;
    border-radius: 12px;
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.booking-note i {
    color: #d97706;
}

.booking-note p {
    font-size: 0.75rem;
    color: #92400e;
    margin: 0;
}

.btn-book-now {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-book-now:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
}

.booking-message {
    padding: 15px;
    border-radius: 12px;
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.booking-message.warning {
    background: #fef3c7;
    color: #92400e;
}

.booking-message.info {
    background: #dbeafe;
    color: #1e40af;
}

.booking-message i {
    font-size: 1.2rem;
}

.booking-message p {
    margin: 0;
    font-size: 0.875rem;
}

.booking-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn-login, .btn-register, .btn-logout {
    width: 100%;
    text-align: center;
    padding: 12px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: block;
    cursor: pointer;
}

.btn-login {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-login:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

.btn-register {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: white;
}

.btn-register:hover {
    transform: translateY(-2px);
    color: white;
    box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
}

.btn-logout {
    background: #ef4444;
    color: white;
    border: none;
}

.btn-logout:hover {
    background: #dc2626;
    transform: translateY(-2px);
    color: white;
}

/* Contact Card */
.btn-contact {
    width: 100%;
    padding: 12px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
    text-align: center;
}

.btn-contact:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

.contact-note {
    margin-top: 10px;
    text-align: center;
}

.contact-note small {
    color: #9ca3af;
    font-size: 0.7rem;
}

/* RTL Support */
body.rtl .info-row {
    flex-direction: row-reverse;
}

body.rtl .review-header {
    flex-direction: row-reverse;
}

body.rtl .review-comment {
    margin-left: 0;
    margin-right: 65px;
}

body.rtl .session-type label {
    flex-direction: row-reverse;
}

/* Responsive */
@media (max-width: 992px) {
    .profile-layout {
        flex-direction: column;
    }
    
    .rating-summary {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 768px) {
    .profile-hero-content {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-specialty, .profile-rating, .profile-badge {
        justify-content: center;
    }
    
    .review-header {
        flex-wrap: wrap;
    }
    
    .review-comment {
        margin-left: 0;
    }
    
    body.rtl .review-comment {
        margin-right: 0;
    }
}
</style>

<script>
function openReviewsModal() {
    document.getElementById('reviewsModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeReviewsModal() {
    document.getElementById('reviewsModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('reviewsModal');
        if (modal && modal.style.display === 'flex') {
            closeReviewsModal();
        }
    }
});

document.getElementById('reviewsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewsModal();
    }
});

// Book Now button - Redirect to correct booking page with session type
const bookBtn = document.getElementById('bookNowBtn');
if (bookBtn) {
    bookBtn.addEventListener('click', function() {
        const selectedType = document.querySelector('input[name="session_type"]:checked');
        const sessionType = selectedType ? selectedType.value : 'video';
        
        // Redirect to booking page with session type as query parameter
        window.location.href = '{{ route("patient.book", $specialist->id) }}?type=' + sessionType;
    });
}

// Contact button
const contactBtn = document.getElementById('contactBtn');
if (contactBtn && !contactBtn.disabled) {
    contactBtn.addEventListener('click', function() {
        window.location.href = '{{ route("chat.index") }}?specialist={{ $specialist->id }}';
    });
}
</script>

@endsection