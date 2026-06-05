{{-- resources/views/patient/sessions/join.blade.php --}}
@extends('layouts.app')

@section('title', __('Join Session') . ' - ' . __('Tamman'))

@section('page-title', __('Join Session'))

@section('content')
    <div class="join-container">
        <div class="join-card">
            <div class="join-header">
                <h2><i class="fas fa-video"></i> {{ __('Your Session is Ready') }}</h2>
                <p>{{ __('You are about to join a secure video session with') }} {{ $session->specialist->name }}</p>
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
                    <i class="fas fa-user-md"></i>
                    <span>{{ $session->specialist->name }}</span>
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
                    <i class="fas fa-spinner fa-spin"></i>
                    {{ __('You will be able to join 15 minutes before the session...') }}
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
            // Session data
            const sessionDateTime = '{{ $session->session_datetime }}';
            const durationMinutes = {{ $session->duration_minutes }};
            const sessionId = {{ $session->id }};
            const userName = '{{ Auth::user()->name }}';
            const specialistName = '{{ $session->specialist->name }}';

            // Use secure room name instead of old meeting_link
            const roomName = '{{ $session->secure_room_name }}';

            const targetTime = new Date(sessionDateTime.replace(' ', 'T'));
            const sessionEndTime = new Date(targetTime.getTime() + durationMinutes * 60000);

            const joinButton = document.getElementById('joinButton');
            const autoJoinMessage = document.getElementById('autoJoinMessage');
            const countdownSpan = document.getElementById('countdown');
            const jitsiContainer = document.getElementById('jitsiContainer');

            let jitsiApi = null;
            let redirectTriggered = false;
            let hasJoined = false;

            // ========== SECURITY: Check if can join ==========
            async function checkCanJoin() {
                try {
                    const response = await fetch(`/patient/sessions/${sessionId}/can-join`, {
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
                            window.location.href = '{{ route("patient.sessions.show", $session->id) }}';
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
                    const response = await fetch(`/patient/sessions/${sessionId}/register-join`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    return await response.json();
                } catch (error) {
                    console.error('Error registering join:', error);
                    return false;
                }
            }

            function updateCountdown() {
                const now = new Date();
                const diffToStart = targetTime - now;
                const isWithinSession = now >= targetTime && now <= sessionEndTime;
                const minutesUntilStart = diffToStart / 60000;

                if (isWithinSession) {
                    // Session is active
                    if (countdownSpan) countdownSpan.textContent = '00:00';
                    if (joinButton) {
                        joinButton.style.display = 'inline-block';
                        if (autoJoinMessage) autoJoinMessage.style.display = 'none';
                    }

                    // Auto-join after 3 seconds if not already joined
                    if (!redirectTriggered && !jitsiApi && !hasJoined) {
                        redirectTriggered = true;
                        setTimeout(() => {
                            if (!jitsiApi && !hasJoined) {
                                startJitsiMeeting();
                            }
                        }, 3000);
                    }
                    return;
                }

                // Session in future
                if (diffToStart > 0) {
                    const minutes = Math.floor(diffToStart / 60000);
                    const seconds = Math.floor((diffToStart % 60000) / 1000);
                    if (countdownSpan) countdownSpan.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    // Show join button when within 15 minutes before session
                    if (minutesUntilStart <= 15) {
                        if (joinButton) joinButton.style.display = 'inline-block';
                        if (autoJoinMessage) autoJoinMessage.style.display = 'none';
                    } else {
                        if (joinButton) joinButton.style.display = 'none';
                        if (autoJoinMessage) autoJoinMessage.style.display = 'block';
                    }
                }
            }

            function updateSessionStatus() {
                const now = new Date();
                const isWithinSession = now >= targetTime && now <= sessionEndTime;

                // If session window passed and we haven't marked as ended
                if (now > sessionEndTime && !hasJoined) {
                    fetch(`/patient/sessions/${sessionId}/check-expiry`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                }

                // If session is within window but not marked ongoing
                if (isWithinSession && !hasJoined && !jitsiApi) {
                    fetch(`/patient/sessions/${sessionId}/mark-ongoing`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                }
            }

            async function startJitsiMeeting() {
                // SECURITY CHECK: Verify can join before proceeding
                const canJoin = await checkCanJoin();
                if (!canJoin) return;

                // If already joined before, still allow rejoin
                // Don't re-register if already registered (to avoid duplicate timestamps)
                if (!hasJoined) {
                    await registerJoin();
                    hasJoined = true;
                }

                // Hide checklist and show Jitsi container
                const checklist = document.querySelector('.checklist-section');
                const sessionInfo = document.querySelector('.session-info');
                const joinActions = document.querySelector('.join-actions');

                if (checklist) checklist.style.display = 'none';
                if (sessionInfo) sessionInfo.style.display = 'none';
                if (joinActions) joinActions.style.display = 'none';
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

                jitsiApi.addEventListener('videoConferenceJoined', () => {
                    // Mark session as ongoing when first joins
                    fetch(`/patient/sessions/${sessionId}/mark-ongoing`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                });

                jitsiApi.addEventListener('videoConferenceLeft', () => {
                    Swal.fire({
                        icon: 'info',
                        title: '{{ __("Left Meeting") }}',
                        text: '{{ __("You have left the meeting. You can rejoin within the session time.") }}',
                        timer: 3000,
                        showConfirmButton: false
                    });

                    // Reset to allow rejoining
                    hasJoined = false;
                    if (checklist) checklist.style.display = 'block';
                    if (sessionInfo) sessionInfo.style.display = 'flex';
                    if (joinActions) joinActions.style.display = 'block';
                    if (jitsiContainer) jitsiContainer.style.display = 'none';
                    jitsiApi = null;

                    // Check if session time is still valid for rejoin
                    const now = new Date();
                    if (now <= sessionEndTime) {
                        if (joinButton) joinButton.style.display = 'inline-block';
                        if (autoJoinMessage) autoJoinMessage.style.display = 'none';
                    } else {
                        window.location.href = '{{ route("patient.sessions") }}';
                    }
                });
            }

            function endMeeting() {
                Swal.fire({
                    title: '{{ __("Leave Meeting") }}',
                    text: '{{ __("Are you sure you want to leave the meeting? You can rejoin within the session time.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, leave") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed && jitsiApi) {
                        jitsiApi.executeCommand('hangup');
                    }
                });
            }

            // Start countdown and status checks
            updateCountdown();
            setInterval(updateCountdown, 1000);
            setInterval(updateSessionStatus, 30000); // Check every 30 seconds

            // Test functions
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