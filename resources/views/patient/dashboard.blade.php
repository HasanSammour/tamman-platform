{{-- resources/views/patient/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', __('Dashboard') . ' - ' . __('Tamman'))

@section('page-title', __('Patient Dashboard'))

@section('content')
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h2>{{ __('Welcome back') }}, {{ $user->name }}!</h2>
                <p>{{ __('How are you feeling today?') }}</p>
            </div>
            <div class="quick-actions">
                <a href="{{ route('patient.mood-tracker') }}" class="quick-action-btn">
                    <i class="fas fa-smile"></i>
                    <span>{{ __('Track Mood') }}</span>
                </a>
                <a href="{{ route('patient.tests') }}" class="quick-action-btn">
                    <i class="fas fa-clipboard-list"></i>
                    <span>{{ __('Take Test') }}</span>
                </a>
                <a href="{{ route('specialists.index') }}" class="quick-action-btn">
                    <i class="fas fa-calendar-plus"></i>
                    <span>{{ __('Book Session') }}</span>
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_points_earned']) }}</h3>
                    <p>{{ __('Points Earned') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['streak_days'] }}</h3>
                    <p>{{ __('Day Streak') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-smile"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_mood_entries']) }}</h3>
                    <p>{{ __('Mood Entries') }}</p>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Row 1: Mood Trends Chart - Full Width -->
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> {{ __('Mood Trends') }}</h3>
                    <a href="{{ route('patient.mood-tracker') }}" class="view-all">{{ __('View Details') }}</a>
                </div>
                <div class="card-body chart-wrapper">
                    @if($moodValues->count() > 0)
                        <div id="moodChart" class="apex-chart"></div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-chart-line"></i>
                            <p>{{ __('No mood data yet') }}</p>
                            <a href="{{ route('patient.mood-tracker') }}" class="btn-primary-sm">{{ __('Track Your Mood') }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Row 2: Upcoming Sessions & Recent Sessions (Side by Side) -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> {{ __('Upcoming Sessions') }}</h3>
                    <a href="{{ route('patient.sessions') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($upcomingSessions->count() > 0)
                        @foreach($upcomingSessions as $session)
                            @php
                                $specialistImage = $session->specialist->getProfileImageUrl();
                                $specialistInitial = mb_substr($session->specialist->name, 0, 1, 'UTF-8');
                            @endphp
                            <div class="session-item">
                                <div class="session-avatar">
                                    @if($specialistImage)
                                        <img src="{{ $specialistImage }}" alt="{{ $session->specialist->name }}">
                                    @else
                                        <div class="avatar-placeholder">{{ $specialistInitial }}</div>
                                    @endif
                                </div>
                                <div class="session-info">
                                    <h4>{{ $session->specialist->name }}</h4>
                                    <p><i class="fas fa-clock"></i> {{ $session->session_datetime->translatedFormat('h:i A') }}</p>
                                    <p><i
                                            class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                                        {{ ucfirst(__($session->session_type)) }} {{ __('Session') }}
                                    </p>
                                </div>
                                <div class="session-action">
                                    <a href="{{ route('patient.sessions.join', $session->id) }}"
                                        class="btn-join">{{ __('Join') }}</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-week"></i>
                            <p>{{ __('No upcoming sessions') }}</p>
                            <a href="{{ route('specialists.index') }}" class="btn-primary-sm">{{ __('Book a Session') }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> {{ __('Recent Sessions') }}</h3>
                    <a href="{{ route('patient.sessions') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($recentSessions->count() > 0)
                        @foreach($recentSessions as $session)
                            @php
                                $specialistImage = $session->specialist->getProfileImageUrl();
                                $specialistInitial = mb_substr($session->specialist->name, 0, 1, 'UTF-8');
                            @endphp
                            <div class="recent-session-item">
                                <div class="session-avatar small">
                                    @if($specialistImage)
                                        <img src="{{ $specialistImage }}" alt="{{ $session->specialist->name }}">
                                    @else
                                        <div class="avatar-placeholder small">{{ $specialistInitial }}</div>
                                    @endif
                                </div>
                                <div class="session-details">
                                    <h4>{{ $session->specialist->name }}</h4>
                                    <p>{{ $session->session_datetime->translatedFormat('M d, Y - h:i A') }}</p>
                                </div>
                                <div class="session-rating">
                                    @if($session->review)
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $session->review->rating)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    @else
                                        <a href="{{ route('patient.sessions') }}" class="btn-rate" data-session-id="{{ $session->id }}">{{ __('Rate') }}</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <p>{{ __('No session history') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Row 3: Psychological Assessments (Full Width) -->
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list"></i> {{ __('Mental Health Assessments') }}</h3>
                    <a href="{{ route('patient.tests') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body tests-body">
                    @php
                        $testInfo = [
                            'phq9' => ['name' => 'PHQ-9', 'full_name' => 'Depression', 'full_name_ar' => 'الاكتئاب', 'icon' => 'fas fa-heartbeat', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
                            'gad7' => ['name' => 'GAD-7', 'full_name' => 'Anxiety', 'full_name_ar' => 'القلق', 'icon' => 'fas fa-brain', 'color' => '#10b981', 'bg' => '#d1fae5'],
                            'pcl5' => ['name' => 'PCL-5', 'full_name' => 'PTSD', 'full_name_ar' => 'اضطراب ما بعد الصدمة', 'icon' => 'fas fa-shield-alt', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
                            'isi' => ['name' => 'ISI', 'full_name' => 'Insomnia', 'full_name_ar' => 'الأرق', 'icon' => 'fas fa-moon', 'color' => '#ef4444', 'bg' => '#fee2e2'],
                            'pss' => ['name' => 'PSS', 'full_name' => 'Stress', 'full_name_ar' => 'الإجهاد', 'icon' => 'fas fa-tachometer-alt', 'color' => '#ec4899', 'bg' => '#fce7f3'],
                            'cis' => ['name' => 'CIS', 'full_name' => 'Functioning', 'full_name_ar' => 'الأداء الوظيفي', 'icon' => 'fas fa-chart-bar', 'color' => '#06b6d4', 'bg' => '#cffafe']
                        ];
                    @endphp

                    <div class="tests-grid">
                        @foreach($testsData as $type => $data)
                            @php
                                $info = $testInfo[$type];
                                $canTake = $data['can_take'];
                                $lastTest = $data['last_test'];
                                $nextDate = $data['next_available_date'];
                                $hasTaken = $data['has_taken_before'];
                                $lastScore = $data['last_score'];
                                $lastLevel = $data['last_level'];
                                $lastDate = $data['last_date'];
                            @endphp
                            <div class="test-card">
                                <div class="test-card-header" style="background: {{ $info['bg'] }};">
                                    <div class="test-icon" style="background: {{ $info['color'] }};">
                                        <i class="{{ $info['icon'] }}" style="color: white;"></i>
                                    </div>
                                    <div class="test-title">
                                        <h4>{{ $info['name'] }}</h4>
                                        <p>{{ app()->getLocale() === 'ar' ? $info['full_name_ar'] : $info['full_name'] }}</p>
                                    </div>
                                </div>
                                <div class="test-card-body">
                                    @if($hasTaken)
                                        <div class="last-result">
                                            <div class="result-score">
                                                <span class="score-label">{{ __('Last Score') }}</span>
                                                <span class="score-value">{{ $lastScore }}</span>
                                            </div>
                                            <div class="result-level {{ $lastLevel }}">
                                                {{ $lastTest ? $lastTest->getResultLevelArAttribute() : '' }}
                                            </div>
                                            <div class="result-date">
                                                <i class="fas fa-calendar-alt"></i>
                                                {{ $lastDate ? $lastDate->translatedFormat('M d, Y') : '' }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="no-result">
                                            <i class="fas fa-clipboard-list"></i>
                                            <p>{{ __('Not taken yet') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="test-card-footer">
                                    @if($canTake)
                                        <a href="{{ route('patient.tests.take', $type) }}" class="btn-test-take">
                                            <i class="fas fa-play"></i> {{ __('Take Test') }}
                                        </a>
                                    @else
                                        <div class="btn-test-disabled">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ __('Available') }}: {{ $nextDate->translatedFormat('M d, Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Row 4: Tamman Points & Notifications (Side by Side) -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> {{ __('Tamman Points') }}</h3>
                    <a href="{{ route('patient.rewards') }}" class="view-all">{{ __('Redeem') }}</a>
                </div>
                <div class="card-body">
                    <div class="points-display">
                        <div class="points-balance">
                            <span class="points-number">{{ number_format($totalPoints) }}</span>
                            <span class="points-label">{{ __('Total Points') }}</span>
                        </div>
                        <div class="points-progress">
                            <div class="progress-label">
                                <span>{{ __('Next Reward') }}</span>
                                <span>{{ number_format($totalPoints) }} / 1000</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ min(($totalPoints / 1000) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($recentPoints->count() > 0)
                        <div class="recent-points">
                            <h4>{{ __('Recent Activity') }}</h4>
                            @foreach($recentPoints as $point)
                                <div class="point-item">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>{{ $point->translated_description ?? ucfirst(str_replace('_', ' ', $point->source)) }}</span>
                                    <strong>+{{ $point->points }}</strong>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> {{ __('Notifications') }}</h3>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <div class="notification-item {{ $notification->is_read ? '' : 'unread' }}">
                                <div class="notification-icon">
                                    <i
                                        class="fas {{ $notification->type == 'session_reminder' ? 'fa-calendar' : ($notification->type == 'points_earned' ? 'fa-star' : 'fa-bell') }}"></i>
                                </div>
                                <div class="notification-content">
                                    <p>{{ __($notification->message) }}</p>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <p>{{ __('No notifications') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Row 5: Referral Program (Full Width at the end) -->
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3><i class="fas fa-gift"></i> {{ __('Referral Program') }}</h3>
                </div>
                <div class="card-body">
                    <div class="referral-code">
                        <p>{{ __('Share your code and earn 100 points per referral!') }}</p>
                        <div class="code-box">
                            <span id="referralCode">{{ $referralCode }}</span>
                            <button onclick="copyReferralCode()" class="btn-copy">
                                <i class="fas fa-copy"></i> {{ __('Copy') }}
                            </button>
                        </div>
                        <div class="referral-stats">
                            <span><i class="fas fa-users"></i> {{ $referralCount }} {{ __('friends joined') }}</span>
                            <span><i class="fas fa-star"></i> {{ $referralCount * 100 }} {{ __('points earned') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .dashboard-container {
                max-width: 100%;
                margin: 0 auto;
            }

            .welcome-section {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 24px;
                padding: 30px;
                margin-bottom: 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
            }

            .welcome-text h2 {
                color: white;
                font-size: 1.5rem;
                margin-bottom: 5px;
            }

            .welcome-text p {
                color: rgba(255, 255, 255, 0.8);
            }

            .quick-actions {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .quick-action-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                padding: 12px 20px;
                border-radius: 40px;
                color: white;
                display: flex;
                align-items: center;
                gap: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                font-size: 0.875rem;
            }

            .quick-action-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
                color: white;
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
                flex-shrink: 0;
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

            .dashboard-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }

            .dashboard-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .dashboard-card.full-width {
                grid-column: span 2;
            }

            .card-header {
                padding: 20px 25px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
                flex-shrink: 0;
            }

            .card-header h3 {
                font-size: 1.1rem;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 10px;
                color: #1f2937;
            }

            .view-all {
                font-size: 0.75rem;
                color: #7c3aed;
                text-decoration: none;
            }

            .card-body {
                padding: 20px 25px;
                flex: 1;
            }

            /* Chart Styles */
            .chart-wrapper {
                position: relative;
                width: 100%;
                padding: 0;
            }

            .apex-chart {
                width: 100%;
                min-height: 380px;
            }

            /* Session Items */
            .session-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 15px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .session-item:last-child {
                border-bottom: none;
            }

            .session-avatar {
                width: 50px;
                height: 50px;
                flex-shrink: 0;
            }

            .session-avatar img {
                width: 100%;
                height: 100%;
                border-radius: 12px;
                object-fit: cover;
            }

            .avatar-placeholder {
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1.2rem;
            }

            .avatar-placeholder.small {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .session-avatar.small {
                width: 40px;
                height: 40px;
                flex-shrink: 0;
            }

            .session-info {
                flex: 1;
            }

            .session-info h4 {
                font-size: 0.9rem;
                margin-bottom: 5px;
                color: #1f2937;
            }

            .session-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                margin-right: 10px;
            }

            .btn-join {
                padding: 6px 16px;
                background: linear-gradient(135deg, #10b981, #059669);
                color: white;
                border-radius: 20px;
                font-size: 0.7rem;
                text-decoration: none;
                transition: all 0.3s ease;
                white-space: nowrap;
            }

            .btn-join:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
                color: white;
            }

            .empty-state {
                text-align: center;
                padding: 40px 20px;
            }

            .empty-state i {
                font-size: 3rem;
                color: #c4b5fd;
                margin-bottom: 15px;
            }

            .empty-state p {
                color: #6b7280;
                margin-bottom: 15px;
            }

            .btn-primary-sm {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 8px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.8rem;
                display: inline-block;
                transition: all 0.3s ease;
            }

            .btn-primary-sm:hover {
                color: white;
                transform: translateY(-2px);
            }

            /* Tests Grid */
            .tests-body {
                padding: 25px;
            }

            .tests-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }

            .test-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                border: 1px solid #f0f0f0;
            }

            .test-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            }

            .test-card-header {
                padding: 16px;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .test-icon {
                width: 48px;
                height: 48px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .test-icon i {
                font-size: 1.3rem;
            }

            .test-title h4 {
                font-size: 1rem;
                font-weight: 700;
                margin: 0 0 2px 0;
                color: #1f2937;
            }

            .test-title p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .test-card-body {
                padding: 16px;
                min-height: 100px;
            }

            .last-result {
                text-align: center;
            }

            .result-score {
                display: flex;
                justify-content: space-between;
                align-items: baseline;
                margin-bottom: 8px;
            }

            .score-label {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .score-value {
                font-size: 1.5rem;
                font-weight: 800;
                color: #1f2937;
            }

            .result-level {
                font-size: 0.7rem;
                padding: 4px 10px;
                border-radius: 20px;
                display: inline-block;
                margin-bottom: 8px;
            }

            .result-level.minimal {
                background: #d1fae5;
                color: #065f46;
            }

            .result-level.mild {
                background: #fef3c7;
                color: #92400e;
            }

            .result-level.moderate {
                background: #fed7aa;
                color: #9a3412;
            }

            .result-level.moderately_severe {
                background: #fed7aa;
                color: #9a3412;
            }

            .result-level.severe {
                background: #fee2e2;
                color: #991b1b;
            }

            .result-level.none {
                background: #d1fae5;
                color: #065f46;
            }

            .result-level.subthreshold {
                background: #fef3c7;
                color: #92400e;
            }

            .result-level.low {
                background: #d1fae5;
                color: #065f46;
            }

            .result-level.high {
                background: #fee2e2;
                color: #991b1b;
            }

            .result-date {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .result-date i {
                margin-right: 4px;
            }

            .no-result {
                text-align: center;
                padding: 15px 0;
            }

            .no-result i {
                font-size: 1.8rem;
                color: #c4b5fd;
                margin-bottom: 8px;
            }

            .no-result p {
                font-size: 0.75rem;
                color: #9ca3af;
                margin: 0;
            }

            .test-card-footer {
                padding: 16px;
                border-top: 1px solid #f0f0f0;
            }

            .btn-test-take {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 10px 16px;
                border-radius: 12px;
                text-decoration: none;
                font-size: 0.8rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .btn-test-take:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
                color: white;
            }

            .btn-test-disabled {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: #f3f4f6;
                color: #9ca3af;
                padding: 10px 16px;
                border-radius: 12px;
                font-size: 0.7rem;
                font-weight: 500;
                cursor: default;
            }

            .btn-test-disabled i {
                color: #9ca3af;
            }

            /* Points Display */
            .points-display {
                text-align: center;
                margin-bottom: 20px;
            }

            .points-number {
                font-size: 2.5rem;
                font-weight: 800;
                color: #7c3aed;
                display: block;
            }

            .points-label {
                font-size: 0.8rem;
                color: #6b7280;
            }

            .points-progress {
                margin-top: 15px;
            }

            .progress-label {
                display: flex;
                justify-content: space-between;
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .progress-bar {
                height: 8px;
                background: #e5e7eb;
                border-radius: 4px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #f59e0b, #d97706);
                border-radius: 4px;
            }

            .recent-points h4 {
                font-size: 0.8rem;
                margin-bottom: 10px;
                color: #1f2937;
            }

            .point-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 0;
                font-size: 0.8rem;
                border-bottom: 1px solid #f3f4f6;
            }

            .point-item i {
                color: #10b981;
                flex-shrink: 0;
            }

            .point-item span {
                flex: 1;
                color: #6b7280;
            }

            .point-item strong {
                color: #10b981;
                flex-shrink: 0;
            }

            .recent-session-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .recent-session-item:last-child {
                border-bottom: none;
            }

            .session-details {
                flex: 1;
            }

            .session-details h4 {
                font-size: 0.85rem;
                margin-bottom: 3px;
                color: #1f2937;
            }

            .session-details p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .stars {
                color: #fbbf24;
                font-size: 0.7rem;
                white-space: nowrap;
            }

            .btn-rate {
                font-size: 0.7rem;
                color: #7c3aed;
                text-decoration: none;
            }

            .referral-code {
                text-align: center;
            }

            .referral-code p {
                font-size: 0.8rem;
                color: #6b7280;
                margin-bottom: 15px;
            }

            .code-box {
                background: #f3f4f6;
                border-radius: 12px;
                padding: 12px 15px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .code-box span {
                font-size: 1rem;
                font-weight: 600;
                color: #7c3aed;
                letter-spacing: 1px;
                word-break: break-all;
            }

            .btn-copy {
                background: #7c3aed;
                border: none;
                padding: 6px 15px;
                border-radius: 8px;
                color: white;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.75rem;
            }

            .btn-copy:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            .referral-stats {
                display: flex;
                justify-content: center;
                gap: 20px;
                font-size: 0.75rem;
                color: #6b7280;
                flex-wrap: wrap;
            }

            .referral-stats i {
                margin-right: 5px;
            }

            .notification-item {
                display: flex;
                gap: 15px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .notification-item:last-child {
                border-bottom: none;
            }

            .notification-item.unread {
                background: #f5f3ff;
                margin: 0 -25px;
                padding: 12px 25px;
            }

            .notification-icon i {
                color: #7c3aed;
            }

            .notification-content {
                flex: 1;
            }

            .notification-content p {
                font-size: 0.8rem;
                margin-bottom: 3px;
                color: #374151;
            }

            .notification-content small {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            /* Responsive Breakpoints */
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .tests-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .apex-chart {
                    min-height: 320px;
                }
            }

            @media (max-width: 992px) {
                .dashboard-grid {
                    grid-template-columns: 1fr;
                }

                .dashboard-card.full-width {
                    grid-column: span 1;
                }

                .apex-chart {
                    min-height: 300px;
                }
            }

            @media (max-width: 768px) {
                .welcome-section {
                    flex-direction: column;
                    text-align: center;
                }

                .quick-actions {
                    justify-content: center;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .tests-grid {
                    grid-template-columns: 1fr;
                }

                .session-item {
                    flex-wrap: wrap;
                }

                .session-avatar {
                    width: 45px;
                    height: 45px;
                }

                .avatar-placeholder {
                    width: 45px;
                    height: 45px;
                    font-size: 1rem;
                }

                .session-action {
                    width: 100%;
                    margin-left: 60px;
                }

                .btn-join {
                    display: inline-block;
                    width: auto;
                }

                .recent-session-item {
                    flex-wrap: wrap;
                }

                .session-rating {
                    margin-left: 55px;
                }

                .code-box {
                    flex-direction: column;
                }

                .btn-copy {
                    width: 100%;
                }

                .referral-stats {
                    flex-direction: column;
                    align-items: center;
                    gap: 8px;
                }

                .notification-item.unread {
                    margin: 0 -16px;
                    padding: 12px 16px;
                }

                .card-header {
                    padding: 15px 20px;
                }

                .card-body {
                    padding: 15px 20px;
                }

                .stats-grid {
                    gap: 15px;
                }

                .stat-card {
                    padding: 15px;
                }

                .stat-icon {
                    width: 45px;
                    height: 45px;
                }

                .stat-icon i {
                    font-size: 1.2rem;
                }

                .stat-info h3 {
                    font-size: 1.2rem;
                }

                .apex-chart {
                    min-height: 280px;
                }
            }

            @media (max-width: 480px) {
                .dashboard-container {
                    padding: 0;
                }

                .welcome-section {
                    padding: 20px;
                }

                .welcome-text h2 {
                    font-size: 1.2rem;
                }

                .quick-action-btn {
                    padding: 8px 15px;
                    font-size: 0.75rem;
                }

                .quick-action-btn i {
                    font-size: 0.8rem;
                }

                .card-header h3 {
                    font-size: 0.9rem;
                }

                .session-info h4 {
                    font-size: 0.8rem;
                }

                .points-number {
                    font-size: 2rem;
                }

                .test-card-header {
                    padding: 12px;
                }

                .test-icon {
                    width: 40px;
                    height: 40px;
                }

                .test-icon i {
                    font-size: 1rem;
                }

                .test-title h4 {
                    font-size: 0.85rem;
                }

                .test-title p {
                    font-size: 0.65rem;
                }

                .apex-chart {
                    min-height: 250px;
                }
            }

            /* RTL Support */
            body.rtl .session-info p {
                margin-right: 0;
                margin-left: 10px;
            }

            body.rtl .point-item {
                flex-direction: row-reverse;
            }

            body.rtl .point-item span {
                text-align: right;
            }

            body.rtl .session-action {
                margin-left: 0;
                margin-right: 60px;
            }

            body.rtl .session-rating {
                margin-left: 0;
                margin-right: 55px;
            }

            body.rtl .referral-stats i {
                margin-right: 0;
                margin-left: 5px;
            }

            body.rtl .card-header h3 {
                flex-direction: row;
            }

            body.rtl .result-date i {
                margin-right: 0;
                margin-left: 4px;
            }

            body.rtl .test-card-header {
                flex-direction: row;
            }

            @media (max-width: 768px) {
                body.rtl .session-action {
                    margin-right: 55px;
                    margin-left: 0;
                }

                body.rtl .session-rating {
                    margin-right: 55px;
                    margin-left: 0;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Data passed from Laravel controller
            const moodLabels = {!! json_encode($moodLabels) !!};
            const moodValues = {!! json_encode($moodValues) !!};
            const currentLocale = '{{ app()->getLocale() }}';

            // Mood descriptions in Arabic and English (10 moods)
            const moodTranslations = {
                ar: {
                    10: { text: 'ممتاز جداً', emoji: '😍' },
                    9: { text: 'ممتاز', emoji: '😊' },
                    8: { text: 'سعيد جداً', emoji: '😄' },
                    7: { text: 'سعيد', emoji: '🙂' },
                    6: { text: 'جيد', emoji: '😐' },
                    5: { text: 'عادي', emoji: '😶' },
                    4: { text: 'حزين قليلاً', emoji: '😕' },
                    3: { text: 'حزين', emoji: '😔' },
                    2: { text: 'حزين جداً', emoji: '😢' },
                    1: { text: 'فظيع', emoji: '😫' }
                },
                en: {
                    10: { text: 'Absolutely Amazing', emoji: '😍' },
                    9: { text: 'Great', emoji: '😊' },
                    8: { text: 'Very Happy', emoji: '😄' },
                    7: { text: 'Happy', emoji: '🙂' },
                    6: { text: 'Pretty Good', emoji: '😐' },
                    5: { text: 'Neutral', emoji: '😶' },
                    4: { text: 'Slightly Sad', emoji: '😕' },
                    3: { text: 'Sad', emoji: '😔' },
                    2: { text: 'Very Sad', emoji: '😢' },
                    1: { text: 'Terrible', emoji: '😫' }
                }
            };

            let moodChart = null;
            let chartInitTimeout = null;
            let resizeTimeout = null;

            function getMoodDescription(value) {
                const translations = currentLocale === 'ar' ? moodTranslations.ar : moodTranslations.en;
                const roundedValue = Math.round(value);
                return translations[roundedValue] || translations[5];
            }

            function renderMoodChart() {
                @if($moodValues->count() > 0)

                    // Clear any existing chart
                    if (moodChart) {
                        moodChart.destroy();
                        moodChart = null;
                    }

                    // Create array of marker colors based on values
                    const markerColors = moodValues.map(value => {
                        if (value <= 2) return '#ef4444';
                        if (value <= 4) return '#f59e0b';
                        if (value <= 6) return '#eab308';
                        if (value <= 8) return '#10b981';
                        return '#7c3aed';
                    });

                    const moodChartOptions = {
                        series: [{
                            name: currentLocale === 'ar' ? 'مستوى المزاج' : 'Mood Level',
                            data: moodValues
                        }],
                        chart: {
                            type: 'line',
                            height: 380,
                            toolbar: { show: false },
                            zoom: { enabled: false },
                            animations: { enabled: true, speed: 500 },
                            background: 'transparent',
                            fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3,
                            colors: ['#7c3aed']
                        },
                        markers: {
                            size: 8,
                            hover: { size: 12, sizeOffset: 4 },
                            colors: markerColors,
                            strokeColors: '#ffffff',
                            strokeWidth: 2,
                            strokeOpacity: 1,
                            radius: 6,
                            hoverRadius: 12
                        },
                        tooltip: {
                            enabled: true,
                            shared: false,
                            intersect: true,
                            followCursor: true,
                            theme: 'dark',
                            style: {
                                fontSize: '12px',
                                fontFamily: currentLocale === 'ar' ? 'Cairo' : 'Inter'
                            },
                            x: {
                                show: true,
                                formatter: (value) => value
                            },
                            y: {
                                formatter: (value) => value + '/10',
                                title: { formatter: () => (currentLocale === 'ar' ? 'المزاج: ' : 'Mood: ') }
                            },
                            custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                                const value = moodValues[dataPointIndex];
                                const label = moodLabels[dataPointIndex];
                                const mood = getMoodDescription(value);
                                return `<div style="padding: 8px 12px; background: #1f2937; border: 1px solid #7c3aed; border-radius: 10px; color: white;">
                                            <strong style="color: #f3f4f6;">${label}</strong><br/>
                                            <span style="font-size: 18px;">${mood.emoji}</span> <span>${mood.text}</span><br/>
                                            <small style="color: #9ca3af;">${currentLocale === 'ar' ? 'المزاج' : 'Mood'}: ${value}/10</small>
                                        </div>`;
                            }
                        },
                        grid: {
                            borderColor: '#e5e7eb',
                            strokeDashArray: 4,
                            position: 'back'
                        },
                        xaxis: {
                            type: 'category',
                            categories: moodLabels,
                            title: {
                                text: currentLocale === 'ar' ? 'التاريخ' : 'Date',
                                style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' }
                            },
                            labels: {
                                rotate: -35,
                                rotateAlways: false,
                                hideOverlappingLabels: true,
                                showAlways: false,
                                style: { fontSize: '10px', colors: '#6b7280' }
                            },
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: {
                            min: 0,
                            max: 10,
                            tickAmount: 10,
                            title: {
                                text: currentLocale === 'ar' ? 'مستوى المزاج (1-10)' : 'Mood Level (1-10)',
                                style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' }
                            },
                            labels: {
                                style: { fontSize: '11px', colors: '#6b7280' },
                                formatter: (value) => Math.round(value)
                            }
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 0.3,
                                opacityFrom: 0.4,
                                opacityTo: 0.1,
                                stops: [0, 100],
                                colorStops: [
                                    { offset: 0, color: '#7c3aed', opacity: 0.3 },
                                    { offset: 100, color: '#7c3aed', opacity: 0.05 }
                                ]
                            }
                        },
                        responsive: [
                            {
                                breakpoint: 768,
                                options: {
                                    chart: { height: 280 },
                                    markers: { size: 6, hoverSize: 10 },
                                    xaxis: { labels: { rotate: -45, style: { fontSize: '9px' } } }
                                }
                            },
                            {
                                breakpoint: 480,
                                options: {
                                    chart: { height: 250 },
                                    markers: { size: 5, hoverSize: 8 },
                                    xaxis: { labels: { rotate: -45, style: { fontSize: '8px' } } }
                                }
                            }
                        ],
                        legend: {
                            show: true,
                            position: 'top',
                            horizontalAlign: 'center',
                            markers: { width: 10, height: 10, radius: 6 },
                            itemMargin: { horizontal: 10, vertical: 5 },
                            labels: { colors: '#374151' }
                        }
                    };

                    const chartElement = document.querySelector("#moodChart");
                    if (chartElement && typeof ApexCharts !== 'undefined') {
                        moodChart = new ApexCharts(chartElement, moodChartOptions);
                        moodChart.render();
                    }
                @endif
            }

            function resizeChart() {
                if (moodChart) {
                    moodChart.updateOptions({});
                    moodChart.resize();
                }
            }

            // Copy Referral Code
            function copyReferralCode() {
                const code = document.getElementById('referralCode').innerText;
                navigator.clipboard.writeText(code);

                Swal.fire({
                    icon: 'success',
                    title: '{{ __("Copied!") }}',
                    text: '{{ __("Referral code copied to clipboard") }}',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#fff',
                    color: '#1f2937'
                });
            }

            // Initialize chart after page is fully loaded
            function initChart() {
                // Clear any pending timeout
                if (chartInitTimeout) {
                    clearTimeout(chartInitTimeout);
                }

                // Wait for page to be fully rendered
                chartInitTimeout = setTimeout(function () {
                    renderMoodChart();
                    // Additional resize after chart is rendered
                    setTimeout(resizeChart, 200);
                    setTimeout(resizeChart, 500);
                }, 500);
            }

            // Handle window load to ensure everything is ready
            window.addEventListener('load', function () {
                initChart();
            });

            // Also run on DOM ready as fallback
            document.addEventListener('DOMContentLoaded', function () {
                // If window.load already triggered, this will be redundant but safe
                if (document.readyState === 'complete') {
                    initChart();
                }
            });

            // Re-render when sidebar toggle is clicked
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    setTimeout(function () {
                        if (moodChart) {
                            resizeChart();
                        } else {
                            renderMoodChart();
                        }
                        setTimeout(resizeChart, 150);
                    }, 400);
                });
            }

            // Re-render when mobile sidebar opens/closes
            const mobileToggle = document.getElementById('mobileSidebarToggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function () {
                    setTimeout(function () {
                        if (moodChart) {
                            resizeChart();
                        } else {
                            renderMoodChart();
                        }
                        setTimeout(resizeChart, 200);
                    }, 450);
                });
            }

            // Re-render on window resize (debounced)
            window.addEventListener('resize', function () {
                if (resizeTimeout) {
                    clearTimeout(resizeTimeout);
                }
                resizeTimeout = setTimeout(function () {
                    if (moodChart) {
                        moodChart.resize();
                    }
                }, 250);
            });
        </script>
    @endpush

@endsection