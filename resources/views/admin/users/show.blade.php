{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.app')

@section('title', __('User Details') . ' - ' . __('Tamman'))

@section('page-title', __('User Details'))

@section('content')
    <div class="user-details-container">
        <!-- Back Button Row -->
        <div class="top-bar">
            <a href="{{ route('admin.users') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Users') }}
            </a>
            <div class="action-buttons">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-edit-header">
                    <i class="fas fa-edit"></i> {{ __('Edit User') }}
                </a>
                <button class="btn-impersonate-header" onclick="impersonateUser({{ $user->id }})">
                    <i class="fas fa-user-secret"></i> {{ __('Login as User') }}
                </button>
            </div>
        </div>

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                @php
                    $profileImage = $user->getProfileImageUrl();
                    $userInitial = mb_substr($user->name, 0, 1, 'UTF-8');
                @endphp
                @if($profileImage)
                    <img src="{{ $profileImage }}" alt="{{ $user->name }}">
                @else
                    <div class="avatar-placeholder">{{ $userInitial }}</div>
                @endif
                <div class="status-badge {{ $user->is_active ? 'active' : 'suspended' }}">
                    {{ $user->is_active ? __('Active') : __('Suspended') }}
                </div>
                @if($user->is_online)
                    <div class="online-badge">
                        <span class="online-dot"></span> {{ __('Online') }}
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <h1>{{ $user->name }}</h1>
                <div class="profile-meta">
                    <span><i class="fas fa-envelope"></i> {{ $user->email }}</span>
                    @if($user->phone)
                        <span><i class="fas fa-phone"></i> {{ $user->phone }}</span>
                    @endif
                    <span><i class="fas fa-calendar-alt"></i> {{ __('Joined') }}:
                        {{ $user->created_at->translatedFormat('M d, Y') }}</span>
                    <span><i class="fas fa-id-card"></i> ID: #{{ $user->id }}</span>
                </div>
                <div class="profile-tags">
                    @if($user->gender)
                        <span class="tag"><i class="fas fa-venus-mars"></i> {{ __(ucfirst($user->gender)) }}</span>
                    @endif
                    @if($user->date_of_birth)
                        <span class="tag"><i class="fas fa-birthday-cake"></i>
                            {{ $user->date_of_birth->translatedFormat('M d, Y') }} ({{ $user->age }} {{ __('years') }})</span>
                    @endif
                    @if($user->hasRole('donor'))
                        <span class="tag donor"><i class="fas fa-hand-holding-heart"></i> {{ __('Donor') }}</span>
                    @endif
                    @if($user->email_verified_at)
                        <span class="tag verified"><i class="fas fa-check-circle"></i> {{ __('Email Verified') }}</span>
                    @else
                        <span class="tag unverified"><i class="fas fa-times-circle"></i> {{ __('Email Not Verified') }}</span>
                    @endif
                </div>
                @if($user->last_login_at)
                    <div class="last-login">
                        <i class="fas fa-history"></i> {{ __('Last login') }}: {{ $user->last_login_at->diffForHumans() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats Cards - 2 Rows, 3 Cards Per Row -->
        <div class="quick-stats">
            <div class="stat-item">
                <div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['completed_sessions']) }}</h3>
                    <p>{{ __('Completed') }}</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon orange"><i class="fas fa-star"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_points']) }}</h3>
                    <p>{{ __('Points') }}</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon teal"><i class="fas fa-coins"></i></div>
                <div class="stat-data">
                    <h3>${{ number_format($stats['total_credit'], 2) }}</h3>
                    <p>{{ __('Credit') }}</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon indigo"><i class="fas fa-smile"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_mood_entries']) }}</h3>
                    <p>{{ __('Mood Entries') }}</p>
                    <small>{{ __('Avg') }}: {{ $stats['average_mood'] }}/10</small>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon pink"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['tests_taken']) }}</h3>
                    <p>{{ __('Tests Taken') }}</p>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> {{ __('Mood Trends') }}</h3>
                    <span class="chart-subtitle">{{ __('Last 30 days') }}</span>
                </div>
                <div class="chart-body">
                    <div id="moodChart" class="apex-chart"></div>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-bar"></i> {{ __('Session Trends') }}</h3>
                    <span class="chart-subtitle">{{ __('Last 6 months') }}</span>
                </div>
                <div class="chart-body">
                    <div id="sessionTrendsChart" class="apex-chart"></div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="details-grid">
            <!-- Sessions Section -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> {{ __('Recent Sessions') }}</h3>
                    <a href="{{ route('admin.reports.sessions') }}?user={{ $user->id }}"
                        class="view-link">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($user->therapySessionsAsPatient->count() > 0)
                        <div class="sessions-list">
                            @foreach($user->therapySessionsAsPatient->take(5) as $session)
                                <div class="session-row">
                                    <div class="session-date">
                                        <div class="day">{{ \Carbon\Carbon::parse($session->session_datetime)->format('d') }}</div>
                                        <div class="month">
                                            {{ \Carbon\Carbon::parse($session->session_datetime)->translatedFormat('M') }}</div>
                                    </div>
                                    <div class="session-details">
                                        <div class="session-specialist">{{ $session->specialist->name }}</div>
                                        <div class="session-time">
                                            {{ \Carbon\Carbon::parse($session->session_datetime)->format('h:i A') }}</div>
                                    </div>
                                    <div class="session-type">
                                        <span class="type-badge {{ $session->session_type }}">
                                            {{ __(ucfirst($session->session_type)) }}
                                        </span>
                                    </div>
                                    <div class="session-status">
                                        <span class="status-badge {{ $session->status }}">
                                            {{ __(ucfirst($session->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-placeholder">
                            <i class="fas fa-calendar-alt"></i>
                            <p>{{ __('No sessions found') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Points & Rewards Section -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> {{ __('Points Activity') }}</h3>
                </div>
                <div class="card-body">
                    @if($user->pointTransactions->count() > 0)
                        <div class="points-summary">
                            <div class="points-total">
                                <span class="label">{{ __('Earned') }}</span>
                                <span class="value positive">+{{ number_format($stats['points_earned']) }}</span>
                            </div>
                            <div class="points-total">
                                <span class="label">{{ __('Redeemed') }}</span>
                                <span class="value negative">{{ number_format($stats['points_redeemed']) }}</span>
                            </div>
                        </div>
                        <div class="transactions-list">
                            @foreach($user->pointTransactions->take(7) as $transaction)
                                <div class="transaction-row">
                                    <div class="transaction-icon {{ $transaction->type }}">
                                        <i
                                            class="fas {{ $transaction->type == 'earned' ? 'fa-plus-circle' : 'fa-minus-circle' }}"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <div class="transaction-source">
                                            {{ __(ucfirst(str_replace('_', ' ', $transaction->source))) }}</div>
                                        <div class="transaction-date">{{ $transaction->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="transaction-points {{ $transaction->type }}">
                                        {{ $transaction->type == 'earned' ? '+' : '-' }}{{ number_format($transaction->points) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-placeholder">
                            <i class="fas fa-star"></i>
                            <p>{{ __('No point transactions found') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tests Section -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list"></i> {{ __('Recent Test Results') }}</h3>
                </div>
                <div class="card-body">
                    @if($user->testResults->count() > 0)
                        <div class="tests-grid">
                            @foreach($user->testResults->take(6) as $test)
                                <div class="test-card">
                                    <div class="test-name">{{ strtoupper($test->test_type) }}</div>
                                    <div class="test-score">{{ $test->score }}</div>
                                    <div class="test-level {{ $test->result_level }}">{{ $test->getResultLevelArAttribute() }}</div>
                                    <div class="test-date">{{ $test->test_date->translatedFormat('M d, Y') }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-placeholder">
                            <i class="fas fa-clipboard-list"></i>
                            <p>{{ __('No test results found') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notifications Section -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> {{ __('Recent Notifications') }}</h3>
                </div>
                <div class="card-body">
                    @if($user->notifications->count() > 0)
                        <div class="notifications-list">
                            @foreach($user->notifications->take(5) as $notification)
                                <div class="notification-row {{ $notification->is_read ? '' : 'unread' }}">
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
                        </div>
                    @else
                        <div class="empty-placeholder">
                            <i class="fas fa-bell-slash"></i>
                            <p>{{ __('No notifications found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Donor Section (if donor) -->
        @if($user->donorProfile || $user->creditTransactionsAsRecipient->count() > 0)
            <div class="donor-section">
                <div class="donor-card">
                    <div class="card-header">
                        <h3><i class="fas fa-hand-holding-heart"></i> {{ __('Donor & Credits Information') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="donor-stats">
                            @if($user->donorProfile)
                                <div class="donor-stat">
                                    <span class="stat-label">{{ __('Total Donated') }}</span>
                                    <span class="stat-value">${{ number_format($user->donorProfile->total_donated, 2) }}</span>
                                </div>
                            @endif
                            @if($user->creditTransactionsAsRecipient->count() > 0)
                                <div class="donor-stat">
                                    <span class="stat-label">{{ __('Credits Received') }}</span>
                                    <span
                                        class="stat-value">${{ number_format($user->creditTransactionsAsRecipient->sum('amount'), 2) }}</span>
                                </div>
                            @endif
                        </div>
                        @if($user->creditTransactionsAsRecipient->count() > 0)
                            <div class="credits-list">
                                <h4>{{ __('Recent Credit Transactions') }}</h4>
                                @foreach($user->creditTransactionsAsRecipient->take(5) as $credit)
                                    <div class="credit-row">
                                        <div class="credit-amount">${{ number_format($credit->amount, 2) }}</div>
                                        <div class="credit-date">{{ $credit->created_at->translatedFormat('M d, Y') }}</div>
                                        <div class="credit-status">
                                            <span class="status-badge {{ $credit->status }}">{{ __(ucfirst($credit->status)) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .user-details-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Top Bar */
            .top-bar {
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

            .action-buttons {
                display: flex;
                gap: 12px;
            }

            .btn-edit-header,
            .btn-impersonate-header {
                padding: 8px 18px;
                border-radius: 40px;
                font-size: 0.85rem;
                text-decoration: none;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-edit-header {
                background: #7c3aed;
                color: white;
            }

            .btn-edit-header:hover {
                background: #6d28d9;
                transform: translateY(-2px);
                color: white;
            }

            .btn-impersonate-header {
                background: #e0e7ff;
                color: #4f46e5;
            }

            .btn-impersonate-header:hover {
                background: #c7d2fe;
                transform: translateY(-2px);
            }

            /* Profile Header */
            .profile-header {
                background: white;
                border-radius: 24px;
                padding: 30px;
                display: flex;
                gap: 30px;
                margin-bottom: 25px;
                flex-wrap: wrap;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .profile-avatar {
                position: relative;
            }

            .profile-avatar img,
            .avatar-placeholder {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3rem;
                font-weight: 600;
                color: white;
            }

            .status-badge {
                position: absolute;
                bottom: 5px;
                right: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 600;
                background: #10b981;
                color: white;
            }

            .status-badge.suspended {
                background: #ef4444;
            }

            .online-badge {
                position: absolute;
                top: 5px;
                right: 5px;
                background: #10b981;
                padding: 4px 8px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 600;
                color: white;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .online-dot {
                width: 8px;
                height: 8px;
                background: white;
                border-radius: 50%;
                animation: pulse 1.5s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }

            .profile-info {
                flex: 1;
            }

            .profile-info h1 {
                font-size: 1.6rem;
                margin: 0 0 10px;
                color: #1f2937;
            }

            .profile-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                margin-bottom: 12px;
            }

            .profile-meta span {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 0.8rem;
                color: #6b7280;
            }

            .profile-meta i {
                color: #7c3aed;
            }

            .profile-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 10px;
            }

            .tag {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                background: #f3f4f6;
                color: #374151;
            }

            .tag.donor {
                background: #fef3c7;
                color: #d97706;
            }

            .tag.verified {
                background: #d1fae5;
                color: #065f46;
            }

            .tag.unverified {
                background: #fee2e2;
                color: #991b1b;
            }

            .last-login {
                font-size: 0.75rem;
                color: #9ca3af;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            /* Quick Stats - 2 Rows, 3 Cards Per Row */
            .quick-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 25px;
            }

            .stat-item {
                background: white;
                border-radius: 16px;
                padding: 18px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s;
            }

            .stat-item:hover {
                transform: translateY(-3px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .stat-icon i {
                font-size: 1.3rem;
                color: white;
            }

            .stat-icon.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon.teal {
                background: linear-gradient(135deg, #14b8a6, #0d9488);
            }

            .stat-icon.indigo {
                background: linear-gradient(135deg, #6366f1, #4f46e5);
            }

            .stat-icon.pink {
                background: linear-gradient(135deg, #ec4899, #db2777);
            }

            .stat-data h3 {
                font-size: 1.3rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-data p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 4px 0 0;
            }

            .stat-data small {
                font-size: 0.6rem;
                color: #9ca3af;
                display: inline-block;
                margin-top: 2px;
            }

            /* Charts Row */
            .charts-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 25px;
                margin-bottom: 25px;
            }

            .chart-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .chart-header {
                padding: 18px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .chart-header h3 {
                margin: 0;
                font-size: 1rem;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .chart-header h3 i {
                color: #7c3aed;
            }

            .chart-subtitle {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .chart-body {
                padding: 20px;
                min-height: 320px;
            }

            .apex-chart {
                width: 100%;
                min-height: 280px;
            }

            /* Details Grid */
            .details-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
                margin-bottom: 25px;
            }

            .info-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .card-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .card-header h3 {
                margin: 0;
                font-size: 0.95rem;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .card-header h3 i {
                color: #7c3aed;
            }

            .view-link {
                font-size: 0.7rem;
                color: #7c3aed;
                text-decoration: none;
            }

            .card-body {
                padding: 16px 20px;
            }

            /* Sessions List */
            .sessions-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .session-row {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .session-row:last-child {
                border-bottom: none;
            }

            .session-date {
                text-align: center;
                min-width: 55px;
            }

            .session-date .day {
                font-size: 1.1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .session-date .month {
                font-size: 0.6rem;
                color: #6b7280;
            }

            .session-details {
                flex: 1;
            }

            .session-specialist {
                font-weight: 600;
                font-size: 0.85rem;
                color: #1f2937;
            }

            .session-time {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .type-badge {
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 500;
            }

            .type-badge.video {
                background: #ede9fe;
                color: #7c3aed;
            }

            .type-badge.audio {
                background: #d1fae5;
                color: #059669;
            }

            .type-badge.text {
                background: #fef3c7;
                color: #d97706;
            }

            .status-badge {
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
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

            /* Points Summary */
            .points-summary {
                display: flex;
                gap: 20px;
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .points-total {
                flex: 1;
                text-align: center;
            }

            .points-total .label {
                display: block;
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .points-total .value {
                font-size: 1.2rem;
                font-weight: 700;
            }

            .points-total .value.positive {
                color: #10b981;
            }

            .points-total .value.negative {
                color: #ef4444;
            }

            /* Transactions List */
            .transactions-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .transaction-row {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .transaction-icon {
                width: 32px;
                height: 32px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .transaction-icon.earned {
                background: #d1fae5;
                color: #059669;
            }

            .transaction-icon.redeemed {
                background: #fee2e2;
                color: #dc2626;
            }

            .transaction-details {
                flex: 1;
            }

            .transaction-source {
                font-size: 0.8rem;
                font-weight: 500;
                color: #1f2937;
            }

            .transaction-date {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            .transaction-points {
                font-size: 0.85rem;
                font-weight: 600;
            }

            .transaction-points.earned {
                color: #059669;
            }

            .transaction-points.redeemed {
                color: #dc2626;
            }

            /* Tests Grid */
            .tests-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .test-card {
                background: #f9fafb;
                border-radius: 12px;
                padding: 12px;
                text-align: center;
            }

            .test-name {
                font-size: 0.7rem;
                font-weight: 600;
                color: #6b7280;
                margin-bottom: 5px;
            }

            .test-score {
                font-size: 1.1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .test-level {
                font-size: 0.6rem;
                padding: 2px 8px;
                border-radius: 20px;
                display: inline-block;
                margin: 5px 0;
            }

            .test-level.minimal {
                background: #d1fae5;
                color: #065f46;
            }

            .test-level.mild {
                background: #fef3c7;
                color: #92400e;
            }

            .test-level.moderate {
                background: #fed7aa;
                color: #9a3412;
            }

            .test-level.severe {
                background: #fee2e2;
                color: #991b1b;
            }

            .test-date {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            /* Notifications */
            .notifications-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .notification-row {
                display: flex;
                gap: 12px;
                padding: 10px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .notification-row.unread {
                background: #f5f3ff;
                margin: 0 -20px;
                padding: 10px 20px;
            }

            .notification-icon i {
                color: #7c3aed;
            }

            .notification-content {
                flex: 1;
            }

            .notification-content p {
                font-size: 0.8rem;
                margin: 0 0 3px;
                color: #374151;
            }

            .notification-content small {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            /* Donor Section */
            .donor-section {
                margin-top: 0;
            }

            .donor-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .donor-stats {
                display: flex;
                gap: 30px;
                margin-bottom: 20px;
            }

            .donor-stat {
                flex: 1;
                text-align: center;
                padding: 15px;
                background: #fef3c7;
                border-radius: 16px;
            }

            .donor-stat .stat-label {
                display: block;
                font-size: 0.7rem;
                color: #92400e;
                margin-bottom: 5px;
            }

            .donor-stat .stat-value {
                font-size: 1.2rem;
                font-weight: 700;
                color: #d97706;
            }

            .credits-list h4 {
                font-size: 0.85rem;
                margin: 0 0 12px;
                color: #1f2937;
            }

            .credit-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .credit-amount {
                font-weight: 600;
                color: #1f2937;
            }

            .credit-date {
                font-size: 0.7rem;
                color: #6b7280;
            }

            /* Empty Placeholder */
            .empty-placeholder {
                text-align: center;
                padding: 30px 20px;
            }

            .empty-placeholder i {
                font-size: 2rem;
                color: #c4b5fd;
                margin-bottom: 10px;
                display: block;
            }

            .empty-placeholder p {
                color: #6b7280;
                font-size: 0.8rem;
                margin: 0;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .quick-stats {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 992px) {
                .charts-row {
                    grid-template-columns: 1fr;
                }

                .details-grid {
                    grid-template-columns: 1fr;
                }

                .tests-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 768px) {
                .user-details-container {
                    padding: 15px;
                }

                .profile-header {
                    flex-direction: column;
                    text-align: center;
                }

                .profile-meta {
                    justify-content: center;
                }

                .profile-tags {
                    justify-content: center;
                }

                .quick-stats {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .tests-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .top-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .action-buttons {
                    justify-content: center;
                }
            }

            @media (max-width: 480px) {
                .tests-grid {
                    grid-template-columns: 1fr;
                }

                .profile-meta {
                    flex-direction: column;
                    gap: 8px;
                    align-items: center;
                }

                .stat-item {
                    padding: 12px;
                }

                .stat-icon {
                    width: 40px;
                    height: 40px;
                }

                .stat-icon i {
                    font-size: 1rem;
                }

                .stat-data h3 {
                    font-size: 1.1rem;
                }
            }

            /* RTL Support */
            body.rtl .btn-back:hover {
                transform: translateX(3px);
            }

            body.rtl .card-header h3 i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .notification-row {
                flex-direction: row;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Chart data
            const moodLabels = @json($moodChart['labels']);
            const moodValues = @json($moodChart['values']);
            const sessionMonths = @json($sessionTrends['months']);
            const sessionCounts = @json($sessionTrends['sessions']);
            const currentLocale = '{{ app()->getLocale() }}';

            let moodChart = null;
            let sessionTrendsChart = null;

            function renderMoodChart() {
                const element = document.querySelector("#moodChart");
                if (!element) return;

                if (moodChart) moodChart.destroy();

                const markerColors = moodValues.map(value => {
                    if (!value) return '#c4b5fd';
                    if (value <= 3) return '#ef4444';
                    if (value <= 5) return '#f59e0b';
                    if (value <= 7) return '#10b981';
                    return '#7c3aed';
                });

                const options = {
                    series: [{
                        name: currentLocale === 'ar' ? 'مستوى المزاج' : 'Mood Level',
                        data: moodValues
                    }],
                    chart: {
                        type: 'line',
                        height: 280,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    stroke: { curve: 'smooth', width: 3, colors: ['#7c3aed'] },
                    markers: { size: 5, hover: { size: 8 }, colors: markerColors, strokeColors: '#ffffff', strokeWidth: 2 },
                    tooltip: { enabled: true, shared: true, intersect: false, theme: 'dark', y: { formatter: (value) => value ? value + '/10' : 'No data' } },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: { categories: moodLabels, labels: { rotate: -35, style: { fontSize: '10px' } } },
                    yaxis: { min: 0, max: 10, title: { text: currentLocale === 'ar' ? 'مستوى المزاج' : 'Mood Level' }, labels: { formatter: (value) => Math.round(value) } },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 100] } },
                    legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 240 } } }]
                };

                moodChart = new ApexCharts(element, options);
                moodChart.render();
            }

            function renderSessionTrendsChart() {
                const element = document.querySelector("#sessionTrendsChart");
                if (!element) return;

                if (sessionTrendsChart) sessionTrendsChart.destroy();

                const options = {
                    series: [{
                        name: currentLocale === 'ar' ? 'عدد الجلسات' : 'Number of Sessions',
                        data: sessionCounts
                    }],
                    chart: {
                        type: 'bar',
                        height: 280,
                        toolbar: { show: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    plotOptions: { bar: { borderRadius: 8, columnWidth: '60%' } },
                    colors: ['#7c3aed'],
                    tooltip: { enabled: true, theme: 'dark', y: { formatter: (value) => value } },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: { categories: sessionMonths, labels: { rotate: -35, style: { fontSize: '10px' } } },
                    yaxis: { title: { text: currentLocale === 'ar' ? 'عدد الجلسات' : 'Number of Sessions' }, labels: { formatter: (value) => Math.round(value) } },
                    legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 240 } } }]
                };

                sessionTrendsChart = new ApexCharts(element, options);
                sessionTrendsChart.render();
            }

            function renderAllCharts() {
                renderMoodChart();
                renderSessionTrendsChart();
            }

            // Initial render
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(renderAllCharts, 300);
            });

            // Re-render when sidebar toggles
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    setTimeout(renderAllCharts, 350);
                });
            }

            const mobileToggle = document.getElementById('mobileSidebarToggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function () {
                    setTimeout(renderAllCharts, 400);
                });
            }

            // Re-render on window resize
            let resizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(renderAllCharts, 250);
            });

            // Impersonate User
            function impersonateUser(userId) {
                Swal.fire({
                    title: '{{ __("Impersonate User") }}',
                    text: '{{ __("You will be logged in as this user.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7c3aed',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, Login as User") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `/admin/users/${userId}/impersonate`;
                    }
                });
            }
        </script>
    @endpush
@endsection