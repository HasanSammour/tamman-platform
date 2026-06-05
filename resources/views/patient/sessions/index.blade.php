{{-- resources/views/patient/sessions/index.blade.php --}}
@extends('layouts.app')

@section('title', __('My Sessions') . ' - ' . __('Tamman'))

@section('page-title', __('My Sessions'))

@section('content')
    <div class="sessions-container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3 id="upcomingCount">{{ $statusCounts['upcoming'] }}</h3>
                    <p>{{ __('Upcoming Sessions') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $statusCounts['completed'] }}</h3>
                    <p>{{ __('Completed Sessions') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $statusCounts['cancelled'] }}</h3>
                    <p>{{ __('Cancelled Sessions') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $statusCounts['upcoming'] + $statusCounts['completed'] }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs - Centered -->
        <div class="sessions-tabs">
            <button class="tab-btn active" data-tab="upcoming">
                <i class="fas fa-calendar-week"></i> {{ __('Upcoming') }}
                @if($statusCounts['upcoming'] > 0)
                    <span class="tab-badge">{{ $statusCounts['upcoming'] }}</span>
                @endif
            </button>
            <button class="tab-btn" data-tab="past">
                <i class="fas fa-history"></i> {{ __('Past Sessions') }}
            </button>
        </div>

        <!-- Upcoming Sessions Tab -->
        <div class="tab-content active" id="tab-upcoming">
            @if($upcomingSessions->count() > 0)
                <div class="sessions-grid" id="upcomingSessionsList">
                    @foreach($upcomingSessions as $session)
                        @php
                            $sessionTime = Carbon\Carbon::parse($session->session_datetime);
                            $now = now();
                            $minutesUntil = $sessionTime->diffInMinutes($now, false);
                            $hoursUntil = $sessionTime->diffInHours($now, false);
                            $minutesAfter = $now->diffInMinutes($sessionTime, false);

                            // Can cancel if more than 24 hours away (negative hoursUntil means future)
                            $canCancel = $hoursUntil < -24;

                            // Can join if within 15 minutes before session OR during session (up to 60 minutes after start)
                            $canJoin = ($minutesUntil <= 15 && $minutesUntil >= -60);

                            // Get join button text based on session type
                            $joinButtonText = $session->session_type != 'text' ? __('Join Session') : __('Open Chat');
                            $joinButtonIcon = $session->session_type != 'text' ? 'fa-video' : 'fa-comment-dots';
                            $joinButtonRoute = $session->session_type != 'text'
                                ? route('patient.sessions.join', $session->id)
                                : route('chat.index', ['user' => $session->specialist_id]);

                            // Get free reward name if applicable
                            $freeRewardName = null;
                            if ($session->is_free && $session->rewardRedemption && $session->rewardRedemption->reward) {
                                $rewardName = $session->rewardRedemption->reward->name;
                                if (is_string($rewardName) && str_starts_with($rewardName, '{')) {
                                    $decoded = json_decode($rewardName, true);
                                    $locale = app()->getLocale();
                                    $freeRewardName = $decoded[$locale] ?? $decoded['en'] ?? null;
                                } else {
                                    $freeRewardName = $rewardName;
                                }
                            }
                        @endphp
                        <div class="session-card animate-scale-in" data-session-id="{{ $session->id }}">
                            <div class="session-card-header">
                                <div class="session-specialist">
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
                                    </div>
                                    <div class="specialist-info">
                                        <h4>{{ $session->specialist->name }}</h4>
                                        <p>{{ $session->specialist->specialistProfile->specialization ?? __('Psychologist') }}</p>
                                    </div>
                                </div>
                                <div class="session-status-badge scheduled">
                                    <i class="fas fa-clock"></i> {{ __('Scheduled') }}
                                    @if($session->is_free)
                                        <span class="free-badge-small">
                                            <i class="fas fa-gift"></i> {{ __('Free') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="session-card-body">
                                <div class="session-datetime">
                                    <div class="datetime-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>{{ $sessionTime->translatedFormat('l, F d, Y') }}</span>
                                    </div>
                                    <div class="datetime-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $sessionTime->translatedFormat('h:i A') }} -
                                            {{ $sessionTime->copy()->addMinutes($session->duration_minutes)->translatedFormat('h:i A') }}</span>
                                    </div>
                                    <div class="datetime-item">
                                        <i
                                            class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                                        <span>{{ __(ucfirst($session->session_type)) }} {{ __('Session') }}</span>
                                        @if($session->is_free && $freeRewardName)
                                            <span class="reward-tooltip" title="{{ $freeRewardName }}">
                                                <i class="fas fa-gift"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="countdown-timer" data-session-datetime="{{ $session->session_datetime }}"
                                    data-session-id="{{ $session->id }}" data-join-url="{{ $joinButtonRoute }}"
                                    data-join-text="{{ $joinButtonText }}" data-join-icon="{{ $joinButtonIcon }}">
                                    <div class="timer-display">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span class="timer-text">{{ __('Starts in') }}</span>
                                        <span class="timer-value">--:--:--</span>
                                    </div>
                                </div>
                            </div>
                            <div class="session-card-footer">
                                <a href="{{ $joinButtonRoute }}" class="btn-join {{ $canJoin ? '' : 'disabled' }}"
                                    data-session-id="{{ $session->id }}" data-original-href="{{ $joinButtonRoute }}">
                                    <i class="fas {{ $joinButtonIcon }}"></i> {{ $joinButtonText }}
                                </a>
                                <a href="{{ route('patient.sessions.show', $session->id) }}" class="btn-details">
                                    <i class="fas fa-info-circle"></i> {{ __('Details') }}
                                </a>
                                @if($canCancel)
                                    <button class="btn-cancel" data-session-id="{{ $session->id }}"
                                        data-session-datetime="{{ $session->session_datetime }}">
                                        <i class="fas fa-times-circle"></i> {{ __('Cancel') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state animate-fade-in">
                    <i class="fas fa-calendar-week"></i>
                    <h3>{{ __('No Upcoming Sessions') }}</h3>
                    <p>{{ __('You don\'t have any upcoming sessions scheduled.') }}</p>
                    <a href="{{ route('specialists.index') }}" class="btn-primary">
                        <i class="fas fa-search"></i> {{ __('Find a Specialist') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- Past Sessions Tab -->
        <div class="tab-content" id="tab-past">
            @if($pastSessions->count() > 0)
                <div class="sessions-grid">
                    @foreach($pastSessions as $session)
                        @php
                            $sessionTime = Carbon\Carbon::parse($session->session_datetime);
                            $statusClass = $session->status;
                            $statusText = __(ucfirst($session->status));

                            $freeRewardName = null;
                            if ($session->is_free && $session->rewardRedemption && $session->rewardRedemption->reward) {
                                $rewardName = $session->rewardRedemption->reward->name;
                                if (is_string($rewardName) && str_starts_with($rewardName, '{')) {
                                    $decoded = json_decode($rewardName, true);
                                    $locale = app()->getLocale();
                                    $freeRewardName = $decoded[$locale] ?? $decoded['en'] ?? null;
                                } else {
                                    $freeRewardName = $rewardName;
                                }
                            }
                        @endphp
                        <div class="session-card past animate-scale-in" data-session-id="{{ $session->id }}">
                            <div class="session-card-header">
                                <div class="session-specialist">
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
                                    </div>
                                    <div class="specialist-info">
                                        <h4>{{ $session->specialist->name }}</h4>
                                        <p>{{ $session->specialist->specialistProfile->specialization ?? __('Psychologist') }}</p>
                                    </div>
                                </div>
                                <div class="session-status-badge {{ $statusClass }}">
                                    <i
                                        class="fas {{ $session->status == 'completed' ? 'fa-check-circle' : ($session->status == 'cancelled' ? 'fa-times-circle' : 'fa-user-slash') }}"></i>
                                    {{ $statusText }}
                                    @if($session->is_free && $session->status == 'completed')
                                        <span class="free-badge-small">
                                            <i class="fas fa-gift"></i> {{ __('Free') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="session-card-body">
                                <div class="session-datetime">
                                    <div class="datetime-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>{{ $sessionTime->translatedFormat('l, F d, Y') }}</span>
                                    </div>
                                    <div class="datetime-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $sessionTime->translatedFormat('h:i A') }}</span>
                                    </div>
                                    <div class="datetime-item">
                                        <i
                                            class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                                        <span>{{ __(ucfirst($session->session_type)) }} {{ __('Session') }}</span>
                                        @if($session->is_free && $freeRewardName)
                                            <span class="reward-tooltip" title="{{ $freeRewardName }}">
                                                <i class="fas fa-gift"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($session->review)
                                    <div class="session-rating">
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $session->review->rating)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rated-text">{{ __('Rated') }}</span>
                                    </div>
                                @endif
                                @if($session->points_awarded > 0)
                                    <div class="points-earned">
                                        <i class="fas fa-star"></i>
                                        <span>+{{ $session->points_awarded }} {{ __('points') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="session-card-footer">
                                <a href="{{ route('patient.sessions.show', $session->id) }}" class="btn-details">
                                    <i class="fas fa-info-circle"></i> {{ __('Details') }}
                                </a>
                                @if($session->status == 'completed' && !$session->review)
                                    <button class="btn-rate" data-session-id="{{ $session->id }}"
                                        data-specialist-name="{{ $session->specialist->name }}">
                                        <i class="fas fa-star"></i> {{ __('Rate Session') }}
                                    </button>
                                @endif
                                <a href="{{ route('patient.book', $session->specialist_id) }}" class="btn-book-again">
                                    <i class="fas fa-redo-alt"></i> {{ __('Book Again') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state animate-fade-in">
                    <i class="fas fa-history"></i>
                    <h3>{{ __('No Past Sessions') }}</h3>
                    <p>{{ __('You haven\'t completed any sessions yet.') }}</p>
                    <a href="{{ route('specialists.index') }}" class="btn-primary">
                        <i class="fas fa-search"></i> {{ __('Find a Specialist') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Rate Session Modal - SIMPLIFIED VERSION -->
    <div id="rateModal" class="modal-overlay">
        <div class="modal-container rate-modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-star"></i> {{ __('Rate Your Session') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p class="rate-specialist-name" id="rateSpecialistName"></p>

                <!-- SIMPLE RATING STARS -->
                <div class="rating-wrapper">
                    <div class="rating-stars-simple">
                        <span class="star" data-rating="1">☆</span>
                        <span class="star" data-rating="2">☆</span>
                        <span class="star" data-rating="3">☆</span>
                        <span class="star" data-rating="4">☆</span>
                        <span class="star" data-rating="5">☆</span>
                    </div>
                </div>

                <textarea id="rateComment" class="form-control rate-comment" rows="4"
                    placeholder="{{ __('Share your experience with the specialist (optional)...') }}"></textarea>
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

    <!-- Cancel Session Modal -->
    <div id="cancelModal" class="modal-overlay">
        <div class="modal-container small">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> {{ __('Cancel Session') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to cancel this session?') }}</p>
                <p class="text-warning">{{ __('This action cannot be undone.') }}</p>
                <p class="cancel-note">{{ __('Note: Sessions can only be cancelled at least 24 hours in advance.') }}</p>
                <input type="hidden" id="cancelSessionId">
                <input type="hidden" id="cancelSessionDatetime">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-modal">{{ __('No, Keep It') }}</button>
                <button type="button" class="btn-confirm-cancel" id="confirmCancelBtn">
                    <span class="btn-text">{{ __('Yes, Cancel Session') }}</span>
                    <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .sessions-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stat-icon i {
                font-size: 1.5rem;
                color: #7c3aed;
            }

            .stat-info h3 {
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            .sessions-tabs {
                display: flex;
                justify-content: center;
                gap: 12px;
                margin-bottom: 25px;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 0;
            }

            .tab-btn {
                background: none;
                border: none;
                padding: 12px 24px;
                font-size: 0.9rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #6b7280;
                position: relative;
                border-radius: 40px 40px 0 0;
            }

            .tab-btn i {
                margin-right: 8px;
            }

            .tab-btn:hover {
                color: #7c3aed;
                background: #f5f3ff;
            }

            .tab-btn.active {
                color: #7c3aed;
                background: #f5f3ff;
            }

            .tab-btn.active::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 0;
                right: 0;
                height: 2px;
                background: #7c3aed;
            }

            .tab-badge {
                display: inline-block;
                background: #7c3aed;
                color: white;
                font-size: 0.7rem;
                padding: 2px 8px;
                border-radius: 20px;
                margin-left: 8px;
            }

            .tab-content {
                display: none;
                animation: fadeIn 0.3s ease;
            }

            .tab-content.active {
                display: block;
            }

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

            .sessions-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }

            .session-card {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
            }

            .session-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
            }

            .session-card.past {
                opacity: 0.85;
            }

            .session-card-header {
                padding: 20px;
                background: #f9fafb;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                border-bottom: 1px solid #e5e7eb;
            }

            .session-specialist {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .specialist-avatar img,
            .avatar-placeholder {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.2rem;
                font-weight: 600;
            }

            .specialist-info h4 {
                font-size: 1rem;
                margin-bottom: 4px;
                color: #1f2937;
            }

            .specialist-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .session-status-badge {
                padding: 6px 14px;
                border-radius: 30px;
                font-size: 0.7rem;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .session-status-badge.scheduled {
                background: #ede9fe;
                color: #7c3aed;
            }

            .session-status-badge.completed {
                background: #d1fae5;
                color: #065f46;
            }

            .session-status-badge.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            .session-status-badge.no_show {
                background: #fef3c7;
                color: #92400e;
            }

            .free-badge-small {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                background: #10b981;
                color: white;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 0.6rem;
                margin-left: 6px;
            }

            .reward-tooltip {
                margin-left: 6px;
                cursor: help;
                color: #f59e0b;
                font-size: 0.7rem;
            }

            .session-card-body {
                padding: 20px;
            }

            .session-datetime {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .datetime-item {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.85rem;
                color: #374151;
                flex-wrap: wrap;
            }

            .datetime-item i {
                width: 20px;
                color: #7c3aed;
            }

            .countdown-timer {
                margin-top: 20px;
                padding-top: 15px;
                border-top: 1px solid #e5e7eb;
            }

            .timer-display {
                display: flex;
                align-items: center;
                gap: 10px;
                background: #fef3c7;
                padding: 10px 15px;
                border-radius: 12px;
                flex-wrap: wrap;
            }

            .timer-display i {
                color: #f59e0b;
            }

            .timer-value {
                font-weight: 700;
                color: #d97706;
                font-family: monospace;
                font-size: 1.1rem;
            }

            .session-rating {
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .points-earned {
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.7rem;
                color: #10b981;
            }

            .stars {
                color: #fbbf24;
                font-size: 0.8rem;
            }

            .rated-text {
                font-size: 0.7rem;
                color: #10b981;
            }

            .session-card-footer {
                padding: 15px 20px;
                background: #f9fafb;
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                border-top: 1px solid #e5e7eb;
            }

            .btn-join,
            .btn-details,
            .btn-cancel,
            .btn-rate,
            .btn-book-again {
                padding: 8px 16px;
                border-radius: 40px;
                font-size: 0.75rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                cursor: pointer;
                border: none;
            }

            .btn-join {
                background: #10b981;
                color: white;
            }

            .btn-join:hover:not(.disabled) {
                background: #059669;
                transform: translateY(-2px);
                color: white;
            }

            .btn-join.disabled {
                background: #9ca3af;
                cursor: not-allowed;
                opacity: 0.6;
            }

            .btn-details {
                background: #f3f4f6;
                color: #374151;
            }

            .btn-details:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-cancel {
                background: #fee2e2;
                color: #991b1b;
            }

            .btn-cancel:hover {
                background: #fecaca;
                transform: translateY(-2px);
            }

            .btn-rate {
                background: #fef3c7;
                color: #d97706;
            }

            .btn-rate:hover {
                background: #fde68a;
                transform: translateY(-2px);
            }

            .btn-book-again {
                background: #ede9fe;
                color: #7c3aed;
            }

            .btn-book-again:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            .empty-state {
                text-align: center;
                padding: 60px 20px;
            }

            .empty-state i {
                font-size: 4rem;
                color: #c4b5fd;
                margin-bottom: 20px;
            }

            .empty-state h3 {
                font-size: 1.2rem;
                margin-bottom: 10px;
                color: #1f2937;
            }

            .empty-state p {
                color: #6b7280;
                margin-bottom: 20px;
            }

            .btn-primary {
                background: #7c3aed;
                color: white;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                background: #6d28d9;
                transform: translateY(-2px);
                color: white;
            }

            /* ==================== SIMPLE RATING STARS - FIXED ==================== */
            .rating-wrapper {
                text-align: center;
                margin: 20px 0;
                padding: 15px 0;
            }

            .rating-stars-simple {
                display: inline-flex;
                gap: 12px;
                justify-content: center;
                direction: ltr;
            }

            .star {
                font-size: 3rem;
                cursor: pointer;
                transition: all 0.2s ease;
                color: #d1d5db;
                user-select: none;
            }

            .star:hover,
            .star.active {
                color: #fbbf24 !important;
                transform: scale(1.1);
            }

            .rate-comment {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                resize: vertical;
            }

            .rate-comment:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .rate-specialist-name {
                font-size: 1rem;
                font-weight: 500;
                text-align: center;
                color: #1f2937;
                margin-bottom: 10px;
            }

            /* Modal Styles */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 9999;
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
                z-index: 10000;
                position: relative;
            }

            .modal-overlay.active .modal-container {
                transform: scale(1);
            }

            .modal-container.small {
                max-width: 400px;
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
                color: #6b7280;
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

            .btn-confirm-cancel,
            .btn-submit-rate {
                background: #ef4444;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-submit-rate {
                background: #7c3aed;
            }

            /* SweetAlert Z-Index Fix */
            .swal2-container {
                z-index: 20000 !important;
            }

            .swal2-popup {
                z-index: 20001 !important;
            }

            .text-warning {
                color: #f59e0b;
                font-size: 0.8rem;
            }

            .cancel-note {
                font-size: 0.75rem;
                color: #6b7280;
                margin-top: 10px;
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

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease forwards;
            }

            .animate-scale-in {
                animation: scaleIn 0.3s ease forwards;
            }

            @media (max-width: 992px) {
                .sessions-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .session-card-footer {
                    flex-direction: column;
                }

                .btn-join,
                .btn-details,
                .btn-cancel,
                .btn-rate,
                .btn-book-again {
                    justify-content: center;
                    width: 100%;
                }

                .sessions-tabs {
                    justify-content: center;
                }

                .tab-btn {
                    padding: 10px 16px;
                    font-size: 0.8rem;
                }

                .star {
                    font-size: 2.5rem;
                }
            }

            @media (max-width: 480px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .session-card-header {
                    flex-direction: column;
                    text-align: center;
                }

                .session-specialist {
                    flex-direction: column;
                }

                .datetime-item {
                    flex-wrap: wrap;
                }

                .star {
                    font-size: 2rem;
                }
            }

            body.rtl .tab-btn i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .free-badge-small {
                margin-left: 0;
                margin-right: 6px;
            }

            body.rtl .reward-tooltip {
                margin-left: 0;
                margin-right: 6px;
            }

            body.rtl .timer-display {
                flex-direction: row;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Tab Switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const tabId = this.dataset.tab;
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });

            // ==================== COUNTDOWN TIMER ====================
            function updateAllCountdowns() {
                const now = new Date();

                document.querySelectorAll('.countdown-timer').forEach(container => {
                    const sessionDateTime = container.dataset.sessionDatetime;
                    const joinUrl = container.dataset.joinUrl;
                    const joinText = container.dataset.joinText;
                    const joinIcon = container.dataset.joinIcon;

                    if (!sessionDateTime) return;

                    const targetTime = new Date(sessionDateTime.replace(' ', 'T'));
                    const diff = targetTime - now;
                    const minutesUntil = diff / 60000;
                    const minutesAfter = -minutesUntil;

                    const timerValue = container.querySelector('.timer-value');
                    const timerText = container.querySelector('.timer-text');
                    const sessionCard = container.closest('.session-card');
                    const joinButton = sessionCard?.querySelector('.btn-join');
                    const cancelButton = sessionCard?.querySelector('.btn-cancel');

                    if (diff <= 0) {
                        if (minutesAfter <= 60) {
                            timerValue.textContent = '00:00:00';
                            timerText.textContent = '{{ __("In Progress") }}';

                            if (joinButton) {
                                joinButton.classList.remove('disabled');
                                joinButton.style.opacity = '1';
                                joinButton.style.pointerEvents = 'auto';
                                if (joinUrl) {
                                    joinButton.href = joinUrl;
                                }
                                if (joinText) {
                                    joinButton.innerHTML = `<i class="fas ${joinIcon}"></i> ${joinText}`;
                                }
                            }
                            if (cancelButton) {
                                cancelButton.style.display = 'none';
                            }
                        } else {
                            timerValue.textContent = '00:00:00';
                            timerText.textContent = '{{ __("Ended") }}';

                            if (joinButton) {
                                joinButton.classList.add('disabled');
                                joinButton.style.opacity = '0.6';
                                joinButton.style.pointerEvents = 'none';
                            }
                            if (cancelButton) {
                                cancelButton.style.display = 'none';
                            }
                        }
                    } else {
                        const hours = Math.floor(diff / 3600000);
                        const minutes = Math.floor((diff % 3600000) / 60000);
                        const seconds = Math.floor((diff % 60000) / 1000);
                        timerValue.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        timerText.textContent = '{{ __("Starts in") }}';

                        if (minutesUntil <= 15 && joinButton) {
                            joinButton.classList.remove('disabled');
                            joinButton.style.opacity = '1';
                            joinButton.style.pointerEvents = 'auto';
                            if (joinUrl) {
                                joinButton.href = joinUrl;
                            }
                            if (joinText) {
                                joinButton.innerHTML = `<i class="fas ${joinIcon}"></i> ${joinText}`;
                            }
                        } else if (joinButton && minutesUntil > 15) {
                            joinButton.classList.add('disabled');
                            joinButton.style.opacity = '0.6';
                            joinButton.style.pointerEvents = 'none';
                        }
                    }
                });
            }

            updateAllCountdowns();
            setInterval(updateAllCountdowns, 1000);

            // ==================== CANCEL SESSION ====================
            let cancelSessionId = null;

            document.querySelectorAll('.btn-cancel').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const sessionId = this.dataset.sessionId;
                    const sessionDateTime = this.dataset.sessionDatetime;

                    if (!sessionDateTime) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: '{{ __("Invalid session data. Please refresh the page and try again.") }}',
                            confirmButtonText: '{{ __("OK") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    const targetTime = new Date(sessionDateTime.replace(' ', 'T'));
                    const now = new Date();
                    const hoursUntil = (targetTime - now) / 3600000;

                    if (hoursUntil <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Cannot Cancel") }}',
                            text: '{{ __("This session has already passed or is ongoing.") }}',
                            confirmButtonText: '{{ __("OK") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    if (hoursUntil < 24) {
                        const hoursLeft = Math.floor(hoursUntil);
                        const minutesLeft = Math.floor((hoursUntil - hoursLeft) * 60);
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Cannot Cancel") }}',
                            html: `<strong>{{ __("Sessions can only be cancelled at least 24 hours in advance.") }}</strong><br><br>{{ __("Time remaining") }}: ${hoursLeft} {{ __("hours") }} ${minutesLeft} {{ __("minutes") }}`,
                            confirmButtonText: '{{ __("OK") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    cancelSessionId = sessionId;
                    document.getElementById('cancelSessionId').value = sessionId;
                    document.getElementById('cancelModal').classList.add('active');
                });
            });

            document.getElementById('confirmCancelBtn')?.addEventListener('click', async () => {
                const sessionId = document.getElementById('cancelSessionId').value;
                const submitBtn = document.getElementById('confirmCancelBtn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnSpinner = submitBtn.querySelector('.btn-spinner');
                const modal = document.getElementById('cancelModal');

                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-block';
                submitBtn.disabled = true;

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
                        modal.classList.remove('active');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonText: '{{ __("OK") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonText: '{{ __("OK") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                } finally {
                    btnText.style.display = 'inline-block';
                    btnSpinner.style.display = 'none';
                    submitBtn.disabled = false;
                }
            });

            // ==================== SIMPLE RATING STARS - FIXED ====================
            let selectedRating = 0;
            let currentSessionId = null;

            const rateModal = document.getElementById('rateModal');
            const rateComment = document.getElementById('rateComment');
            const submitRateBtn = document.getElementById('submitRateBtn');
            const rateSpecialistName = document.getElementById('rateSpecialistName');

            // Handle star clicks and hovers
            function initSimpleStars() {
                const stars = document.querySelectorAll('.rating-stars-simple .star');

                stars.forEach(star => {
                    // Remove old listeners
                    star.removeEventListener('click', star._clickHandler);
                    star.removeEventListener('mouseenter', star._mouseEnterHandler);
                    star.removeEventListener('mouseleave', star._mouseLeaveHandler);

                    // Click handler
                    const clickHandler = function () {
                        selectedRating = parseInt(this.dataset.rating);
                        updateSimpleStars(selectedRating);
                    };

                    // Mouse enter handler
                    const mouseEnterHandler = function () {
                        const rating = parseInt(this.dataset.rating);
                        updateSimpleStarsHover(rating);
                    };

                    // Mouse leave handler
                    const mouseLeaveHandler = function () {
                        updateSimpleStars(selectedRating);
                    };

                    star.addEventListener('click', clickHandler);
                    star.addEventListener('mouseenter', mouseEnterHandler);
                    star.addEventListener('mouseleave', mouseLeaveHandler);

                    star._clickHandler = clickHandler;
                    star._mouseEnterHandler = mouseEnterHandler;
                    star._mouseLeaveHandler = mouseLeaveHandler;
                });
            }

            function updateSimpleStars(rating) {
                const stars = document.querySelectorAll('.rating-stars-simple .star');
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.textContent = '★';
                        star.classList.add('active');
                    } else {
                        star.textContent = '☆';
                        star.classList.remove('active');
                    }
                });
            }

            function updateSimpleStarsHover(rating) {
                const stars = document.querySelectorAll('.rating-stars-simple .star');
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.textContent = '★';
                    } else {
                        star.textContent = '☆';
                    }
                });
            }

            // Rate button click handler
            document.querySelectorAll('.btn-rate').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    currentSessionId = this.dataset.sessionId;
                    const specialistName = this.dataset.specialistName;

                    rateSpecialistName.innerHTML = '{{ __("Rate your session with") }} ' + specialistName;
                    document.getElementById('rateSessionId').value = currentSessionId;

                    // Reset rating
                    selectedRating = 0;
                    updateSimpleStars(0);
                    rateComment.value = '';

                    // Show modal
                    rateModal.classList.add('active');

                    // Re-initialize stars
                    setTimeout(initSimpleStars, 50);
                });
            });

            // Submit rating
            if (submitRateBtn) {
                submitRateBtn.addEventListener('click', async function () {
                    if (selectedRating === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("No Rating") }}',
                            text: '{{ __("Please select a rating before submitting.") }}',
                            confirmButtonText: '{{ __("OK") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    const sessionId = document.getElementById('rateSessionId').value;
                    const comment = rateComment.value;
                    const submitBtn = submitRateBtn;
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
                            rateModal.classList.remove('active');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error") }}',
                                text: data.message,
                                confirmButtonText: '{{ __("OK") }}',
                                confirmButtonColor: '#7c3aed'
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonText: '{{ __("OK") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                    } finally {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // Modal close handlers
            document.querySelectorAll('#rateModal .modal-close, #rateModal .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    rateModal.classList.remove('active');
                });
            });

            rateModal?.addEventListener('click', (e) => {
                if (e.target === rateModal) {
                    rateModal.classList.remove('active');
                }
            });

            // Cancel modal close handlers
            document.querySelectorAll('#cancelModal .modal-close, #cancelModal .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('cancelModal').classList.remove('active');
                });
            });

            document.getElementById('cancelModal')?.addEventListener('click', (e) => {
                if (e.target === document.getElementById('cancelModal')) {
                    document.getElementById('cancelModal').classList.remove('active');
                }
            });
        </script>
    @endpush

@endsection