{{-- resources/views/specialist/sessions/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Session Details') . ' - ' . __('Tamman'))

@section('page-title', __('Session Details'))

@section('content')
    <div class="session-details-container">
        <!-- Back Button -->
        <div class="back-nav">
            <a href="{{ route('specialist.clients.show', $session->patient_id) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Client Profile') }}
            </a>
        </div>

        <!-- Main Card -->
        <div class="details-card">
            <!-- Session Header -->
            <div class="session-header">
                <div class="session-status">
                    @if($session->status == 'scheduled')
                        <span class="status-badge scheduled"><i class="fas fa-clock"></i> {{ __('Scheduled') }}</span>
                    @elseif($session->status == 'completed')
                        <span class="status-badge completed"><i class="fas fa-check-circle"></i> {{ __('Completed') }}</span>
                    @elseif($session->status == 'cancelled')
                        <span class="status-badge cancelled"><i class="fas fa-times-circle"></i> {{ __('Cancelled') }}</span>
                    @elseif($session->status == 'ongoing')
                        <span class="status-badge ongoing"><i class="fas fa-spinner fa-pulse"></i> {{ __('Ongoing') }}</span>
                    @else
                        <span class="status-badge no-show"><i class="fas fa-user-slash"></i> {{ __('No Show') }}</span>
                    @endif
                </div>
                <div class="session-id">
                    {{ __('Session ID') }}: #{{ $session->id }}
                </div>
            </div>

            <!-- Client Info with Countdown Timer -->
            <div class="client-section">
                <div class="client-avatar">
                    @php
                        $clientImage = $session->patient->getProfileImageUrl();
                        $clientInitial = mb_substr($session->patient->name, 0, 1, 'UTF-8');
                    @endphp
                    @if($clientImage)
                        <img src="{{ $clientImage }}" alt="{{ $session->patient->name }}">
                    @else
                        <div class="avatar-placeholder">{{ $clientInitial }}</div>
                    @endif
                </div>
                <div class="client-info">
                    <h2>{{ $session->patient->name }}</h2>
                    <p><i class="fas fa-envelope"></i> {{ $session->patient->email }}</p>
                    @if($session->patient->phone)
                        <p><i class="fas fa-phone"></i> {{ $session->patient->phone }}</p>
                    @endif
                </div>

                <!-- Professional Countdown Timer Box -->
                @if($session->status == 'scheduled')
                    <div class="countdown-box" id="countdownBox">
                        <div class="countdown-inner">
                            <div class="countdown-item">
                                <div class="countdown-value" id="days">00</div>
                                <div class="countdown-label">{{ __('Days') }}</div>
                            </div>
                            <div class="countdown-separator">:</div>
                            <div class="countdown-item">
                                <div class="countdown-value" id="hours">00</div>
                                <div class="countdown-label">{{ __('Hours') }}</div>
                            </div>
                            <div class="countdown-separator">:</div>
                            <div class="countdown-item">
                                <div class="countdown-value" id="minutes">00</div>
                                <div class="countdown-label">{{ __('Mins') }}</div>
                            </div>
                            <div class="countdown-separator">:</div>
                            <div class="countdown-item">
                                <div class="countdown-value" id="seconds">00</div>
                                <div class="countdown-label">{{ __('Secs') }}</div>
                            </div>
                        </div>
                        <div class="countdown-title">
                            <i class="fas fa-hourglass-start"></i>
                            <span>{{ __('Time until session') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Session Details Grid -->
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-icon purple">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">{{ __('Date') }}</span>
                        <span
                            class="detail-value">{{ Carbon\Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y') }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon blue">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">{{ __('Time') }}</span>
                        <span class="detail-value" id="sessionTimeDisplay"></span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon green">
                        <i
                            class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">{{ __('Session Type') }}</span>
                        <span class="detail-value">{{ __(ucfirst($session->session_type)) }} {{ __('Session') }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon orange">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="detail-content">
                        <span class="detail-label">{{ __('Duration') }}</span>
                        <span class="detail-value">{{ $session->duration_minutes }} {{ __('minutes') }}</span>
                    </div>
                </div>
            </div>

            <!-- Meeting Link Section (for video/audio) -->
            @if($session->session_type != 'text' && $session->meeting_link)
                <div class="meeting-section">
                    <h3><i class="fas fa-video"></i> {{ __('Meeting Link') }}</h3>
                    <div class="meeting-link-box">
                        <input type="text" id="meetingLink" value="{{ $session->meeting_link }}" readonly>
                        <button class="btn-copy" onclick="copyMeetingLink()">
                            <i class="fas fa-copy"></i> {{ __('Copy') }}
                        </button>
                    </div>
                    @if($canJoin)
                        <a href="{{ route('specialist.sessions.join', $session->id) }}" class="btn-join-session" target="_blank">
                            <i class="fas fa-video"></i> {{ __('Join Session Now') }}
                        </a>
                    @elseif($joinTime && $session->status == 'scheduled')
                        <div class="join-notice">
                            <i class="fas fa-info-circle"></i>
                            {{ __('You can join the session starting') }}
                            {{ Carbon\Carbon::parse($joinTime)->translatedFormat('h:i A') }}
                        </div>
                    @endif
                </div>
                @if(in_array($session->status, ['scheduled', 'ongoing']))
                    <!-- Participant Status Display -->
                    <div class="participant-status">
                        <div class="status-item">
                            <i class="fas fa-user-md"></i>
                            <span>{{ __('You (Specialist)') }}</span>
                            @if($session->specialist_joined)
                                <span class="badge-joined"><i class="fas fa-check-circle"></i> {{ __('Joined') }}</span>
                            @else
                                <span class="badge-waiting"><i class="fas fa-clock"></i> {{ __('Not Joined') }}</span>
                            @endif
                        </div>
                        <div class="status-item">
                            <i class="fas fa-user"></i>
                            <span>{{ __('Patient') }}</span>
                            @if($session->patient_joined)
                                <span class="badge-joined"><i class="fas fa-check-circle"></i> {{ __('Joined') }}</span>
                            @else
                                <span class="badge-waiting"><i class="fas fa-clock"></i> {{ __('Waiting') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Security Note -->
                    <div class="security-note">
                        <i class="fas fa-shield-alt"></i>
                        <small>{{ __('This is a private session. Only you and the patient can join. Join first to become the host.') }}</small>
                    </div>
                @endif
            @endif
            <!-- Session Notes Section -->
            <div class="notes-section">
                <div class="notes-header">
                    <h3><i class="fas fa-notes-medical"></i> {{ __('Session Notes') }}</h3>
                    <a href="{{ route('specialist.session-notes.edit', $session->id) }}" class="btn-notes">
                        <i class="fas fa-pen"></i> {{ $session->notes ? __('Edit Notes') : __('Add Notes') }}
                    </a>
                </div>
                <p class="notes-description">{{ __('Add clinical notes after the session to track progress.') }}</p>
            </div>

            <!-- Text Chat Section -->
            @if($session->session_type == 'text')
                <div class="chat-section-centered">
                    <div class="chat-card-centered">
                        <div class="chat-icon-wrapper">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <h3>{{ __('Text Chat Session') }}</h3>
                        <p>{{ __('This is a text chat session. Click the button below to open the chat with your patient.') }}
                        </p>
                        <a href="{{ route('chat.index', ['user' => $session->patient_id]) }}" class="btn-chat-centered">
                            <i class="fas fa-comment-dots"></i> {{ __('Open Chat with Patient') }}
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-buttons">
                @if($canJoin && $session->session_type != 'text')
                    <a href="{{ route('specialist.sessions.join', $session->id) }}" class="btn-action btn-join" target="_blank">
                        <i class="fas fa-video"></i> {{ __('Join Session') }}
                    </a>
                @endif
                @if($canCancel)
                    <button class="btn-action btn-cancel" onclick="cancelSession({{ $session->id }})">
                        <i class="fas fa-times-circle"></i> {{ __('Cancel Session') }}
                    </button>
                @endif
            </div>
            <div class="status-buttons-container">
                @if(in_array($session->status, ['scheduled', 'ongoing']))
                    <div class="status-controls">
                        <h4>{{ __('Session Status Controls') }}</h4>
                        <div class="status-buttons">
                            @if($session->status == 'scheduled')
                                <button class="btn-status btn-start" onclick="markOngoing({{ $session->id }})">
                                    <i class="fas fa-play"></i> {{ __('Mark as Ongoing') }}
                                </button>
                            @endif
                            @if(in_array($session->status, ['scheduled', 'ongoing']))
                                <button class="btn-status btn-complete" onclick="markComplete({{ $session->id }})">
                                    <i class="fas fa-check-circle"></i> {{ __('Mark as Completed') }}
                                </button>
                                <button class="btn-status btn-no-show" onclick="markNoShow({{ $session->id }})">
                                    <i class="fas fa-user-slash"></i> {{ __('Mark as No-Show') }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .session-details-container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px;
            }

            .back-nav {
                margin-bottom: 20px;
            }

            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 18px;
                background: #f3f4f6;
                border-radius: 40px;
                color: #374151;
                text-decoration: none;
                font-size: 0.85rem;
                transition: all 0.3s ease;
            }

            .btn-back:hover {
                background: #e5e7eb;
                transform: translateX(-3px);
            }

            body.rtl .btn-back:hover {
                transform: translateX(3px);
            }

            .details-card {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            .session-header {
                padding: 20px 25px;
                background: #f9fafb;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 30px;
                font-size: 0.75rem;
                font-weight: 500;
            }

            .status-badge.scheduled {
                background: #ede9fe;
                color: #7c3aed;
            }

            .status-badge.completed {
                background: #d1fae5;
                color: #065f46;
            }

            .status-badge.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            .status-badge.ongoing {
                background: #fef3c7;
                color: #d97706;
            }

            .status-badge.no-show {
                background: #f3f4f6;
                color: #6b7280;
            }

            .session-id {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            /* Client Section */
            .client-section {
                padding: 25px;
                display: flex;
                align-items: center;
                gap: 20px;
                border-bottom: 1px solid #e5e7eb;
                flex-wrap: wrap;
            }

            .client-avatar img,
            .avatar-placeholder {
                width: 70px;
                height: 70px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                font-weight: 600;
                color: white;
            }

            .client-info {
                flex: 1;
            }

            .client-info h2 {
                font-size: 1.2rem;
                margin: 0 0 5px;
                color: #1f2937;
            }

            .client-info p {
                font-size: 0.8rem;
                color: #6b7280;
                margin: 3px 0;
            }

            .client-info i {
                width: 20px;
                color: #7c3aed;
            }

            /* Professional Countdown Timer */
            .countdown-box {
                background: linear-gradient(135deg, #1e1b4b, #2e1065);
                border-radius: 20px;
                padding: 18px 25px;
                min-width: 320px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            }

            .countdown-inner {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                margin-bottom: 10px;
            }

            .countdown-item {
                text-align: center;
                min-width: 55px;
            }

            .countdown-value {
                font-size: 2rem;
                font-weight: 700;
                color: white;
                font-family: 'Courier New', monospace;
                background: rgba(255, 255, 255, 0.1);
                padding: 8px 12px;
                border-radius: 12px;
                letter-spacing: 2px;
            }

            .countdown-item .countdown-label {
                font-size: 0.65rem;
                color: rgba(255, 255, 255, 0.6);
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-top: 6px;
                display: block;
            }

            .countdown-separator {
                font-size: 2rem;
                font-weight: 700;
                color: rgba(255, 255, 255, 0.5);
                margin-bottom: 20px;
            }

            .countdown-title {
                text-align: center;
                font-size: 0.7rem;
                color: rgba(255, 255, 255, 0.5);
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding-top: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }

            .countdown-title i {
                font-size: 0.65rem;
                color: #fbbf24;
            }

            /* Status Controls Section */
            .status-buttons-container {
                margin: 0 25px 25px 25px;
            }

            .status-controls {
                background: #f8fafc;
                border-radius: 20px;
                padding: 25px;
                border: 1px solid #e2e8f0;
            }

            .status-controls h4 {
                font-size: 0.9rem;
                margin: 0 0 15px 0;
                color: #1e293b;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .status-controls h4 i {
                color: #7c3aed;
            }

            .status-buttons {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            /* Button Styles - NO TRANSFORM, only background and shadow changes */
            .btn-status {
                padding: 10px 20px;
                border-radius: 40px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s ease;
                position: relative;
            }

            .btn-start {
                background: #3b82f6;
                color: white;
                box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
            }

            .btn-start:hover {
                background: #2563eb;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            }

            .btn-complete {
                background: #10b981;
                color: white;
                box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
            }

            .btn-complete:hover {
                background: #059669;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }

            .btn-no-show {
                background: #f59e0b;
                color: white;
                box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
            }

            .btn-no-show:hover {
                background: #d97706;
                box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            }

            .btn-status:active {
                transform: none;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            /* Loading spinner */
            .btn-status.loading {
                position: relative;
                color: transparent !important;
                pointer-events: none;
            }

            .btn-status.loading i {
                opacity: 0;
            }

            .btn-status.loading::after {
                content: '';
                position: absolute;
                width: 18px;
                height: 18px;
                top: 50%;
                left: 50%;
                margin-left: -9px;
                margin-top: -9px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: white;
                animation: spin 0.6s linear infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
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

            /* Details Grid */
            .details-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                padding: 25px;
                border-bottom: 1px solid #e5e7eb;
            }

            .detail-item {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .detail-icon {
                width: 45px;
                height: 45px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .detail-icon i {
                font-size: 1.2rem;
                color: white;
            }

            .detail-icon.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .detail-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .detail-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .detail-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .detail-content {
                display: flex;
                flex-direction: column;
            }

            .detail-label {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .detail-value {
                font-size: 0.85rem;
                font-weight: 500;
                color: #1f2937;
            }

            /* Meeting Section */
            .meeting-section,
            .notes-section {
                padding: 25px;
                border-bottom: 1px solid #e5e7eb;
            }

            .meeting-section h3,
            .notes-section h3 {
                font-size: 1rem;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1f2937;
            }

            .notes-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                margin-bottom: 10px;
            }

            .notes-description {
                font-size: 0.8rem;
                color: #6b7280;
                margin: 0;
            }

            .meeting-link-box {
                display: flex;
                gap: 10px;
                margin: 15px 0;
            }

            .meeting-link-box input {
                flex: 1;
                padding: 10px 15px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.8rem;
                background: #f9fafb;
            }

            .btn-copy {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-copy:hover {
                background: #6d28d9;
            }

            .btn-join-session {
                display: inline-block;
                background: #10b981;
                color: white;
                padding: 12px 25px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .btn-join-session:hover {
                background: #059669;
                color: white;
            }

            .join-notice {
                background: #fef3c7;
                padding: 10px 15px;
                border-radius: 12px;
                font-size: 0.75rem;
                color: #d97706;
            }

            .btn-notes {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #ede9fe;
                color: #7c3aed;
                padding: 8px 18px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.8rem;
                transition: all 0.3s ease;
            }

            .btn-notes:hover {
                background: #ddd6fe;
            }

            /* Text Chat Section */
            .chat-section-centered {
                padding: 25px;
                width: 100%;
                box-sizing: border-box;
            }

            .chat-card-centered {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                border-radius: 24px;
                padding: 35px 25px;
                text-align: center;
                max-width: 500px;
                margin: 0 auto;
                width: 100%;
                box-sizing: border-box;
            }

            .chat-icon-wrapper {
                width: 70px;
                height: 70px;
                background: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .chat-icon-wrapper i {
                font-size: 2rem;
                color: #d97706;
            }

            .chat-card-centered h3 {
                font-size: 1.2rem;
                color: #92400e;
                margin-bottom: 10px;
            }

            .chat-card-centered p {
                font-size: 0.85rem;
                color: #78350f;
                margin-bottom: 25px;
                line-height: 1.5;
            }

            .btn-chat-centered {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                background: #d97706;
                color: white;
                padding: 12px 28px;
                border-radius: 40px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .btn-chat-centered:hover {
                background: #b45309;
                transform: translateY(-2px);
                color: white;
            }

            .btn-chat-centered i:last-child {
                transition: transform 0.3s ease;
            }

            .btn-chat-centered:hover i:last-child {
                transform: translateX(5px);
            }

            /* Mobile responsive */
            @media (max-width: 768px) {
                .chat-card-centered {
                    padding: 25px 20px;
                }

                .chat-icon-wrapper {
                    width: 55px;
                    height: 55px;
                }

                .chat-icon-wrapper i {
                    font-size: 1.5rem;
                }

                .chat-card-centered h3 {
                    font-size: 1rem;
                }

                .btn-chat-centered {
                    padding: 10px 20px;
                    font-size: 0.85rem;
                }
            }

            /* Action Buttons */
            .action-buttons {
                padding: 25px;
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .btn-action {
                padding: 12px 25px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-join {
                background: #10b981;
                color: white;
            }

            .btn-join:hover {
                background: #059669;
                color: white;
            }

            .btn-cancel {
                background: #fee2e2;
                color: #991b1b;
            }

            .btn-cancel:hover {
                background: #fecaca;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .session-details-container {
                    padding: 15px;
                }

                .client-section {
                    flex-direction: column;
                    text-align: center;
                }

                .countdown-box {
                    width: 100%;
                    min-width: auto;
                }

                .countdown-inner {
                    gap: 2px;
                }

                .countdown-item {
                    min-width: 45px;
                }

                .countdown-value {
                    font-size: 1.3rem;
                    padding: 5px 8px;
                }

                .countdown-separator {
                    font-size: 1.3rem;
                    margin-bottom: 18px;
                }

                .details-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }

                .meeting-link-box {
                    flex-direction: column;
                }

                .action-buttons {
                    flex-direction: column;
                }

                .btn-action {
                    justify-content: center;
                }

                .notes-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .status-buttons {
                    flex-direction: column;
                }

                .btn-status {
                    justify-content: center;
                    width: 100%;
                }

                .status-controls {
                    padding: 20px;
                }
            }

            @media (max-width: 480px) {
                .session-header {
                    padding: 15px;
                }

                .client-section {
                    padding: 20px;
                }

                .details-grid {
                    padding: 20px;
                }

                .meeting-section,
                .notes-section {
                    padding: 20px;
                }

                .action-buttons {
                    padding: 20px;
                }

                .status-buttons-container {
                    margin: 0 20px 20px 20px;
                }

                .countdown-value {
                    font-size: 1rem;
                    padding: 4px 6px;
                }

                .countdown-item {
                    min-width: 35px;
                }

                .countdown-label {
                    font-size: 0.55rem;
                }
            }

            /* RTL Support */
            body.rtl .client-section {
                text-align: right;
            }

            body.rtl .detail-item {
                flex-direction: row;
            }

            body.rtl .notes-header {
                flex-direction: row;
            }

            @media (max-width: 768px) {
                body.rtl .notes-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Format time with AM/PM or ص/م based on locale
            const currentLocale = '{{ app()->getLocale() }}';

            function formatTime(date) {
                if (currentLocale === 'ar') {
                    let hours = date.getHours();
                    let minutes = date.getMinutes();
                    let period = hours >= 12 ? 'م' : 'ص';
                    hours = hours % 12 || 12;
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')} ${period}`;
                }
                return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            }

            // Set session time display
            const sessionDateTime = new Date('{{ $session->session_datetime }}');
            const sessionEndTime = new Date(sessionDateTime.getTime() + {{ $session->duration_minutes }} * 60000);
            document.getElementById('sessionTimeDisplay').innerHTML = `${formatTime(sessionDateTime)} - ${formatTime(sessionEndTime)}`;

            // Professional Countdown Timer
            function updateCountdown() {
                const now = new Date();
                const diff = sessionDateTime - now;

                if (diff <= 0) {
                    document.getElementById('days').textContent = '00';
                    document.getElementById('hours').textContent = '00';
                    document.getElementById('minutes').textContent = '00';
                    document.getElementById('seconds').textContent = '00';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            }

            // Update every second if session is scheduled
            @if($session->status == 'scheduled')
                updateCountdown();
                setInterval(updateCountdown, 1000);
            @endif

                function copyMeetingLink() {
                    const linkInput = document.getElementById('meetingLink');
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

            function cancelSession(sessionId) {
                Swal.fire({
                    title: '{{ __("Cancel Session") }}',
                    text: '{{ __("Are you sure you want to cancel this session?") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, cancel it") }}',
                    cancelButtonText: '{{ __("No, keep it") }}'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const response = await fetch(`/specialist/sessions/${sessionId}/cancel`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire('{{ __("Cancelled") }}', data.message, 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire('{{ __("Error") }}', data.message, 'error');
                        }
                    }
                });
            }

            // Mark session as Ongoing
            async function markOngoing(sessionId) {
                const btn = document.querySelector('.btn-start');

                // Show loading state
                btn.classList.add('loading');
                btn.disabled = true;

                try {
                    const response = await fetch(`/specialist/sessions/${sessionId}/ongoing`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Session Started") }}',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update UI
                        const statusBadge = document.querySelector('.session-status .status-text');
                        if (statusBadge) {
                            statusBadge.textContent = '{{ __("Ongoing") }}';
                        }
                        const statusDiv = document.querySelector('.session-status');
                        if (statusDiv) {
                            statusDiv.classList.remove('scheduled');
                            statusDiv.classList.add('ongoing');
                        }

                        // Hide the "Mark as Ongoing" button
                        btn.style.display = 'none';

                        // Reload after 2 seconds to refresh the page state
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                        btn.classList.remove('loading');
                        btn.disabled = false;
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                    btn.classList.remove('loading');
                    btn.disabled = false;
                }
            }

            // Mark session as Completed
            async function markComplete(sessionId) {
                const result = await Swal.fire({
                    title: '{{ __("Complete Session") }}',
                    text: '{{ __("Mark this session as completed? The patient will earn points.") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, complete") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                const btn = document.querySelector('.btn-complete');

                btn.classList.add('loading');
                btn.disabled = true;

                try {
                    const response = await fetch(`/specialist/sessions/${sessionId}/complete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("Completed") }}',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update UI
                        const statusBadge = document.querySelector('.session-status .status-text');
                        if (statusBadge) {
                            statusBadge.textContent = '{{ __("Completed") }}';
                        }
                        const statusDiv = document.querySelector('.session-status');
                        if (statusDiv) {
                            statusDiv.classList.remove('scheduled', 'ongoing');
                            statusDiv.classList.add('completed');
                        }

                        // Redirect to session notes page after 2 seconds
                        setTimeout(() => {
                            window.location.href = `/specialist/session-notes/${sessionId}`;
                        }, 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                        btn.classList.remove('loading');
                        btn.disabled = false;
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                    btn.classList.remove('loading');
                    btn.disabled = false;
                }
            }

            // Mark session as No-Show
            async function markNoShow(sessionId) {
                const result = await Swal.fire({
                    title: '{{ __("Mark as No-Show") }}',
                    text: '{{ __("This will mark the session as no-show. The patient will not earn points.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, mark as no-show") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                const btn = document.querySelector('.btn-no-show');

                btn.classList.add('loading');
                btn.disabled = true;

                try {
                    const response = await fetch(`/specialist/sessions/${sessionId}/no-show`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("No-Show Marked") }}',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update UI
                        const statusBadge = document.querySelector('.session-status .status-text');
                        if (statusBadge) {
                            statusBadge.textContent = '{{ __("No-Show") }}';
                        }
                        const statusDiv = document.querySelector('.session-status');
                        if (statusDiv) {
                            statusDiv.classList.remove('scheduled', 'ongoing');
                            statusDiv.classList.add('no-show');
                        }

                        // Hide all status control buttons
                        document.querySelector('.status-controls')?.remove();

                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                        btn.classList.remove('loading');
                        btn.disabled = false;
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                    btn.classList.remove('loading');
                    btn.disabled = false;
                }
            }
        </script>
    @endpush
@endsection