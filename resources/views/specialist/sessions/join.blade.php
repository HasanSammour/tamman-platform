{{-- resources/views/specialist/sessions/join.blade.php --}}
@extends('layouts.app')

@section('title', __('Join Session') . ' - ' . __('Tamman'))

@section('page-title', __('Join Session'))

@section('content')
    <div class="join-container">
        <div class="join-card">
            <div class="join-header">
                <h2><i class="fas fa-video"></i> {{ __('Your Session is Ready') }}</h2>
                <p>{{ __('You are about to join a secure video session with') }} {{ $session->patient->name }}</p>
            </div>

            <!-- Pre-join Checklist -->
            <div class="checklist-section">
                <h3><i class="fas fa-check-circle"></i> {{ __('Before you join') }}</h3>
                <div class="checklist-items">
                    <div class="checklist-item">
                        <i class="fas fa-microphone"></i>
                        <span>{{ __('Check your microphone') }}</span>
                        <button class="btn-test" onclick="testMicrophone()">
                            <i class="fas fa-microphone-alt"></i> {{ __('Test') }}
                        </button>
                    </div>
                    <div class="checklist-item">
                        <i class="fas fa-video"></i>
                        <span>{{ __('Check your camera') }}</span>
                        <button class="btn-test" onclick="testCamera()">
                            <i class="fas fa-camera"></i> {{ __('Test') }}
                        </button>
                    </div>
                    <div class="checklist-item">
                        <i class="fas fa-wifi"></i>
                        <span>{{ __('Stable internet connection') }}</span>
                        <button class="btn-test" onclick="testSpeed()">
                            <i class="fas fa-tachometer-alt"></i> {{ __('Test') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Session Info -->
            <div class="session-info">
                <div class="info-item">
                    <i class="fas fa-user"></i>
                    <span>{{ $session->patient->name }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span id="sessionTimer">{{ __('Session starts in') }} <span id="countdown">--:--</span></span>
                </div>
            </div>

            <!-- Jitsi Container (hidden initially) -->
            <div class="jitsi-container" id="jitsiContainer" style="display: none;">
                <div id="jitsiMeeting" style="width: 100%; height: 500px; border-radius: 16px; overflow: hidden;"></div>
                <div class="meeting-controls">
                    <button class="btn-end-meeting" onclick="endMeeting()">
                        <i class="fas fa-phone-slash"></i> {{ __('Leave Meeting') }}
                    </button>
                </div>
            </div>

            <!-- Join Button -->
            <div class="join-actions">
                <button class="btn-join-meeting" id="joinButton" style="display: none;" onclick="startJitsiMeeting()">
                    <i class="fas fa-video"></i> {{ __('Join Meeting') }}
                </button>
                <div id="autoJoinMessage" class="auto-join-message">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('You will be redirected automatically...') }}
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .join-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
            }

            .join-card {
                background: white;
                border-radius: 32px;
                padding: 30px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }

            .join-header {
                text-align: center;
                margin-bottom: 25px;
            }

            .join-header h2 {
                font-size: 1.5rem;
                margin-bottom: 8px;
                color: #1f2937;
            }

            .join-header p {
                color: #6b7280;
            }

            .checklist-section {
                background: #f9fafb;
                border-radius: 20px;
                padding: 20px;
                margin-bottom: 25px;
            }

            .checklist-section h3 {
                font-size: 1rem;
                margin-bottom: 15px;
                color: #1f2937;
            }

            .checklist-items {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .checklist-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .checklist-item:last-child {
                border-bottom: none;
            }

            .checklist-item i {
                width: 30px;
                color: #7c3aed;
            }

            .checklist-item span {
                flex: 1;
                font-size: 0.85rem;
                color: #374151;
            }

            .btn-test {
                background: #f3f4f6;
                border: none;
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 0.7rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-test:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .session-info {
                display: flex;
                justify-content: center;
                gap: 30px;
                margin-bottom: 25px;
                flex-wrap: wrap;
            }

            .info-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.85rem;
                color: #1f2937;
            }

            .info-item i {
                color: #7c3aed;
            }

            .jitsi-container {
                margin: 20px 0;
                border-radius: 16px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
            }

            .meeting-controls {
                margin-top: 20px;
                text-align: center;
            }

            .btn-end-meeting {
                background: #ef4444;
                color: white;
                border: none;
                padding: 10px 24px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-end-meeting:hover {
                background: #dc2626;
                transform: translateY(-2px);
            }

            .join-actions {
                text-align: center;
                margin-top: 20px;
            }

            .btn-join-meeting {
                display: inline-block;
                background: #10b981;
                color: white;
                padding: 14px 30px;
                border-radius: 50px;
                text-decoration: none;
                font-size: 1rem;
                font-weight: 600;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
            }

            .btn-join-meeting:hover {
                background: #059669;
                color: white;
                transform: translateY(-2px);
            }

            .auto-join-message {
                padding: 14px;
                background: #ede9fe;
                border-radius: 50px;
                color: #7c3aed;
                font-size: 0.85rem;
            }

            .session-ended {
                text-align: center;
                padding: 40px 20px;
            }

            .session-ended i {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
                display: block;
            }

            .session-ended p {
                color: #6b7280;
                margin-bottom: 20px;
            }

            .session-ended .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f3f4f6;
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.85rem;
                color: #374151;
            }

            @media (max-width: 768px) {
                .join-card {
                    padding: 20px;
                }

                .checklist-item {
                    flex-wrap: wrap;
                }

                .btn-test {
                    width: 100%;
                    margin-top: 5px;
                }

                .jitsi-container iframe {
                    height: 350px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://meet.jit.si/external_api.js"></script>
        <script>
            const sessionStartTime = new Date('{{ $session->session_datetime }}');
            const durationMinutes = {{ $session->duration_minutes }};
            const sessionEndTime = new Date(sessionStartTime.getTime() + durationMinutes * 60000);
            const sessionId = {{ $session->id }};
            const roomName = '{{ $session->secure_room_name }}';
            const userName = '{{ Auth::user()->name }} (Specialist)';

            let jitsiApi = null;
            let hasJoined = false;

            // ========== SECURITY: Check if can join ==========
            async function checkCanJoin() {
                try {
                    const response = await fetch(`/specialist/sessions/${sessionId}/can-join`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (!data.can_join) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Cannot Join Session") }}',
                            text: data.message,
                            confirmButtonColor: '#ef4444'
                        }).then(() => {
                            window.location.href = '{{ route("specialist.sessions.show", $session->id) }}';
                        });
                        return false;
                    }

                    return true;
                } catch (error) {
                    console.error('Error checking join permission:', error);
                    return false;
                }
            }

            // ========== SECURITY: Register as joined ==========
            async function registerJoin() {
                try {
                    const response = await fetch(`/specialist/sessions/${sessionId}/register-join`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    return data.success;
                } catch (error) {
                    console.error('Error registering join:', error);
                    return false;
                }
            }

            // ========== Handle Leave Meeting ==========
            async function endMeeting() {
                const result = await Swal.fire({
                    title: '{{ __("Leave Meeting") }}',
                    text: '{{ __("Are you sure you want to leave the meeting? You can rejoin within the session time.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, leave") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (result.isConfirmed && jitsiApi) {
                    jitsiApi.executeCommand('hangup');
                    await handleLeaveMeeting();
                }
            }

            async function handleLeaveMeeting() {
                Swal.fire({
                    icon: 'info',
                    title: '{{ __("Left Meeting") }}',
                    text: '{{ __("You have left the meeting. You can rejoin within the session time.") }}',
                    timer: 3000,
                    showConfirmButton: false
                });

                // Reset state to allow rejoining
                hasJoined = false;

                // Show pre-join elements again
                const checklist = document.querySelector('.checklist-section');
                const sessionInfo = document.querySelector('.session-info');
                const joinActions = document.querySelector('.join-actions');
                const jitsiContainer = document.getElementById('jitsiContainer');

                if (checklist) checklist.style.display = 'block';
                if (sessionInfo) sessionInfo.style.display = 'flex';
                if (joinActions) joinActions.style.display = 'block';
                if (jitsiContainer) jitsiContainer.style.display = 'none';

                // Clean up Jitsi API
                if (jitsiApi) {
                    jitsiApi.dispose();
                    jitsiApi = null;
                }

                // Check if session time is still valid for rejoin
                const now = new Date();
                if (now <= sessionEndTime && now >= sessionStartTime) {
                    const joinButton = document.getElementById('joinButton');
                    const autoJoinMessage = document.getElementById('autoJoinMessage');
                    if (joinButton) joinButton.style.display = 'inline-block';
                    if (autoJoinMessage) autoJoinMessage.style.display = 'none';
                }
            }

            // ========== Start Jitsi Meeting ==========
            async function startJitsiMeeting() {
                // SECURITY CHECK: Verify can join before proceeding
                const canJoin = await checkCanJoin();
                if (!canJoin) return;

                // Register as joined (only once per session)
                if (!hasJoined) {
                    await registerJoin();
                    hasJoined = true;
                }

                // Hide pre-join elements
                const checklist = document.querySelector('.checklist-section');
                const sessionInfo = document.querySelector('.session-info');
                const joinActions = document.querySelector('.join-actions');

                if (checklist) checklist.style.display = 'none';
                if (sessionInfo) sessionInfo.style.display = 'none';
                if (joinActions) joinActions.style.display = 'none';

                // Show Jitsi container
                const jitsiContainer = document.getElementById('jitsiContainer');
                if (jitsiContainer) jitsiContainer.style.display = 'block';

                const domain = 'meet.jit.si';
                const options = {
                    roomName: roomName,
                    width: '100%',
                    height: 500,
                    parentNode: document.querySelector('#jitsiMeeting'),
                    userInfo: {
                        displayName: userName,
                        email: '{{ Auth::user()->email }}',
                    },
                    configOverwrite: {
                        startWithAudioMuted: false,
                        startWithVideoMuted: false,
                        prejoinPageEnabled: false,
                        disableDeepLinking: true,
                        disableInviteFunctions: true,
                        defaultLanguage: '{{ app()->getLocale() === "ar" ? "ar" : "en" }}',
                    },
                    interfaceConfigOverwrite: {
                        SHOW_JITSI_WATERMARK: false,
                        SHOW_WATERMARK_FOR_GUESTS: false,
                        DEFAULT_BACKGROUND: '#1f2937',
                    }
                };

                jitsiApi = new JitsiMeetExternalAPI(domain, options);

                // Show moderator badge for specialist
                setTimeout(() => {
                    showModeratorBadge();
                }, 3000);

                // Handle when user leaves via Jitsi's own UI
                jitsiApi.addEventListener('videoConferenceLeft', () => {
                    handleLeaveMeeting();
                });
            }

            function showModeratorBadge() {
                const badge = document.createElement('div');
                badge.id = 'moderatorBadge';
                badge.innerHTML = '<i class="fas fa-star"></i> {{ __("You are the host - You can manage participants") }}';
                badge.style.cssText = `
                            position: fixed;
                            bottom: 20px;
                            right: 20px;
                            background: #fbbf24;
                            color: #1f2937;
                            padding: 10px 16px;
                            border-radius: 40px;
                            font-size: 0.8rem;
                            font-weight: 600;
                            z-index: 10000;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                        `;
                document.body.appendChild(badge);

                setTimeout(() => {
                    badge.style.opacity = '0.7';
                }, 5000);
            }

            // ========== Countdown Timer ==========
            function updateCountdown() {
                const now = new Date();

                // Session is ACTIVE (started but not ended)
                if (now >= sessionStartTime && now <= sessionEndTime) {
                    const joinActions = document.querySelector('.join-actions');
                    const jitsiContainer = document.getElementById('jitsiContainer');

                    if (joinActions) joinActions.style.display = 'none';

                    if (!hasJoined && !jitsiApi) {
                        startJitsiMeeting();
                    }
                    return;
                }

                // Session hasn't started yet
                if (now < sessionStartTime) {
                    const diff = sessionStartTime - now;
                    const minutes = Math.floor(diff / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000);
                    const countdownSpan = document.getElementById('countdown');
                    if (countdownSpan) countdownSpan.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    const joinButton = document.getElementById('joinButton');
                    const autoJoinMessage = document.getElementById('autoJoinMessage');

                    if (minutes <= 15) {
                        if (joinButton) joinButton.style.display = 'inline-block';
                        if (autoJoinMessage) autoJoinMessage.style.display = 'none';
                    } else {
                        if (joinButton) joinButton.style.display = 'none';
                        if (autoJoinMessage) autoJoinMessage.style.display = 'block';
                    }
                    return;
                }

                // Session has ended
                if (now > sessionEndTime) {
                    const countdownSpan = document.getElementById('countdown');
                    if (countdownSpan) countdownSpan.textContent = '00:00';

                    Swal.fire({
                        icon: 'info',
                        title: '{{ __("Session Ended") }}',
                        text: '{{ __("This session has ended. You will be redirected.") }}',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("specialist.sessions.show", $session->id) }}';
                    });
                }
            }

            // Initialize countdown
            updateCountdown();
            setInterval(updateCountdown, 1000);

            // ========== Test Functions ==========
            function testMicrophone() {
                Swal.fire({
                    title: '{{ __("Microphone Test") }}',
                    html: '<div id="micTest">{{ __("Speak into your microphone...") }}</div>',
                    icon: 'info',
                    confirmButtonText: '{{ __("Done") }}',
                    didOpen: () => {
                        navigator.mediaDevices.getUserMedia({ audio: true })
                            .then(() => {
                                document.getElementById('micTest').innerHTML = '✅ {{ __("Microphone is working!") }}';
                            })
                            .catch(() => {
                                document.getElementById('micTest').innerHTML = '❌ {{ __("Microphone not detected. Please check your permissions.") }}';
                            });
                    }
                });
            }

            function testCamera() {
                Swal.fire({
                    title: '{{ __("Camera Test") }}',
                    html: '<video id="cameraTest" autoplay playsinline style="width:100%; border-radius:12px;"></video>',
                    confirmButtonText: '{{ __("Stop Test") }}',
                    didOpen: () => {
                        navigator.mediaDevices.getUserMedia({ video: true })
                            .then(stream => {
                                const video = document.getElementById('cameraTest');
                                video.srcObject = stream;
                                video.play();
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Camera Error") }}',
                                    text: '{{ __("Camera not detected. Please check your permissions.") }}'
                                });
                            });
                    },
                    willClose: () => {
                        const video = document.getElementById('cameraTest');
                        if (video && video.srcObject) {
                            video.srcObject.getTracks().forEach(track => track.stop());
                        }
                    }
                });
            }

            function testSpeed() {
                Swal.fire({
                    title: '{{ __("Testing Connection") }}',
                    html: '<div id="speedTest"><i class="fas fa-spinner fa-spin"></i> {{ __("Testing...") }}</div>',
                    showConfirmButton: false,
                    didOpen: () => {
                        setTimeout(() => {
                            Swal.update({
                                html: '<div id="speedTest">✅ {{ __("Your connection looks good!") }}</div>',
                                showConfirmButton: true,
                                confirmButtonText: '{{ __("OK") }}'
                            });
                        }, 2000);
                    }
                });
            }
        </script>
    @endpush
@endsection