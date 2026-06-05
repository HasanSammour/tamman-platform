{{-- resources/views/patient/sessions/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Session Details') . ' - ' . __('Tamman'))

@section('page-title', __('Session Details'))

@section('content')
    <div class="session-details-container">
        <div class="details-card animate-scale-in">
            <!-- Back Button and Status - Side by Side -->
            <div class="details-header-row">
                <a href="{{ route('patient.sessions') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Sessions') }}
                </a>
                <div class="session-status {{ $statusBadgeClass }}">
                    <div class="status-icon">
                        <i class="fas {{ $statusIcon }}"></i>
                    </div>
                    <span class="status-text">{{ $statusText }}</span>
                    @if($session->is_free)
                        <span class="free-badge">
                            <i class="fas fa-gift"></i> {{ __('Free') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Countdown Timer - Centered with Icon -->
            @if($session->status === 'scheduled' && isset($timeUntil))
                <div class="countdown-card animate-fade-in-up">
                    <div class="countdown-content">
                        <div class="countdown-icon">
                            <i class="fas fa-hourglass-half"></i>
                            <span class="countdown-label">{{ __('Session starts in') }}</span>
                        </div>
                        <div class="countdown-timer" id="sessionCountdown">
                            @if(isset($timeUntil['days']))
                                <div class="countdown-unit">
                                    <span class="countdown-number" id="days">0</span>
                                    <span class="countdown-unit-label">{{ __('days') }}</span>
                                </div>
                            @endif
                            @if(isset($timeUntil['hours']) || isset($timeUntil['days']))
                                <div class="countdown-unit">
                                    <span class="countdown-number" id="hours">0</span>
                                    <span class="countdown-unit-label">{{ __('hours') }}</span>
                                </div>
                            @endif
                            <div class="countdown-unit">
                                <span class="countdown-number" id="minutes">0</span>
                                <span class="countdown-unit-label">{{ __('minutes') }}</span>
                            </div>
                            <div class="countdown-unit">
                                <span class="countdown-number" id="seconds">0</span>
                                <span class="countdown-unit-label">{{ __('seconds') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Free Session Reward Info -->
            @if($session->is_free && $freeRewardName)
                <div class="free-session-alert animate-fade-in-up">
                    <div class="alert-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="alert-content">
                        <strong>{{ __('Free Session') }}</strong>
                        <p>{{ __('This session was booked using your reward:') }} <span
                                class="reward-name">{{ $freeRewardName }}</span></p>
                    </div>
                </div>
            @endif

            <!-- Specialist Info Section -->
            <div class="specialist-section animate-fade-in-up">
                <div class="specialist-avatar">
                    @php
                        $specialistImage = $session->specialist->getProfileImageUrl();
                        $specialistInitial = mb_substr($session->specialist->name, 0, 1, 'UTF-8');
                    @endphp
                    @if($specialistImage)
                        <img src="{{ $specialistImage }}" alt="{{ $session->specialist->name }}">
                    @else
                        <div class="avatar-placeholder">{{ $specialistInitial }}</div>
                    @endif
                    <div class="specialist-online-dot"></div>
                </div>
                <div class="specialist-info">
                    <h2>{{ $session->specialist->name }}</h2>
                    <p class="specialization">
                        <i class="fas fa-stethoscope"></i>
                        {{ $session->specialist->specialistProfile->specialization ?? __('Psychologist') }}
                    </p>
                    <div class="rating">
                        @php $rating = $session->specialist->specialistProfile->rating_avg ?? 0; @endphp
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($rating))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-value">({{ number_format($rating, 1) }})</span>
                        <span class="reviews-count">{{ __('based on') }}
                            {{ $session->specialist->reviewsReceived->count() }} {{ __('reviews') }}</span>
                    </div>
                </div>
            </div>

            <!-- Session Details Grid -->
            <div class="details-section animate-fade-in-up">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    <h3>{{ __('Session Information') }}</h3>
                </div>

                <div class="details-grid">
                    <div class="detail-card">
                        <div class="detail-icon" style="background: #ede9fe;">
                            <i class="fas fa-calendar-alt" style="color: #7c3aed;"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">{{ __('Date') }}</span>
                            <span class="detail-value">{{ $formattedDate }}</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon" style="background: #d1fae5;">
                            <i class="fas fa-clock" style="color: #10b981;"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">{{ __('Time') }}</span>
                            <span class="detail-value">{{ $startTime }} - {{ $endTime }}</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon" style="background: {{ $sessionTypeColor }}20;">
                            <i class="fas {{ $sessionTypeIcon }}" style="color: {{ $sessionTypeColor }};"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">{{ __('Session Type') }}</span>
                            <span class="detail-value">
                                <span class="session-type-badge {{ $session->session_type }}">
                                    {{ __(ucfirst($session->session_type)) }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon" style="background: #fef3c7;">
                            <i class="fas fa-hourglass-half" style="color: #f59e0b;"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">{{ __('Duration') }}</span>
                            <span class="detail-value">{{ $session->duration_minutes }} {{ __('minutes') }}</span>
                        </div>
                    </div>

                    <div class="detail-card highlight">
                        <div class="detail-icon" style="background: #f5f3ff;">
                            <i class="fas fa-credit-card" style="color: #7c3aed;"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">{{ __('Payment Method') }}</span>
                            <span class="detail-value">
                                @if($session->is_free)
                                    <span class="payment-badge free">
                                        <i class="fas fa-gift"></i> {{ $paymentMethodText }}
                                    </span>
                                @elseif($session->is_paid_by_credit)
                                    <span class="payment-badge credit">
                                        <i class="fas fa-coins"></i> {{ $paymentMethodText }}
                                    </span>
                                @else
                                    <span class="payment-badge cash">
                                        <i class="fas fa-money-bill-wave"></i> {{ $paymentMethodText }}
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($session->points_awarded > 0)
                        <div class="detail-card">
                            <div class="detail-icon" style="background: #fef9c3;">
                                <i class="fas fa-star" style="color: #eab308;"></i>
                            </div>
                            <div class="detail-content">
                                <span class="detail-label">{{ __('Points Earned') }}</span>
                                <span class="detail-value points-earned">+{{ $session->points_awarded }}
                                    {{ __('points') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Rating Section (If exists) -->
            @if($sessionRating)
                <div class="rating-section animate-fade-in-up">
                    <div class="section-title">
                        <i class="fas fa-star"></i>
                        <h3>{{ __('Your Rating') }}</h3>
                    </div>
                    <div class="rating-card">
                        <div class="rating-stars-display">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $sessionRating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <span class="rating-date">{{ $reviewDate }}</span>
                        </div>
                        @if($sessionComment)
                            <div class="rating-comment">
                                <i class="fas fa-quote-left"></i>
                                <p>{{ $sessionComment }}</p>
                                <i class="fas fa-quote-right"></i>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Meeting Link Section (for video/audio) -->
            @if($session->session_type != 'text' && $session->meeting_link)
                <div class="meeting-section animate-fade-in-up">
                    <div class="section-title">
                        <i class="fas fa-video"></i>
                        <h3>{{ __('Meeting Link') }}</h3>
                    </div>
                    <div class="meeting-card">
                        <div class="meeting-link-container">
                            <div class="meeting-link-wrapper">
                                <input type="text" id="meetingLink" value="{{ $session->meeting_link }}" readonly>
                                <button class="btn-copy-link" onclick="copyMeetingLink()">
                                    <i class="fas fa-copy"></i> {{ __('Copy') }}
                                </button>
                            </div>
                        </div>
                        @if(in_array($session->status, ['scheduled', 'ongoing']))

                            <!-- Participant Status Display -->
                            <div class="participant-status">
                                <div class="status-item">
                                    <i class="fas fa-user-md"></i>
                                    <span>{{ __('Specialist') }}</span>
                                    @if($session->specialist_joined)
                                        <span class="badge-joined"><i class="fas fa-check-circle"></i> {{ __('Joined') }}</span>
                                    @else
                                        <span class="badge-waiting"><i class="fas fa-clock"></i> {{ __('Waiting') }}</span>
                                    @endif
                                </div>
                                <div class="status-item">
                                    <i class="fas fa-user"></i>
                                    <span>{{ __('You') }}</span>
                                    @if($session->patient_joined)
                                        <span class="badge-joined"><i class="fas fa-check-circle"></i> {{ __('Joined') }}</span>
                                    @else
                                        <span class="badge-waiting"><i class="fas fa-clock"></i> {{ __('Waiting') }}</span>
                                    @endif
                                </div>
                            </div>

                            @if($canJoin)
                                <a href="{{ route('patient.sessions.join', $session->id) }}" class="btn-join-now" target="_blank">
                                    <i class="fas fa-video"></i> {{ __('Join Session Now') }}
                                    <span class="join-badge">{{ __('Available Now') }}</span>
                                </a>
                            @elseif($session->status == 'scheduled')
                                <div class="join-notice">
                                    <i class="fas fa-info-circle"></i>
                                    <span>{{ __('You can join the session 15 minutes before the scheduled time.') }}</span>
                                    @if($joinTime)
                                        <strong>{{ $joinTime->translatedFormat('h:i A') }}</strong>
                                    @endif
                                </div>
                            @endif

                            <!-- Security Note -->
                            <div class="security-note">
                                <i class="fas fa-shield-alt"></i>
                                <small>{{ __('This is a private session. Only you and the specialist can join.') }}</small>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Text Chat Section -->
            @if($session->session_type == 'text')
                <div class="chat-section animate-fade-in-up">
                    <div class="section-title">
                        <i class="fas fa-comment-dots"></i>
                        <h3>{{ __('Text Chat Session') }}</h3>
                    </div>
                    <div class="chat-card">
                        <i class="fas fa-comments"></i>
                        <p>{{ __('Your text chat session will be available in the "Messages" section.') }}</p>
                            <a href="{{ route('chat.index', ['user' => $session->specialist_id]) }}" class="btn-go-to-chat">
                            <i class="fas fa-comments"></i> {{ __('Go to Messages') }}
                        </a>
                    </div>
                </div>
            @endif

            <!-- Session Notes -->
            @if($session->notes)
                <div class="notes-section animate-fade-in-up">
                    <div class="section-title">
                        <i class="fas fa-pen"></i>
                        <h3>{{ __('Session Notes') }}</h3>
                    </div>
                    <div class="notes-card">
                        <i class="fas fa-sticky-note"></i>
                        <p>{{ $session->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Cancel Policy -->
            @if($session->status == 'scheduled')
                <div class="policy-section animate-fade-in-up">
                    <div class="policy-card {{ $canCancel ? 'active' : 'inactive' }}">
                        <i class="fas {{ $canCancel ? 'fa-info-circle' : 'fa-clock' }}"></i>
                        <div class="policy-content">
                            <strong>{{ __('Cancellation Policy') }}</strong>
                            @if($canCancel)
                                <p>{{ __('You can cancel this session up to 24 hours before the scheduled time.') }}</p>
                            @else
                                <p class="policy-warning-text">
                                    {{ __('Cancellation window has passed. You cannot cancel this session.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-buttons animate-fade-in-up">
                @if($session->status == 'scheduled')
                    @if($canJoin && $session->session_type != 'text')
                        <a href="{{ route('patient.sessions.join', $session->id) }}" class="btn-join-action" target="_blank">
                            <i class="fas fa-video"></i> {{ __('Join Session') }}
                            <span class="btn-badge">{{ __('Now') }}</span>
                        </a>
                    @endif
                    @if($canCancel)
                        <button class="btn-cancel-action" id="cancelSessionBtn" data-session-id="{{ $session->id }}"
                            data-session-time="{{ $session->session_datetime }}">
                            <i class="fas fa-times-circle"></i> {{ __('Cancel Session') }}
                        </button>
                    @endif
                @endif
                @if($session->status == 'completed' && !$session->review && $canRate)
                    <button class="btn-rate-action" id="rateSessionBtn" data-session-id="{{ $session->id }}"
                        data-specialist-name="{{ $session->specialist->name }}">
                        <i class="fas fa-star"></i> {{ __('Rate This Session') }}
                    </button>
                @endif
                <a href="{{ route('patient.book', $session->specialist_id) }}" class="btn-book-again-action">
                    <i class="fas fa-redo-alt"></i> {{ __('Book Another Session') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Rate Session Modal -->
    <div id="rateModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-star"></i> {{ __('Rate Your Session') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p id="rateSpecialistName"></p>
                <div class="rating-stars">
                    <i class="far fa-star" data-rating="1"></i>
                    <i class="far fa-star" data-rating="2"></i>
                    <i class="far fa-star" data-rating="3"></i>
                    <i class="far fa-star" data-rating="4"></i>
                    <i class="far fa-star" data-rating="5"></i>
                </div>
                <textarea id="rateComment" class="form-control" rows="4"
                    placeholder="{{ __('Share your experience...') }}"></textarea>
                <input type="hidden" id="rateSessionId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn-submit-rate" id="submitRateBtn">
                    <span class="btn-text">{{ __('Submit Rating') }}</span>
                    <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .session-details-container {
                max-width: 900px;
                margin: 0 auto;
                padding: 20px;
            }

            .details-card {
                background: white;
                border-radius: 28px;
                padding: 30px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
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

            .animate-scale-in {
                animation: scaleIn 0.4s ease;
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease forwards;
            }

            /* Details Header Row - Back Button + Status Side by Side */
            .details-header-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
                flex-wrap: wrap;
                gap: 15px;
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f3f4f6;
                padding: 8px 18px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.8rem;
                color: #374151;
                transition: all 0.3s ease;
            }

            .btn-back:hover {
                background: #e5e7eb;
                transform: translateX(-3px);
            }

            body.rtl .btn-back:hover {
                transform: translateX(3px);
            }

            /* Session Status */
            .session-status {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 16px;
                border-radius: 50px;
            }

            .session-status.scheduled {
                background: #ede9fe;
            }

            .session-status.completed {
                background: #d1fae5;
            }

            .session-status.cancelled {
                background: #fee2e2;
            }

            .session-status.no-show {
                background: #fef3c7;
            }

            .status-icon {
                width: 28px;
                height: 28px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .session-status.scheduled .status-icon {
                background: #7c3aed20;
                color: #7c3aed;
            }

            .session-status.completed .status-icon {
                background: #10b98120;
                color: #10b981;
            }

            .session-status.cancelled .status-icon {
                background: #ef444420;
                color: #ef4444;
            }

            .session-status.no-show .status-icon {
                background: #f59e0b20;
                color: #f59e0b;
            }

            .status-text {
                font-weight: 600;
                font-size: 0.85rem;
            }

            .free-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                background: #10b981;
                color: white;
                padding: 2px 8px;
                border-radius: 30px;
                font-size: 0.65rem;
                font-weight: 500;
            }

            /* Countdown Card - Centered */
            .countdown-card {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                border-radius: 20px;
                padding: 20px 25px;
                margin-bottom: 25px;
            }

            .countdown-content {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 30px;
                flex-wrap: wrap;
            }

            .countdown-icon {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .countdown-icon i {
                font-size: 1.8rem;
                color: #d97706;
            }

            .countdown-label {
                font-size: 0.85rem;
                font-weight: 500;
                color: #92400e;
            }

            .countdown-timer {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .countdown-unit {
                display: flex;
                flex-direction: column;
                align-items: center;
                background: white;
                padding: 8px 16px;
                border-radius: 12px;
                min-width: 65px;
            }

            .countdown-number {
                font-size: 1.3rem;
                font-weight: 700;
                color: #d97706;
                font-family: monospace;
            }

            .countdown-unit-label {
                font-size: 0.65rem;
                color: #92400e;
            }

            /* Free Session Alert */
            .free-session-alert {
                display: flex;
                align-items: center;
                gap: 15px;
                background: #d1fae5;
                padding: 15px 20px;
                border-radius: 16px;
                margin-bottom: 25px;
            }

            .alert-icon i {
                font-size: 1.5rem;
                color: #059669;
            }

            .alert-content strong {
                color: #065f46;
                display: block;
                margin-bottom: 4px;
            }

            .alert-content p {
                color: #065f46;
                margin: 0;
                font-size: 0.85rem;
            }

            .reward-name {
                font-weight: 600;
                background: #a7f3d0;
                padding: 2px 8px;
                border-radius: 20px;
                display: inline-block;
            }

            /* Specialist Section */
            .specialist-section {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 20px;
                background: #f9fafb;
                border-radius: 24px;
                margin-bottom: 25px;
                position: relative;
                flex-wrap: wrap;
            }

            .specialist-avatar {
                position: relative;
            }

            .specialist-avatar img,
            .avatar-placeholder {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                font-weight: 600;
                color: white;
            }

            .specialist-online-dot {
                position: absolute;
                bottom: 5px;
                right: 5px;
                width: 14px;
                height: 14px;
                background: #10b981;
                border-radius: 50%;
                border: 2px solid white;
            }

            .specialist-info h2 {
                font-size: 1.3rem;
                margin-bottom: 5px;
            }

            .specialization {
                color: #6b7280;
                margin-bottom: 8px;
                font-size: 0.85rem;
            }

            .rating {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .stars {
                color: #fbbf24;
                font-size: 0.85rem;
            }

            .rating-value {
                font-weight: 600;
                color: #1f2937;
            }

            .reviews-count {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            /* Section Title */
            .section-title {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
            }

            .section-title i {
                width: 32px;
                height: 32px;
                background: #ede9fe;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #7c3aed;
                font-size: 0.9rem;
            }

            .section-title h3 {
                font-size: 1.1rem;
                margin: 0;
                color: #1f2937;
            }

            /* Details Grid */
            .details-section {
                margin-bottom: 25px;
            }

            .details-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .detail-card {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 15px;
                background: #f9fafb;
                border-radius: 16px;
                transition: all 0.3s ease;
            }

            .detail-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            }

            .detail-card.highlight {
                background: #f5f3ff;
            }

            .detail-icon {
                width: 45px;
                height: 45px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .detail-icon i {
                font-size: 1.2rem;
            }

            .detail-content {
                flex: 1;
            }

            .detail-label {
                display: block;
                font-size: 0.7rem;
                color: #9ca3af;
                margin-bottom: 4px;
            }

            .detail-value {
                font-weight: 600;
                color: #1f2937;
            }

            .session-type-badge {
                display: inline-block;
                padding: 2px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .session-type-badge.video {
                background: #ede9fe;
                color: #7c3aed;
            }

            .session-type-badge.audio {
                background: #d1fae5;
                color: #059669;
            }

            .session-type-badge.text {
                background: #fef3c7;
                color: #d97706;
            }

            .payment-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
            }

            .payment-badge.free {
                background: #d1fae5;
                color: #065f46;
            }

            .payment-badge.credit {
                background: #ede9fe;
                color: #7c3aed;
            }

            .payment-badge.cash {
                background: #fef3c7;
                color: #d97706;
            }

            .points-earned {
                color: #10b981;
            }

            /* Rating Section */
            .rating-section {
                margin-bottom: 25px;
            }

            .rating-card {
                background: #fef3c7;
                border-radius: 20px;
                padding: 20px;
            }

            .rating-stars-display {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
                flex-wrap: wrap;
            }

            .rating-stars-display i {
                font-size: 1.1rem;
                color: #fbbf24;
            }

            .rating-date {
                font-size: 0.7rem;
                color: #92400e;
            }

            .rating-comment {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                background: white;
                padding: 15px;
                border-radius: 16px;
            }

            .rating-comment i:first-child {
                color: #d97706;
                font-size: 0.8rem;
                opacity: 0.5;
            }

            .rating-comment i:last-child {
                color: #d97706;
                font-size: 0.8rem;
                opacity: 0.5;
                align-self: flex-end;
            }

            .rating-comment p {
                flex: 1;
                margin: 0;
                font-size: 0.85rem;
                color: #78350f;
                line-height: 1.5;
            }

            /* Meeting Section */
            .meeting-section,
            .chat-section,
            .notes-section,
            .policy-section {
                margin-bottom: 25px;
            }

            .meeting-card,
            .chat-card,
            .notes-card,
            .policy-card {
                background: #f9fafb;
                border-radius: 20px;
                padding: 20px;
            }

            .meeting-link-wrapper {
                display: flex;
                gap: 12px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }

            .meeting-link-wrapper input {
                flex: 1;
                padding: 12px 16px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.8rem;
                color: #1f2937;
            }

            .meeting-link-wrapper input:focus {
                outline: none;
                border-color: #7c3aed;
            }

            .btn-copy-link {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 0 24px;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.8rem;
            }

            .btn-copy-link:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .btn-join-now {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                background: #10b981;
                color: white;
                padding: 12px 24px;
                border-radius: 40px;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .btn-join-now:hover {
                background: #059669;
                transform: translateY(-2px);
                color: white;
            }

            .join-badge {
                background: rgba(255, 255, 255, 0.2);
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 0.65rem;
            }

            .join-notice {
                display: flex;
                align-items: center;
                gap: 10px;
                background: #fef3c7;
                padding: 12px 16px;
                border-radius: 12px;
                font-size: 0.75rem;
                color: #d97706;
                flex-wrap: wrap;
            }

            /* Chat Card */
            .chat-card {
                text-align: center;
            }

            .chat-card i {
                font-size: 2rem;
                color: #f59e0b;
                margin-bottom: 10px;
            }

            .chat-card p {
                margin-bottom: 15px;
                color: #6b7280;
            }

            .btn-go-to-chat {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f59e0b;
                color: white;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .btn-go-to-chat:hover {
                background: #d97706;
                transform: translateY(-2px);
                color: white;
            }

            /* Notes Card */
            .notes-card {
                display: flex;
                gap: 12px;
                background: #fef2f2;
            }

            .notes-card i {
                color: #ef4444;
                font-size: 1.2rem;
            }

            .notes-card p {
                margin: 0;
                color: #991b1b;
                line-height: 1.5;
                flex: 1;
            }

            /* Policy Card */
            .policy-card {
                display: flex;
                gap: 12px;
                background: #fefce8;
            }

            .policy-card i {
                color: #ca8a04;
                font-size: 1.2rem;
            }

            .policy-content strong {
                display: block;
                color: #713f12;
                margin-bottom: 4px;
            }

            .policy-content p {
                margin: 0;
                font-size: 0.8rem;
                color: #713f12;
            }

            .policy-warning {
                display: inline-block;
                margin-top: 8px;
                color: #ef4444;
                font-size: 0.7rem;
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-join-action,
            .btn-cancel-action,
            .btn-rate-action,
            .btn-book-again-action {
                padding: 12px 24px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                border: none;
                transition: all 0.3s ease;
            }

            .btn-join-action {
                background: #10b981;
                color: white;
            }

            .btn-join-action:hover {
                background: #059669;
                transform: translateY(-2px);
                color: white;
            }

            .btn-cancel-action {
                background: #fee2e2;
                color: #991b1b;
            }

            .btn-cancel-action:hover {
                background: #fecaca;
                transform: translateY(-2px);
            }

            .btn-rate-action {
                background: #fef3c7;
                color: #d97706;
            }

            .btn-rate-action:hover {
                background: #fde68a;
                transform: translateY(-2px);
            }

            .btn-book-again-action {
                background: #ede9fe;
                color: #7c3aed;
            }

            .btn-book-again-action:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            .btn-badge {
                background: rgba(255, 255, 255, 0.2);
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 0.65rem;
            }

            /* Modal */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .modal-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .modal-container {
                background: white;
                border-radius: 24px;
                max-width: 500px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
                transform: scale(0.9);
                transition: transform 0.3s ease;
                z-index: 10001;
            }

            .modal-overlay.active .modal-container {
                transform: scale(1);
            }

            .modal-header {
                padding: 20px 25px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-header h3 {
                margin: 0;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
            }

            .modal-body {
                padding: 25px;
            }

            .modal-footer {
                padding: 20px 25px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .btn-cancel-modal {
                background: #f3f4f6;
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-submit-rate {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            /* Participant Status */
            .participant-status {
                background: #f8fafc;
                border-radius: 16px;
                padding: 15px;
                margin: 15px 0;
                display: flex;
                gap: 20px;
                flex-wrap: wrap;
            }

            .status-item {
                flex: 1;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 12px;
                background: white;
                border-radius: 12px;
                font-size: 0.85rem;
            }

            .status-item i:first-child {
                width: 30px;
                color: #7c3aed;
            }

            .badge-joined {
                color: #10b981;
                font-size: 0.7rem;
                margin-left: auto;
            }

            .badge-waiting {
                color: #f59e0b;
                font-size: 0.7rem;
                margin-left: auto;
            }

            .security-note {
                margin-top: 15px;
                padding: 10px;
                background: #fef3c7;
                border-radius: 12px;
                text-align: center;
                font-size: 0.7rem;
                color: #d97706;
            }

            .security-note i {
                margin-right: 5px;
            }

            .rating-stars {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin: 20px 0;
            }

            .rating-stars i {
                font-size: 2rem;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #d1d5db;
            }

            .rating-stars i:hover,
            .rating-stars i.active {
                color: #fbbf24;
                transform: scale(1.1);
            }

            .policy-card.active {
                background: #fefce8;
            }

            .policy-card.inactive {
                background: #fee2e2;
            }

            .policy-warning-text {
                color: #991b1b;
                font-weight: 500;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .details-card {
                    padding: 20px;
                }

                .details-grid {
                    grid-template-columns: 1fr;
                }

                .specialist-section {
                    flex-direction: column;
                    text-align: center;
                }

                .action-buttons {
                    flex-direction: column;
                }

                .btn-join-action,
                .btn-cancel-action,
                .btn-rate-action,
                .btn-book-again-action {
                    justify-content: center;
                }

                .countdown-content {
                    flex-direction: column;
                    text-align: center;
                }

                .countdown-timer {
                    justify-content: center;
                }

                .meeting-link-wrapper {
                    flex-direction: column;
                }

                .btn-copy-link {
                    padding: 10px;
                }

                .details-header-row {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }

            @media (max-width: 480px) {
                .countdown-unit {
                    min-width: 55px;
                    padding: 5px 10px;
                }

                .countdown-number {
                    font-size: 1rem;
                }
            }

            /* RTL Support */
            body.rtl .btn-back:hover {
                transform: translateX(3px);
            }

            body.rtl .detail-card {
                flex-direction: row;
            }

            body.rtl .rating-stars-display {
                flex-direction: row;
            }

            body.rtl .rating-comment {
                flex-direction: row;
            }

            body.rtl .policy-card {
                flex-direction: row;
            }

            body.rtl .notes-card {
                flex-direction: row;
            }

            body.rtl .countdown-content {
                flex-direction: row;
            }

            @media (max-width: 768px) {
                body.rtl .countdown-content {
                    flex-direction: column;
                }

                body.rtl .details-header-row {
                    align-items: flex-start;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Countdown Timer
            @if($session->status === 'scheduled' && isset($timeUntil))
                function updateCountdown() {
                    const sessionTime = new Date('{{ Carbon\Carbon::parse($session->session_datetime)->toIso8601String() }}');
                    const now = new Date();
                    const diff = sessionTime - now;

                    if (diff <= 0) {
                        const countdownCard = document.querySelector('.countdown-card');
                        if (countdownCard) countdownCard.remove();
                        location.reload();
                        return;
                    }

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (86400000)) / 3600000);
                    const minutes = Math.floor((diff % 3600000) / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000);

                    if (document.getElementById('days')) document.getElementById('days').textContent = days;
                    if (document.getElementById('hours')) document.getElementById('hours').textContent = hours;
                    document.getElementById('minutes').textContent = minutes;
                    document.getElementById('seconds').textContent = seconds;
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            @endif

                // Copy Meeting Link
                function copyMeetingLink() {
                    const linkInput = document.getElementById('meetingLink');
                    if (linkInput) {
                        linkInput.select();
                        document.execCommand('copy');

                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Copied!") }}',
                            text: '{{ __("Meeting link copied to clipboard") }}',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }

            // Cancel Session
            const cancelBtn = document.getElementById('cancelSessionBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', async () => {
                    const sessionId = cancelBtn.dataset.sessionId;

                    const result = await Swal.fire({
                        title: '{{ __("Cancel Session") }}',
                        text: '{{ __("Are you sure you want to cancel this session?") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '{{ __("Yes, cancel it") }}',
                        cancelButtonText: '{{ __("No, keep it") }}'
                    });

                    if (!result.isConfirmed) return;

                    const originalText = cancelBtn.innerHTML;
                    cancelBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Cancelling...") }}';
                    cancelBtn.disabled = true;

                    try {
                        const response = await fetch(`/patient/sessions/${sessionId}/cancel`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Cancelled") }}',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(() => {
                                window.location.href = '{{ route("patient.sessions") }}';
                            }, 2000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error") }}',
                                text: data.message,
                                confirmButtonColor: '#7c3aed'
                            });
                            cancelBtn.innerHTML = originalText;
                            cancelBtn.disabled = false;
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        cancelBtn.innerHTML = originalText;
                        cancelBtn.disabled = false;
                    }
                });
            }

            // Rate Session Modal
            let selectedRating = 0;
            let rateSessionId = null;

            const rateBtn = document.getElementById('rateSessionBtn');
            if (rateBtn) {
                rateBtn.addEventListener('click', function () {
                    rateSessionId = this.dataset.sessionId;
                    const specialistName = this.dataset.specialistName;

                    document.getElementById('rateSpecialistName').innerHTML = `{{ __("Rate your session with") }} ${specialistName}`;
                    document.getElementById('rateSessionId').value = rateSessionId;
                    document.getElementById('rateModal').classList.add('active');

                    selectedRating = 0;
                    document.querySelectorAll('.rating-stars i').forEach(star => {
                        star.classList.remove('active');
                        star.classList.remove('fas');
                        star.classList.add('far');
                    });
                    document.getElementById('rateComment').value = '';
                });
            }

            document.querySelectorAll('.rating-stars i').forEach(star => {
                star.addEventListener('click', function () {
                    selectedRating = parseInt(this.dataset.rating);
                    document.querySelectorAll('.rating-stars i').forEach((s, index) => {
                        if (index < selectedRating) {
                            s.classList.add('active');
                            s.classList.remove('far');
                            s.classList.add('fas');
                        } else {
                            s.classList.remove('active');
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });

                star.addEventListener('mouseenter', function () {
                    const rating = parseInt(this.dataset.rating);
                    document.querySelectorAll('.rating-stars i').forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('fas');
                            s.classList.remove('far');
                        } else {
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });

                star.addEventListener('mouseleave', function () {
                    document.querySelectorAll('.rating-stars i').forEach((s, index) => {
                        if (index < selectedRating) {
                            s.classList.add('fas');
                            s.classList.remove('far');
                        } else {
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });
            });

            document.getElementById('submitRateBtn')?.addEventListener('click', async () => {
                if (selectedRating === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("No Rating") }}',
                        text: '{{ __("Please select a rating before submitting.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                    return;
                }

                const sessionId = document.getElementById('rateSessionId').value;
                const comment = document.getElementById('rateComment').value;
                const submitBtn = document.getElementById('submitRateBtn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnSpinner = submitBtn.querySelector('.btn-spinner');

                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`/patient/sessions/${sessionId}/rate`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            rating: selectedRating,
                            comment: comment
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Thank You!") }}',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        document.getElementById('rateModal').classList.remove('active');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                } finally {
                    btnText.style.display = 'inline-block';
                    btnSpinner.style.display = 'none';
                    submitBtn.disabled = false;
                }
            });

            // Modal Close Handlers
            document.querySelectorAll('.modal-close, .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.modal-overlay').forEach(modal => {
                        modal.classList.remove('active');
                    });
                });
            });

            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                    }
                });
            });
        </script>
    @endpush

@endsection