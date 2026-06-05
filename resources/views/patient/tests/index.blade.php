{{-- resources/views/patient/tests/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Psychological Assessments') . ' - ' . __('Tamman'))

@section('page-title', __('Psychological Assessments'))

@section('content')
    <div class="tests-container">
        <!-- Encouragement Banner with Rotating Messages -->
        <div class="encouragement-banner">
            <div class="animated-waves">
                <svg class="waves-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
                    <path class="wave wave1" fill="rgba(255,255,255,0.15)"
                        d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,176C672,171,768,181,864,192C960,203,1056,213,1152,208C1248,203,1344,181,1392,170.7L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
                    </path>
                    <path class="wave wave2" fill="rgba(255,255,255,0.1)"
                        d="M0,224L48,218.7C96,213,192,203,288,208C384,213,480,235,576,234.7C672,235,768,213,864,197.3C960,181,1056,171,1152,176C1248,181,1344,203,1392,213.3L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
                    </path>
                    <path class="wave wave3" fill="rgba(255,255,255,0.05)"
                        d="M0,256L48,250.7C96,245,192,235,288,229.3C384,224,480,224,576,224C672,224,768,224,864,224C960,224,1056,224,1152,224C1248,224,1344,224,1392,224L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
                    </path>
                </svg>
            </div>
            <div class="encouragement-content">
                <div class="encouragement-emoji">📋</div>
                <div class="encouragement-text">
                    <h3 id="encouragementTitle">{{ __('Understand Your Mental Health') }}</h3>
                    <p id="encouragementMessage">
                        {{ __('Take clinically-validated assessments to better understand your emotional well-being. These tests help you track your progress over time.') }}
                    </p>
                </div>
                <div class="encouragement-tips">
                    <div class="tip-bubble">📊 {{ __('Track your progress') }}</div>
                    <div class="tip-bubble">🎯 {{ __('Get insights') }}</div>
                    <div class="tip-bubble">💪 {{ __('Earn points') }}</div>
                </div>
            </div>
        </div>

        <!-- Stats Cards - Full Width Spread -->
        <div class="stats-grid-tests">
            <div class="stat-card-tests">
                <div class="stat-icon-tests"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-info-tests">
                    <h3>{{ number_format($stats['total_tests']) }}</h3>
                    <p>{{ __('Total Tests Taken') }}</p>
                </div>
            </div>
            <div class="stat-card-tests">
                <div class="stat-icon-tests"><i class="fas fa-star"></i></div>
                <div class="stat-info-tests">
                    <h3>{{ number_format($stats['total_points_earned']) }}</h3>
                    <p>{{ __('Points Earned') }}</p>
                </div>
            </div>
            <div class="stat-card-tests">
                <div class="stat-icon-tests"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-info-tests">
                    <h3>{{ $stats['tests_this_month'] }}</h3>
                    <p>{{ __('Tests This Month') }}</p>
                </div>
            </div>
        </div>

        <!-- Tests Grid -->
        <div class="tests-grid-container">
            @foreach($testsData as $type => $data)
                @php
                    $test = $data['info'];
                    $lastResult = $data['last_result'];
                    $canTake = $data['can_take'];
                    $nextAvailable = $data['next_available'];
                    $hasTaken = $data['has_taken'];
                @endphp
                <div class="test-card-large" style="border-top: 4px solid {{ $test['color'] }}">
                    <div class="test-card-header">
                        <div class="test-icon-large" style="background: {{ $test['bg'] }}; color: {{ $test['color'] }}">
                            <i class="{{ $test['icon'] }}"></i>
                        </div>
                        <div class="test-header-info">
                            <h3>{{ $test['name'] }}</h3>
                            <p>{{ app()->getLocale() === 'ar' ? $test['full_name_ar'] : $test['full_name'] }}</p>
                        </div>
                    </div>

                    <div class="test-card-body">
                        <p class="test-description">
                            {{ app()->getLocale() === 'ar' ? $test['description_ar'] : $test['description'] }}</p>
                        <div class="test-meta">
                            <span><i class="fas fa-question-circle"></i> {{ $test['questions_count'] }}
                                {{ __('questions') }}</span>
                            <span><i class="fas fa-clock"></i> {{ $test['time_minutes'] }} {{ __('minutes') }}</span>
                            <span><i class="fas fa-star"></i> {{ __('10 points') }}</span>
                        </div>

                        @if($hasTaken && $lastResult)
                            <div class="last-result-box">
                                <div class="last-score">
                                    <span class="score-label">{{ __('Last Score') }}</span>
                                    <span class="score-value">{{ $lastResult->score }}</span>
                                </div>
                                <div class="last-level {{ $lastResult->result_level }}">
                                    {{ $lastResult->getResultLevelArAttribute() }}
                                </div>
                                <div class="last-date">
                                    <i class="fas fa-calendar-alt"></i> {{ $lastResult->test_date->translatedFormat('M d, Y') }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="test-card-footer">
                        @if($canTake)
                            <a href="{{ route('patient.tests.take', $type) }}" class="btn-test-start"
                                style="background: {{ $test['color'] }}">
                                <i class="fas fa-play"></i> {{ __('Start Test') }}
                            </a>
                        @else
                            <div class="btn-test-locked" style="border-color: {{ $test['color'] }}; color: {{ $test['color'] }}">
                                <i class="fas fa-lock"></i> {{ __('Available') }}: {{ $nextAvailable->translatedFormat('M d, Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-card">
                <i class="fas fa-shield-alt"></i>
                <h4>{{ __('Clinically Validated') }}</h4>
                <p>{{ __('All assessments are clinically validated tools used by mental health professionals worldwide.') }}
                </p>
            </div>
            <div class="info-card">
                <i class="fas fa-chart-line"></i>
                <h4>{{ __('Track Progress') }}</h4>
                <p>{{ __('View your results over time and see how your mental health improves with treatment.') }}</p>
            </div>
            <div class="info-card">
                <i class="fas fa-star"></i>
                <h4>{{ __('Earn Points') }}</h4>
                <p>{{ __('Each completed test earns you 10 Tamman Points!') }}</p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .tests-container {
                max-width: 100%;
                margin: 0 auto;
            }

            /* Encouragement Banner */
            .encouragement-banner {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 24px;
                margin-bottom: 30px;
                position: relative;
                overflow: hidden;
                padding: 30px;
                color: white;
            }

            .animated-waves {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 100px;
                overflow: hidden;
                opacity: 0.4;
            }

            .waves-svg {
                position: relative;
                width: 100%;
                height: 100%;
                animation: waveFloat 8s ease-in-out infinite;
            }

            .wave {
                animation: waveMove 6s ease-in-out infinite alternate;
            }

            .wave1 {
                animation: waveMove 8s ease-in-out infinite alternate;
            }

            .wave2 {
                animation: waveMove 6s ease-in-out infinite alternate reverse;
            }

            .wave3 {
                animation: waveMove 10s ease-in-out infinite alternate;
            }

            @keyframes waveMove {
                0% {
                    transform: translateX(0) translateY(0);
                }

                100% {
                    transform: translateX(-30px) translateY(5px);
                }
            }

            @keyframes waveFloat {
                0% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-5px);
                }

                100% {
                    transform: translateY(0);
                }
            }

            .encouragement-content {
                position: relative;
                z-index: 2;
                display: flex;
                align-items: center;
                gap: 25px;
                flex-wrap: wrap;
            }

            .encouragement-emoji {
                font-size: 4rem;
                animation: bounce 2s ease-in-out infinite;
            }

            @keyframes bounce {

                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-10px);
                }
            }

            .encouragement-text {
                flex: 1;
            }

            .encouragement-text h3 {
                color: white;
                font-size: 1.3rem;
                margin-bottom: 8px;
                transition: opacity 0.3s ease;
            }

            .encouragement-text p {
                color: rgba(255, 255, 255, 0.9);
                margin: 0;
                transition: opacity 0.3s ease;
            }

            .encouragement-tips {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            .tip-bubble {
                background: rgba(255, 255, 255, 0.2);
                padding: 8px 16px;
                border-radius: 40px;
                font-size: 0.8rem;
                backdrop-filter: blur(10px);
            }

            /* Stats */
            .stats-grid-tests {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .stat-card-tests {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .stat-card-tests:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .stat-icon-tests {
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, #ede9fe, #ddd6fe);
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stat-icon-tests i {
                font-size: 1.5rem;
                color: #7c3aed;
            }

            .stat-info-tests h3 {
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-info-tests p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            /* Tests Grid */
            .tests-grid-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
                margin-bottom: 40px;
            }

            .test-card-large {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
            }

            .test-card-large:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.1);
            }

            .test-card-header {
                padding: 20px 25px;
                display: flex;
                align-items: center;
                gap: 15px;
                border-bottom: 1px solid #f3f4f6;
            }

            .test-icon-large {
                width: 55px;
                height: 55px;
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .test-icon-large i {
                font-size: 1.6rem;
            }

            .test-header-info h3 {
                font-size: 1.2rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .test-header-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            .test-card-body {
                padding: 20px 25px;
                flex: 1;
            }

            .test-description {
                font-size: 0.85rem;
                color: #4b5563;
                margin-bottom: 15px;
                line-height: 1.5;
            }

            .test-meta {
                display: flex;
                gap: 15px;
                margin-bottom: 15px;
                flex-wrap: wrap;
            }

            .test-meta span {
                font-size: 0.7rem;
                color: #6b7280;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .last-result-box {
                background: #f9fafb;
                border-radius: 12px;
                padding: 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 10px;
            }

            .last-score {
                display: flex;
                flex-direction: column;
            }

            .score-label {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .score-value {
                font-size: 1.2rem;
                font-weight: 700;
                color: #1f2937;
            }

            .last-level {
                font-size: 0.7rem;
                padding: 4px 10px;
                border-radius: 20px;
            }

            .last-level.minimal {
                background: #d1fae5;
                color: #065f46;
            }

            .last-level.mild {
                background: #fef3c7;
                color: #92400e;
            }

            .last-level.moderate {
                background: #fed7aa;
                color: #9a3412;
            }

            .last-level.moderately_severe {
                background: #fed7aa;
                color: #9a3412;
            }

            .last-level.severe {
                background: #fee2e2;
                color: #991b1b;
            }

            .last-level.none {
                background: #d1fae5;
                color: #065f46;
            }

            .last-level.subthreshold {
                background: #fef3c7;
                color: #92400e;
            }

            .last-level.low {
                background: #d1fae5;
                color: #065f46;
            }

            .last-level.high {
                background: #fee2e2;
                color: #991b1b;
            }

            .last-date {
                font-size: 0.65rem;
                color: #9ca3af;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .test-card-footer {
                padding: 16px 25px;
                border-top: 1px solid #f3f4f6;
            }

            .btn-test-start {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 12px;
                border-radius: 40px;
                color: white;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                border: none;
                width: 100%;
                cursor: pointer;
            }

            .btn-test-start:hover {
                transform: translateY(-2px);
                filter: brightness(1.05);
                color: white;
            }

            .btn-test-locked {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 12px;
                border-radius: 40px;
                background: #f3f4f6;
                border: 1px solid;
                font-size: 0.75rem;
                font-weight: 500;
                cursor: default;
            }

            /* Info Section */
            .info-section {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 25px;
                margin-top: 20px;
            }

            .info-card {
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                border-radius: 20px;
                padding: 25px;
                text-align: center;
            }

            .info-card i {
                font-size: 2rem;
                color: #7c3aed;
                margin-bottom: 15px;
                display: block;
            }

            .info-card h4 {
                font-size: 1rem;
                margin-bottom: 8px;
                color: #1f2937;
            }

            .info-card p {
                font-size: 0.8rem;
                color: #6b7280;
                margin: 0;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .stats-grid-tests {
                    grid-template-columns: repeat(3, 1fr);
                }

                .tests-grid-container {
                    grid-template-columns: 1fr;
                }

                .info-section {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 992px) {
                .encouragement-content {
                    flex-direction: column;
                    text-align: center;
                }

                .encouragement-tips {
                    justify-content: center;
                }
            }

            @media (max-width: 768px) {
                .stats-grid-tests {
                    grid-template-columns: 1fr;
                }

                .test-card-header {
                    flex-wrap: wrap;
                    justify-content: center;
                    text-align: center;
                }

                .test-meta {
                    justify-content: center;
                }

                .last-result-box {
                    flex-direction: column;
                    text-align: center;
                }

                .encouragement-emoji {
                    font-size: 3rem;
                }
            }

            /* RTL */
            body.rtl .test-meta span {
                flex-direction: row;
            }

            body.rtl .last-date i {
                margin-left: 4px;
                margin-right: 0;
            }

            body.rtl .encouragement-content {
                flex-direction: row;
            }

            @media (max-width: 992px) {
                body.rtl .encouragement-content {
                    flex-direction: column;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Rotating encouragement messages
            const encouragementMessages = [
                { title: "{{ __('Understand Your Mental Health') }}", message: "{{ __('Take clinically-validated assessments to better understand your emotional well-being. These tests help you track your progress over time.') }}" },
                { title: "{{ __('Track Your Progress') }}", message: "{{ __('Regular assessments help you see how your mental health improves over time. Each test takes only a few minutes.') }}" },
                { title: "{{ __('Earn Points') }}", message: "{{ __('Each completed test earns you 10 Tamman Points! Use them for session discounts and rewards.') }}" },
                { title: "{{ __('Share with Your Specialist') }}", message: "{{ __('Your results can be shared with your mental health professional to help them better understand your needs.') }}" }
            ];

            let messageIndex = 0;
            const titleElement = document.getElementById('encouragementTitle');
            const messageElement = document.getElementById('encouragementMessage');

            if (titleElement && messageElement) {
                setInterval(() => {
                    messageIndex = (messageIndex + 1) % encouragementMessages.length;
                    titleElement.style.opacity = '0';
                    messageElement.style.opacity = '0';
                    setTimeout(() => {
                        titleElement.textContent = encouragementMessages[messageIndex].title;
                        messageElement.textContent = encouragementMessages[messageIndex].message;
                        titleElement.style.opacity = '1';
                        messageElement.style.opacity = '1';
                    }, 300);
                }, 8000);
            }
        </script>
    @endpush

@endsection