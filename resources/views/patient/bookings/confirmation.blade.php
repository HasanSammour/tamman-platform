{{-- resources/views/patient/bookings/confirmation.blade.php --}}
@extends('layouts.app')

@section('title', __('Booking Confirmation') . ' - ' . __('Tamman'))

@section('page-title', __('Session Booked Successfully!'))

@section('content')
    <div class="confirmation-container">
        <div class="confirmation-card animate-scale-in">
            <!-- Success Icon with Animated Checkmark -->
            <div class="success-animation">
                <div class="success-circle">
                    <div class="checkmark-container">
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" />
                            <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                        </svg>
                    </div>
                </div>
            </div>

            <h2 class="success-title">{{ __('Your session has been booked!') }}</h2>
            <p class="confirmation-message">{{ __('A confirmation has been sent to your email.') }}</p>

            <!-- Session Details Card -->
            <div class="session-details-card">
                <div class="card-header-icon">
                    <i class="fas fa-calendar-check"></i>
                    <h3>{{ __('Session Details') }}</h3>
                </div>

                <div class="details-grid">
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-user-md"></i>
                            <span>{{ __('Specialist') }}</span>
                        </div>
                        <div class="detail-value">
                            <strong>{{ $session->specialist->name }}</strong>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-stethoscope"></i>
                            <span>{{ __('Specialization') }}</span>
                        </div>
                        <div class="detail-value">
                            {{ $session->specialist->specialistProfile->specialization ?? __('Psychologist') }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ __('Date') }}</span>
                        </div>
                        <div class="detail-value">
                            {{ Carbon\Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y') }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-clock"></i>
                            <span>{{ __('Time') }}</span>
                        </div>
                        <div class="detail-value">
                            {{ Carbon\Carbon::parse($session->session_datetime)->format('h:i A') }} -
                            {{ Carbon\Carbon::parse($session->session_datetime)->addMinutes($session->duration_minutes)->format('h:i A') }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i
                                class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                            <span>{{ __('Session Type') }}</span>
                        </div>
                        <div class="detail-value">
                            <span class="session-type-badge {{ $session->session_type }}">
                                {{ __(ucfirst($session->session_type)) }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-hourglass-half"></i>
                            <span>{{ __('Duration') }}</span>
                        </div>
                        <div class="detail-value">
                            {{ $session->duration_minutes }} {{ __('minutes') }}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-credit-card"></i>
                            <span>{{ __('Payment Method') }}</span>
                        </div>
                        <div class="detail-value">
                            @if($session->is_free)
                                <span class="payment-badge free">
                                    <i class="fas fa-gift"></i> {{ __('Free Session (Reward)') }}
                                    @if($freeRewardName)
                                        @php
                                            $displayRewardName = $freeRewardName;
                                            if (is_string($freeRewardName) && (str_starts_with($freeRewardName, '{') || str_contains($freeRewardName, '"en"'))) {
                                                try {
                                                    $decoded = json_decode($freeRewardName, true);
                                                    if (is_array($decoded)) {
                                                        $locale = app()->getLocale();
                                                        $displayRewardName = $decoded[$locale] ?? $decoded['en'] ?? $freeRewardName;
                                                    }
                                                } catch (\Exception $e) {
                                                }
                                            }
                                        @endphp
                                        <small class="reward-name">({{ $displayRewardName }})</small>
                                    @endif
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
                        </div>
                    </div>

                    @if($session->is_free)
                        <div class="detail-row highlight">
                            <div class="detail-label">
                                <i class="fas fa-star"></i>
                                <span>{{ __('Free Session') }}</span>
                            </div>
                            <div class="detail-value">
                                <span class="free-badge">
                                    <i class="fas fa-check-circle"></i> {{ __('Reward Redeemed Successfully') }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Meeting Link Card (for video/audio sessions only) -->
            @if($session->session_type != 'text' && $session->meeting_link)
                <div class="meeting-card">
                    <div class="card-header-icon">
                        <i class="fas fa-video"></i>
                        <h3>{{ __('Meeting Link') }}</h3>
                    </div>
                    <p class="meeting-info">{{ __('Join your session using the link below at the scheduled time.') }}</p>
                    <div class="meeting-link-wrapper">
                        <input type="text" id="meetingLink" value="{{ $session->meeting_link }}" readonly>
                        <button class="btn-copy-link" onclick="copyMeetingLink()">
                            <i class="fas fa-copy"></i> {{ __('Copy') }}
                        </button>
                    </div>
                    <div class="meeting-note">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ __('You can join the session 15 minutes before the scheduled time.') }}</span>
                    </div>
                </div>
            @endif

            <!-- Text Chat Note -->
            @if($session->session_type == 'text')
                <div class="text-chat-card">
                    <div class="card-header-icon">
                        <i class="fas fa-comment-dots"></i>
                        <h3>{{ __('Text Chat Session') }}</h3>
                    </div>
                    <p>{{ __('Your text chat session will be available in the "Messages" section at the scheduled time.') }}</p>
                    <a href="{{ route('chat.index', ['user' => $session->specialist_id]) }}" class="btn-go-to-chat">
                        <i class="fas fa-comments"></i> {{ __('Go to Messages') }}
                    </a>
                </div>
            @endif

            <!-- Session Notes (if any) -->
            @if($session->notes)
                <div class="notes-card">
                    <div class="card-header-icon">
                        <i class="fas fa-pen"></i>
                        <h3>{{ __('Session Notes') }}</h3>
                    </div>
                    <p class="notes-content">{{ $session->notes }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('patient.dashboard') }}" class="btn-dashboard">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Go to Dashboard') }}
                </a>
                <a href="{{ route('patient.sessions') }}" class="btn-sessions">
                    <i class="fas fa-calendar-alt"></i> {{ __('View My Sessions') }}
                </a>
            </div>

            <!-- Reminder Card -->
            <div class="reminder-card">
                <div class="card-header-icon">
                    <i class="fas fa-bell"></i>
                    <h4>{{ __('Important Reminders') }}</h4>
                </div>
                <ul class="reminder-list">
                    <li><i class="fas fa-clock"></i> {{ __('Arrive 5 minutes before your session') }}</li>
                    <li><i class="fas fa-microphone"></i> {{ __('Test your microphone and camera before joining') }}</li>
                    <li><i class="fas fa-wifi"></i> {{ __('Ensure you have a stable internet connection') }}</li>
                    <li><i class="fas fa-envelope"></i>
                        {{ __('You will receive email and SMS reminders before your session') }}</li>
                    @if($session->session_type == 'text')
                        <li><i class="fas fa-comment-dots"></i> {{ __('Text chat sessions are private and secure') }}</li>
                    @endif
                </ul>
            </div>

            <!-- Cancellation Policy -->
            <div class="policy-card">
                <div class="card-header-icon">
                    <i class="fas fa-info-circle"></i>
                    <h4>{{ __('Cancellation Policy') }}</h4>
                </div>
                <p>{{ __('You can cancel this session up to 24 hours before the scheduled time.') }}</p>
                @if($canCancel)
                    <button class="btn-cancel-session" id="cancelSessionBtn" data-session-id="{{ $session->id }}"
                        data-session-time="{{ $session->session_datetime }}">
                        <i class="fas fa-times-circle"></i> {{ __('Cancel Session') }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .confirmation-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 16px;
            }

            .confirmation-card {
                background: white;
                border-radius: 28px;
                padding: 28px 24px;
                text-align: center;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
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

            .animate-scale-in {
                animation: scaleIn 0.5s ease;
            }

            /* Success Animation */
            .success-animation {
                margin-bottom: 20px;
            }

            .success-circle {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #10b981, #059669);
                border-radius: 50%;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
            }

            .checkmark-container {
                width: 50px;
                height: 50px;
            }

            .checkmark {
                width: 100%;
                height: 100%;
            }

            .checkmark-circle {
                stroke: white;
                stroke-width: 3;
                stroke-dasharray: 166;
                stroke-dashoffset: 166;
                animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
            }

            .checkmark-check {
                stroke: white;
                stroke-width: 3;
                stroke-dasharray: 48;
                stroke-dashoffset: 48;
                animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.4s forwards;
            }

            @keyframes stroke {
                100% {
                    stroke-dashoffset: 0;
                }
            }

            .success-title {
                font-size: 1.4rem;
                color: #1f2937;
                margin-bottom: 6px;
            }

            .confirmation-message {
                color: #6b7280;
                font-size: 0.85rem;
                margin-bottom: 24px;
            }

            /* Card Header Icon */
            .card-header-icon {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 16px;
                padding-bottom: 10px;
                border-bottom: 2px solid #f3f4f6;
            }

            .card-header-icon i {
                width: 28px;
                height: 28px;
                background: #ede9fe;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #7c3aed;
                font-size: 0.85rem;
            }

            .card-header-icon h3,
            .card-header-icon h4 {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 600;
                color: #1f2937;
            }

            /* Session Details Card */
            .session-details-card {
                background: #f9fafb;
                border-radius: 20px;
                padding: 20px;
                text-align: left;
                margin-bottom: 20px;
            }

            .details-grid {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .detail-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid #e5e7eb;
            }

            .detail-row:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }

            .detail-row.highlight {
                background: #d1fae5;
                margin: -6px -10px -6px -10px;
                padding: 10px;
                border-radius: 12px;
                border-bottom: none;
            }

            .detail-label {
                display: flex;
                align-items: center;
                gap: 6px;
                color: #6b7280;
                font-size: 0.75rem;
                min-width: 100px;
            }

            .detail-label i {
                width: 18px;
                font-size: 0.75rem;
                color: #7c3aed;
            }

            .detail-value {
                font-weight: 500;
                color: #1f2937;
                font-size: 0.8rem;
                text-align: right;
                word-break: break-word;
            }

            /* Badges */
            .session-type-badge {
                display: inline-block;
                padding: 3px 10px;
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
                gap: 5px;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
                white-space: nowrap;
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

            .free-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                background: #10b981;
                color: white;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
            }

            .reward-name {
                font-size: 0.6rem;
                opacity: 0.8;
                margin-left: 3px;
            }

            /* Meeting Card */
            .meeting-card {
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                border-radius: 20px;
                padding: 20px;
                text-align: left;
                margin-bottom: 20px;
            }

            .meeting-info {
                font-size: 0.8rem;
                color: #6b7280;
                margin-bottom: 14px;
            }

            .meeting-link-wrapper {
                display: flex;
                gap: 10px;
                margin-bottom: 14px;
                flex-wrap: wrap;
            }

            .meeting-link-wrapper input {
                flex: 1;
                padding: 10px 14px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.75rem;
                color: #1f2937;
                min-width: 0;
            }

            .meeting-link-wrapper input:focus {
                outline: none;
                border-color: #7c3aed;
            }

            .btn-copy-link {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 0 18px;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.75rem;
                white-space: nowrap;
            }

            .btn-copy-link:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .meeting-note {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.7rem;
                color: #f59e0b;
                background: #fef3c7;
                padding: 8px 12px;
                border-radius: 12px;
                flex-wrap: wrap;
            }

            /* Text Chat Card */
            .text-chat-card {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                border-radius: 20px;
                padding: 20px;
                text-align: center;
                margin-bottom: 20px;
            }

            .text-chat-card p {
                font-size: 0.8rem;
                color: #92400e;
                margin-bottom: 14px;
            }

            .btn-go-to-chat {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #d97706;
                color: white;
                padding: 8px 20px;
                border-radius: 40px;
                text-decoration: none;
                transition: all 0.3s ease;
                font-size: 0.8rem;
            }

            .btn-go-to-chat:hover {
                background: #b45309;
                transform: translateY(-2px);
                color: white;
            }

            /* Notes Card */
            .notes-card {
                background: #fef2f2;
                border-radius: 20px;
                padding: 16px;
                text-align: left;
                margin-bottom: 20px;
            }

            .notes-card .card-header-icon i {
                background: #fee2e2;
                color: #ef4444;
            }

            .notes-content {
                font-size: 0.8rem;
                color: #991b1b;
                margin: 0;
                line-height: 1.5;
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                justify-content: center;
                gap: 12px;
                flex-wrap: wrap;
                margin-bottom: 24px;
            }

            .btn-dashboard,
            .btn-sessions {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.8rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .btn-dashboard {
                background: #f3f4f6;
                color: #374151;
            }

            .btn-dashboard:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-sessions {
                background: #ede9fe;
                color: #7c3aed;
            }

            .btn-sessions:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            /* Reminder Card */
            .reminder-card {
                background: #f0fdf4;
                border-radius: 20px;
                padding: 16px;
                text-align: left;
                margin-bottom: 16px;
            }

            .reminder-card .card-header-icon i {
                background: #d1fae5;
                color: #10b981;
            }

            .reminder-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .reminder-list li {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.75rem;
                color: #065f46;
                padding: 6px 0;
                flex-wrap: wrap;
            }

            .reminder-list li i {
                width: 18px;
                font-size: 0.7rem;
                color: #10b981;
            }

            /* Policy Card */
            .policy-card {
                background: #fefce8;
                border-radius: 20px;
                padding: 16px;
                text-align: left;
            }

            .policy-card .card-header-icon i {
                background: #fef08a;
                color: #ca8a04;
            }

            .policy-card p {
                font-size: 0.75rem;
                color: #713f12;
                margin-bottom: 14px;
            }

            .btn-cancel-session {
                background: #fee2e2;
                color: #991b1b;
                border: none;
                padding: 6px 16px;
                border-radius: 40px;
                font-size: 0.7rem;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .btn-cancel-session:hover {
                background: #fecaca;
                transform: translateY(-2px);
            }

            /* Mobile Responsive */
            @media (max-width: 640px) {
                .confirmation-container {
                    padding: 12px;
                }

                .confirmation-card {
                    padding: 20px 16px;
                    border-radius: 24px;
                }

                .success-circle {
                    width: 65px;
                    height: 65px;
                }

                .checkmark-container {
                    width: 40px;
                    height: 40px;
                }

                .success-title {
                    font-size: 1.2rem;
                }

                .confirmation-message {
                    font-size: 0.75rem;
                }

                .session-details-card {
                    padding: 14px;
                }

                .detail-row {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 4px;
                }

                .detail-label {
                    min-width: auto;
                }

                .detail-value {
                    text-align: left;
                    width: 100%;
                }

                .meeting-link-wrapper {
                    flex-direction: column;
                }

                .btn-copy-link {
                    width: 100%;
                    padding: 8px;
                    justify-content: center;
                }

                .action-buttons {
                    flex-direction: column;
                }

                .btn-dashboard,
                .btn-sessions {
                    justify-content: center;
                    width: 100%;
                }

                .reminder-list li {
                    flex-direction: row;
                }

                .payment-badge {
                    white-space: normal;
                    flex-wrap: wrap;
                }
            }

            /* Small Mobile */
            @media (max-width: 480px) {
                .confirmation-card {
                    padding: 16px 12px;
                }

                .card-header-icon h3,
                .card-header-icon h4 {
                    font-size: 0.85rem;
                }

                .detail-label {
                    font-size: 0.7rem;
                }

                .detail-value {
                    font-size: 0.7rem;
                }

                .session-type-badge,
                .payment-badge {
                    font-size: 0.65rem;
                    padding: 2px 8px;
                }

                .meeting-info {
                    font-size: 0.7rem;
                }

                .meeting-link-wrapper input {
                    font-size: 0.65rem;
                    padding: 8px 12px;
                }

                .meeting-note {
                    font-size: 0.65rem;
                }

                .text-chat-card p {
                    font-size: 0.7rem;
                }

                .btn-go-to-chat {
                    font-size: 0.7rem;
                    padding: 6px 16px;
                }

                .notes-content {
                    font-size: 0.7rem;
                }

                .reminder-list li {
                    font-size: 0.65rem;
                }

                .policy-card p {
                    font-size: 0.65rem;
                }

                .btn-cancel-session {
                    font-size: 0.65rem;
                }
            }

            /* RTL Support */
            body.rtl .card-header-icon {
                flex-direction: row;
            }

            body.rtl .detail-label {
                flex-direction: row;
            }

            body.rtl .meeting-note {
                flex-direction: row;
            }

            body.rtl .reminder-list li {
                flex-direction: row;
            }

            body.rtl .btn-cancel-session {
                flex-direction: row;
            }

            body.rtl .detail-row {
                text-align: right;
            }

            body.rtl .detail-value {
                text-align: left;
            }

            @media (max-width: 640px) {
                body.rtl .detail-row {
                    align-items: flex-start;
                }

                body.rtl .detail-value {
                    text-align: right;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
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
                        showConfirmButton: false,
                        background: '#fff',
                        color: '#1f2937'
                    });
                }
            }

            // Cancel Session Button
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
        </script>
    @endpush

@endsection