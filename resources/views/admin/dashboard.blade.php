{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', __('Admin Dashboard') . ' - ' . __('Tamman'))

@section('page-title', __('Admin Dashboard'))

@section('content')
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h2>{{ __('Welcome back') }}, {{ $admin->name }}!</h2>
                <p>{{ __("Here's what's happening on your platform today.") }}</p>
            </div>
            <div class="quick-actions">
                <a href="{{ route('admin.users') }}" class="quick-action-btn">
                    <i class="fas fa-users"></i>
                    <span>{{ __('Manage Users') }}</span>
                </a>
                <a href="{{ route('admin.specialists') }}" class="quick-action-btn">
                    <i class="fas fa-user-md"></i>
                    <span>{{ __('Manage Specialists') }}</span>
                </a>
                <a href="{{ route('admin.approvals') }}" class="quick-action-btn">
                    <i class="fas fa-clipboard-list"></i>
                    <span>{{ __('Verifications') }}</span>
                    @if($stats['pending_specialists'] > 0)
                        <span class="badge">{{ $stats['pending_specialists'] }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_users']) }}</h3>
                    <p>{{ __('Total Users') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-md"></i></div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_specialists']) }}</h3>
                    <p>{{ __('Verified Specialists') }}</p>
                    <small class="text-warning">{{ $stats['pending_specialists'] }} {{ __('pending') }}</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                    <small>{{ number_format($stats['completed_sessions']) }} {{ __('completed') }}</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_points_awarded']) }}</h3>
                    <p>{{ __('Points Awarded') }}</p>
                </div>
            </div>
        </div>

        <!-- Row 1: Today's Sessions & Upcoming Sessions (Side by Side) -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-day"></i> {{ __("Today's Sessions") }}</h3>
                    <a href="{{ route('admin.reports.sessions') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($todaySessions->count() > 0)
                        @foreach($todaySessions->take(8) as $session)
                            @php
                                $patientImage = $session->patient->getProfileImageUrl();
                                $patientInitial = mb_substr($session->patient->name, 0, 1, 'UTF-8');
                                $specialistImage = $session->specialist->getProfileImageUrl();
                                $specialistInitial = mb_substr($session->specialist->name, 0, 1, 'UTF-8');
                            @endphp
                            <div class="session-item">
                                <div class="session-time"><span
                                        class="time">{{ $session->session_datetime->translatedFormat('h:i A') }}</span></div>
                                <div class="session-avatars">
                                    <div class="session-avatar patient">
                                        @if($patientImage)<img src="{{ $patientImage }}" alt="{{ $session->patient->name }}">@else
                                        <div class="avatar-placeholder small">{{ $patientInitial }}</div>@endif
                                    </div>
                                    <div class="session-avatar specialist">
                                        @if($specialistImage)<img src="{{ $specialistImage }}"
                                        alt="{{ $session->specialist->name }}">@else<div class="avatar-placeholder small">
                                            {{ $specialistInitial }}</div>@endif
                                    </div>
                                </div>
                                <div class="session-info">
                                    <h4>{{ $session->patient->name }}</h4>
                                    <p>{{ __('with') }} {{ $session->specialist->name }}</p>
                                </div>
                                <div class="session-type"><i
                                        class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state"><i class="fas fa-calendar-alt"></i>
                            <p>{{ __('No sessions scheduled for today') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-week"></i> {{ __('Upcoming Sessions (Next 7 Days)') }}</h3>
                    <a href="{{ route('admin.reports.sessions') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($upcomingSessions->count() > 0)
                        @foreach($upcomingSessions as $session)
                            @php
                                $patientImage = $session->patient->getProfileImageUrl();
                                $patientInitial = mb_substr($session->patient->name, 0, 1, 'UTF-8');
                                $specialistImage = $session->specialist->getProfileImageUrl();
                                $specialistInitial = mb_substr($session->specialist->name, 0, 1, 'UTF-8');
                            @endphp
                            <div class="session-item upcoming">
                                <div class="session-date">
                                    <span class="day">{{ $session->session_datetime->format('d') }}</span>
                                    <span class="month">{{ $session->session_datetime->translatedFormat('M') }}</span>
                                </div>
                                <div class="session-avatars">
                                    <div class="session-avatar patient small">
                                        @if($patientImage)<img src="{{ $patientImage }}" alt="{{ $session->patient->name }}">@else
                                        <div class="avatar-placeholder small">{{ $patientInitial }}</div>@endif
                                    </div>
                                    <div class="session-avatar specialist small">
                                        @if($specialistImage)<img src="{{ $specialistImage }}"
                                        alt="{{ $session->specialist->name }}">@else<div class="avatar-placeholder small">
                                            {{ $specialistInitial }}</div>@endif
                                    </div>
                                </div>
                                <div class="session-info">
                                    <h4>{{ $session->patient->name }}</h4>
                                    <p>{{ __('with') }} {{ $session->specialist->name }}</p>
                                </div>
                                <div class="session-time-badge">{{ $session->session_datetime->translatedFormat('h:i A') }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state"><i class="fas fa-calendar-week"></i>
                            <p>{{ __('No upcoming sessions') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Row 2: Platform Activity Chart - Full Width -->
        <div class="dashboard-card full-width platform-activity-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line"></i> {{ __('Platform Activity (Last 6 Months)') }}</h3>
            </div>
            <div class="card-body">
                <div class="activity-charts-container">
                    <div class="activity-chart-box">
                        <div class="chart-subtitle">{{ __('New Users & Sessions') }}</div>
                        <div id="usersSessionsChart" class="apex-chart"></div>
                    </div>
                    <div class="activity-chart-box">
                        <div class="chart-subtitle">{{ __('Tamman Points') }}</div>
                        <div id="pointsChart" class="apex-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Tests Distribution Pie Chart & Content Distribution Pie Chart (Side by Side) -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> {{ __('Psychological Assessments') }}</h3>
                    <a href="{{ route('admin.reports.index') }}" class="view-all">{{ __('View Details') }}</a>
                </div>
                <div class="card-body tests-body">
                    <div id="testsPieChart" class="pie-chart-container"></div>
                    <div class="tests-legend">
                        <div class="legend-item"><span class="legend-color phq9"></span><span
                                class="legend-label">PHQ-9</span><span
                                class="legend-value">{{ number_format($testDistribution['phq9']) }}</span></div>
                        <div class="legend-item"><span class="legend-color gad7"></span><span
                                class="legend-label">GAD-7</span><span
                                class="legend-value">{{ number_format($testDistribution['gad7']) }}</span></div>
                        <div class="legend-item"><span class="legend-color pcl5"></span><span
                                class="legend-label">PCL-5</span><span
                                class="legend-value">{{ number_format($testDistribution['pcl5']) }}</span></div>
                        <div class="legend-item"><span class="legend-color isi"></span><span
                                class="legend-label">ISI</span><span
                                class="legend-value">{{ number_format($testDistribution['isi']) }}</span></div>
                        <div class="legend-item"><span class="legend-color pss"></span><span
                                class="legend-label">PSS</span><span
                                class="legend-value">{{ number_format($testDistribution['pss']) }}</span></div>
                        <div class="legend-item"><span class="legend-color cis"></span><span
                                class="legend-label">CIS</span><span
                                class="legend-value">{{ number_format($testDistribution['cis']) }}</span></div>
                    </div>
                    <div class="total-badge"><i class="fas fa-clipboard-list"></i><span>{{ __('Total') }}:
                            {{ array_sum($testDistribution) }}</span></div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> {{ __('Content Distribution') }}</h3>
                    <a href="{{ route('admin.content') }}" class="view-all">{{ __('Manage') }}</a>
                </div>
                <div class="card-body content-body">
                    <div id="contentPieChart" class="pie-chart-container"></div>
                    <div class="content-legend">
                        <div class="legend-item"><span class="legend-color article"></span><span
                                class="legend-label">{{ __('Articles') }}</span><span
                                class="legend-value">{{ $contentDistribution['article']['published'] }}/{{ $contentDistribution['article']['total'] }}</span>
                        </div>
                        <div class="legend-item"><span class="legend-color video"></span><span
                                class="legend-label">{{ __('Videos') }}</span><span
                                class="legend-value">{{ $contentDistribution['video']['published'] }}/{{ $contentDistribution['video']['total'] }}</span>
                        </div>
                        <div class="legend-item"><span class="legend-color tip"></span><span
                                class="legend-label">{{ __('Tips') }}</span><span
                                class="legend-value">{{ $contentDistribution['tip']['published'] }}/{{ $contentDistribution['tip']['total'] }}</span>
                        </div>
                        <div class="legend-item"><span class="legend-color guide"></span><span
                                class="legend-label">{{ __('Guides') }}</span><span
                                class="legend-value">{{ $contentDistribution['guide']['published'] }}/{{ $contentDistribution['guide']['total'] }}</span>
                        </div>
                    </div>
                    <div class="total-badge"><i class="fas fa-newspaper"></i><span>{{ __('Total') }}:
                            {{ array_sum(array_column($contentDistribution, 'total')) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Row 4: Donation Overview (Full Width) -->
        <div class="dashboard-card full-width donation-card">
            <div class="card-header">
                <h3><i class="fas fa-hand-holding-heart"></i> {{ __('Donation Overview') }}</h3>
                <a href="{{ route('admin.payments.index') }}" class="view-all">{{ __('View Details') }}</a>
            </div>
            <div class="card-body donation-body">
                <div class="donation-stats">
                    <div class="donation-stat">
                        <div class="donation-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="donation-info">
                            <h4>${{ number_format($donationStats['total_donated'], 2) }}</h4>
                            <p>{{ __('Total Donated') }}</p>
                        </div>
                    </div>
                    <div class="donation-stat">
                        <div class="donation-icon"><i class="fas fa-users"></i></div>
                        <div class="donation-info">
                            <h4>{{ number_format($donationStats['total_donors']) }}</h4>
                            <p>{{ __('Total Donors') }}</p>
                        </div>
                    </div>
                    <div class="donation-stat">
                        <div class="donation-icon"><i class="fas fa-heart"></i></div>
                        <div class="donation-info">
                            <h4>{{ number_format($donationStats['users_supported']) }}</h4>
                            <p>{{ __('Users Supported') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 5: Pending Applications & Recent Users (Side by Side) -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-clock"></i> {{ __('Pending Specialist Applications') }}</h3>
                    <a href="{{ route('admin.approvals') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($pendingSpecialists->count() > 0)
                        @foreach($pendingSpecialists as $specialist)
                            @php $specialistImage = $specialist->user->getProfileImageUrl();
                            $specialistInitial = mb_substr($specialist->user->name, 0, 1, 'UTF-8'); @endphp
                            <div class="pending-item">
                                <div class="pending-avatar">@if($specialistImage)<img src="{{ $specialistImage }}"
                                alt="{{ $specialist->user->name }}">@else<div class="avatar-placeholder pending">
                                        {{ $specialistInitial }}</div>@endif</div>
                                <div class="pending-info">
                                    <h4>{{ $specialist->user->name }}</h4>
                                    <p>{{ $specialist->specialization }}</p>
                                    <small>{{ $specialist->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="pending-actions"><a href="{{ route('admin.approvals.show', $specialist) }}"
                                        class="btn-review"><i class="fas fa-eye"></i> {{ __('Review') }}</a></div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state"><i class="fas fa-check-circle"></i>
                            <p>{{ __('No pending applications') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-plus"></i> {{ __('Recent Users') }}</h3>
                    <a href="{{ route('admin.users') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        @foreach($recentUsers as $user)
                            @php $userImage = $user->getProfileImageUrl();
                            $userInitial = mb_substr($user->name, 0, 1, 'UTF-8'); @endphp
                            <div class="user-item">
                                <div class="user-avatar">@if($userImage)<img src="{{ $userImage }}" alt="{{ $user->name }}">@else
                                <div class="avatar-placeholder">{{ $userInitial }}</div>@endif</div>
                                <div class="user-info">
                                    <h4>{{ $user->name }}</h4>
                                    <p>{{ $user->email }}</p><small>{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state"><i class="fas fa-users"></i>
                            <p>{{ __('No users found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Row 6: Recent System Logs & Daily Activity Timeline (Side by Side) -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> {{ __('Recent System Logs') }}</h3>
                    <a href="{{ route('admin.logs') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body logs-container">
                    @if($recentLogs->count() > 0)
                        <div class="logs-list">
                            @foreach($recentLogs as $log)
                                <div class="log-item">
                                    <div class="log-icon"><i
                                            class="fas {{ $log->action == 'verify_specialist' ? 'fa-check-circle' : ($log->action == 'delete_user' ? 'fa-trash' : 'fa-cog') }}"></i>
                                    </div>
                                    <div class="log-details">
                                        <div class="log-action">{{ __(ucfirst(str_replace('_', ' ', $log->action))) }}</div>
                                        <div class="log-meta">
                                            <span>{{ $log->admin->name ?? __('System') }}</span>
                                            <span class="separator">•</span>
                                            <span>{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state"><i class="fas fa-history"></i>
                            <p>{{ __('No system logs found') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> {{ __('Daily Activity (Last 7 Days)') }}</h3>
                </div>
                <div class="card-body">
                    <div class="timeline-stats">
                        @foreach($activityTimeline as $day)
                            <div class="timeline-day">
                                <div class="day-label">{{ $day['date'] }}</div>
                                <div class="day-stats">
                                    <div class="stat-badge sessions" title="{{ __('Sessions') }}"><i
                                            class="fas fa-calendar-check"></i><span
                                            class="stat-number">{{ $day['sessions'] }}</span></div>
                                    <div class="stat-badge users" title="{{ __('New Users') }}"><i
                                            class="fas fa-user-plus"></i><span
                                            class="stat-number">{{ $day['new_users'] }}</span></div>
                                    <div class="stat-badge mood" title="{{ __('Mood Entries') }}"><i
                                            class="fas fa-smile"></i><span class="stat-number">{{ $day['mood_entries'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
                position: relative;
            }

            .quick-action-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
                color: white;
            }

            .quick-action-btn .badge {
                position: absolute;
                top: -8px;
                right: -8px;
                background: #ef4444;
                color: white;
                font-size: 0.7rem;
                padding: 2px 6px;
                border-radius: 20px;
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

            .stat-info small {
                font-size: 0.65rem;
            }

            .text-warning {
                color: #f59e0b;
            }

            .dashboard-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
                margin-bottom: 25px;
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

            /* Platform Activity Card */
            .platform-activity-card {
                margin-bottom: 25px;
            }

            .activity-charts-container {
                display: flex;
                gap: 25px;
                flex-wrap: wrap;
            }

            .activity-chart-box {
                flex: 1;
                min-width: 300px;
                background: #f9fafb;
                border-radius: 16px;
                padding: 20px;
                min-height: 420px;
            }

            .chart-subtitle {
                font-size: 0.9rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 15px;
                text-align: center;
                padding-bottom: 10px;
                border-bottom: 2px solid #e5e7eb;
            }

            .apex-chart {
                width: 100%;
                min-height: 350px;
            }

            /* Session Items */
            .session-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
                flex-wrap: wrap;
            }

            .session-item:last-child {
                border-bottom: none;
            }

            .session-time {
                min-width: 60px;
            }

            .session-time .time {
                font-size: 0.8rem;
                font-weight: 500;
                color: #7c3aed;
            }

            .session-avatars {
                display: flex;
                gap: -5px;
                position: relative;
            }

            .session-avatar {
                width: 40px;
                height: 40px;
                flex-shrink: 0;
            }

            .session-avatar.patient {
                z-index: 2;
            }

            .session-avatar.specialist {
                margin-left: -10px;
                z-index: 1;
            }

            .session-avatar img,
            .avatar-placeholder {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1rem;
            }

            .avatar-placeholder.small {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
            }

            .avatar-placeholder.pending {
                width: 45px;
                height: 45px;
                font-size: 1rem;
            }

            .session-date {
                text-align: center;
                min-width: 50px;
            }

            .session-date .day {
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
                display: block;
            }

            .session-date .month {
                font-size: 0.65rem;
                color: #6b7280;
            }

            .session-info {
                flex: 1;
            }

            .session-info h4 {
                font-size: 0.85rem;
                margin-bottom: 2px;
                color: #1f2937;
            }

            .session-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .session-type {
                color: #7c3aed;
            }

            .session-time-badge {
                font-size: 0.7rem;
                padding: 4px 10px;
                background: #f3f4f6;
                border-radius: 20px;
                color: #6b7280;
            }

            /* Pie Charts */
            .tests-body,
            .content-body {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .pie-chart-container {
                width: 100%;
                max-width: 220px;
                margin: 0 auto 20px;
            }

            .tests-legend,
            .content-legend {
                width: 100%;
                margin-top: 15px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .legend-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 6px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .legend-color {
                width: 14px;
                height: 14px;
                border-radius: 4px;
                flex-shrink: 0;
            }

            .legend-color.phq9 {
                background: #7c3aed;
            }

            .legend-color.gad7 {
                background: #10b981;
            }

            .legend-color.pcl5 {
                background: #f59e0b;
            }

            .legend-color.isi {
                background: #ef4444;
            }

            .legend-color.pss {
                background: #ec4899;
            }

            .legend-color.cis {
                background: #06b6d4;
            }

            .legend-color.article {
                background: #7c3aed;
            }

            .legend-color.video {
                background: #10b981;
            }

            .legend-color.tip {
                background: #f59e0b;
            }

            .legend-color.guide {
                background: #06b6d4;
            }

            .legend-label {
                flex: 1;
                font-size: 0.75rem;
                color: #374151;
            }

            .legend-value {
                font-size: 0.75rem;
                font-weight: 600;
                color: #1f2937;
            }

            .total-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f5f3ff;
                padding: 8px 16px;
                border-radius: 30px;
                font-size: 0.75rem;
                font-weight: 500;
                color: #7c3aed;
                margin-top: 20px;
            }

            /* Donation */
            .donation-card {
                margin-bottom: 25px;
            }

            .donation-body {
                padding: 0;
            }

            .donation-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0;
            }

            .donation-stat {
                text-align: center;
                padding: 25px 20px;
                border-right: 1px solid #e5e7eb;
            }

            .donation-stat:last-child {
                border-right: none;
            }

            .donation-icon {
                width: 55px;
                height: 55px;
                background: #ede9fe;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 12px;
            }

            .donation-icon i {
                font-size: 1.5rem;
                color: #7c3aed;
            }

            .donation-info h4 {
                font-size: 1.25rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .donation-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 5px 0 0;
            }

            /* Pending Items */
            .pending-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .pending-item:last-child {
                border-bottom: none;
            }

            .pending-avatar {
                width: 45px;
                height: 45px;
                flex-shrink: 0;
            }

            .pending-avatar img {
                width: 100%;
                height: 100%;
                border-radius: 12px;
                object-fit: cover;
            }

            .pending-info {
                flex: 1;
            }

            .pending-info h4 {
                font-size: 0.9rem;
                margin-bottom: 3px;
                color: #1f2937;
            }

            .pending-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            .pending-info small {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .btn-review {
                padding: 6px 12px;
                background: #f3f4f6;
                border-radius: 8px;
                color: #7c3aed;
                text-decoration: none;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .btn-review:hover {
                background: #e5e7eb;
                color: #6d28d9;
            }

            /* Users */
            .user-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .user-item:last-child {
                border-bottom: none;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                flex-shrink: 0;
            }

            .user-avatar img,
            .avatar-placeholder {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
            }

            .user-info {
                flex: 1;
            }

            .user-info h4 {
                font-size: 0.85rem;
                margin-bottom: 2px;
                color: #1f2937;
            }

            .user-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .user-info small {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            /* Logs */
            .logs-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .log-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .log-item:last-child {
                border-bottom: none;
            }

            .log-icon i {
                color: #7c3aed;
            }

            .log-details {
                flex: 1;
            }

            .log-action {
                font-size: 0.8rem;
                font-weight: 500;
                color: #1f2937;
            }

            .log-meta {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .log-meta .separator {
                margin: 0 5px;
            }

            /* Activity Timeline */
            .timeline-stats {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .timeline-day {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
                padding: 8px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .day-label {
                font-size: 0.8rem;
                font-weight: 500;
                color: #374151;
                min-width: 100px;
            }

            .day-stats {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .stat-badge {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.7rem;
                padding: 4px 12px;
                border-radius: 20px;
                background: #f3f4f6;
                color: #6b7280;
                min-width: 85px;
                justify-content: center;
            }

            .stat-badge i {
                font-size: 0.7rem;
            }

            .stat-badge.sessions i {
                color: #7c3aed;
            }

            .stat-badge.users i {
                color: #10b981;
            }

            .stat-badge.mood i {
                color: #f59e0b;
            }

            .stat-number {
                min-width: 35px;
                text-align: center;
            }

            /* Empty State */
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
                margin-bottom: 0;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .donation-stats {
                    flex-direction: column;
                }

                .donation-stat {
                    border-right: none;
                    border-bottom: 1px solid #e5e7eb;
                }

                .donation-stat:last-child {
                    border-bottom: none;
                }

                .activity-charts-container {
                    flex-direction: column;
                }

                .activity-chart-box {
                    min-height: 380px;
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
                    min-height: 280px;
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

                .timeline-day {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .day-stats {
                    width: 100%;
                    justify-content: space-between;
                }

                .stat-badge {
                    flex: 1;
                    justify-content: center;
                }

                .session-item {
                    flex-wrap: wrap;
                }

                .donation-stats {
                    flex-direction: column;
                }

                .pie-chart-container {
                    max-width: 180px;
                }

                .pending-item {
                    flex-wrap: wrap;
                }

                .btn-review {
                    width: 100%;
                    justify-content: center;
                }

                .session-avatars {
                    order: -1;
                }

                .activity-chart-box {
                    min-height: 350px;
                }

                .apex-chart {
                    min-height: 250px;
                }
            }

            @media (max-width: 480px) {
                .pie-chart-container {
                    max-width: 150px;
                }

                .day-stats {
                    flex-direction: column;
                    gap: 8px;
                }

                .stat-badge {
                    min-width: auto;
                    width: 100%;
                }

                .session-time {
                    width: 100%;
                }

                .activity-chart-box {
                    min-height: 300px;
                }

                .apex-chart {
                    min-height: 220px;
                }
            }

            /* RTL Support */
            body.rtl .donation-stat {
                border-right: none;
                border-left: 1px solid #e5e7eb;
            }

            body.rtl .donation-stat:last-child {
                border-left: none;
            }

            body.rtl .card-header h3 {
                flex-direction: row;
            }

            body.rtl .legend-item {
                flex-direction: row;
            }

            body.rtl .session-avatar.specialist {
                margin-left: 0;
                margin-right: -10px;
            }

            body.rtl .session-avatars {
                flex-direction: row-reverse;
            }

            @media (max-width: 992px) {
                body.rtl .donation-stat {
                    border-left: 1px solid #e5e7eb;
                    border-right: none;
                }
            }

            @media (max-width: 768px) {
                body.rtl .donation-stat {
                    border-left: none;
                    border-bottom: 1px solid #e5e7eb;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const currentLocale = '{{ app()->getLocale() }}';
            const monthlyMonths = @json($monthlyStats['months']);
            const monthlyUsers = @json($monthlyStats['users']);
            const monthlySessions = @json($monthlyStats['sessions']);
            const monthlyPoints = @json($monthlyStats['points']);

            const testDistribution = @json($testDistribution);
            const totalTests = {{ array_sum($testDistribution) }};

            const contentDistribution = @json($contentDistribution);
            const totalContent = {{ array_sum(array_column($contentDistribution, 'total')) }};

            // Translations for chart labels
            const chartLabels = {
                newUsers: currentLocale === 'ar' ? 'مستخدمين جدد' : 'New Users',
                sessions: currentLocale === 'ar' ? 'جلسات' : 'Sessions',
                tammanPoints: currentLocale === 'ar' ? 'نقاط طمأن' : 'Tamman Points',
                points: currentLocale === 'ar' ? 'النقاط' : 'Points',
                month: currentLocale === 'ar' ? 'الشهر' : 'Month',
                count: currentLocale === 'ar' ? 'العدد' : 'Count'
            };

            const testLabels = { phq9: 'PHQ-9', gad7: 'GAD-7', pcl5: 'PCL-5', isi: 'ISI', pss: 'PSS', cis: 'CIS' };
            const contentLabels = {
                article: currentLocale === 'ar' ? 'مقالات' : 'Articles',
                video: currentLocale === 'ar' ? 'فيديوهات' : 'Videos',
                tip: currentLocale === 'ar' ? 'نصائح' : 'Tips',
                guide: currentLocale === 'ar' ? 'أدلة' : 'Guides'
            };
            const contentColors = { article: '#7c3aed', video: '#10b981', tip: '#f59e0b', guide: '#06b6d4' };

            function renderTestsPieChart() {
                const el = document.querySelector("#testsPieChart");
                if (!el || typeof ApexCharts === 'undefined') return;
                if (el.chart) el.chart.destroy();
                new ApexCharts(el, {
                    series: [testDistribution.phq9, testDistribution.gad7, testDistribution.pcl5, testDistribution.isi, testDistribution.pss, testDistribution.cis],
                    chart: { type: 'donut', height: 200, width: '100%', toolbar: { show: false }, animations: { enabled: true }, background: 'transparent', fontFamily: currentLocale === 'ar' ? 'Cairo' : 'Inter' },
                    labels: [testLabels.phq9, testLabels.gad7, testLabels.pcl5, testLabels.isi, testLabels.pss, testLabels.cis],
                    colors: ['#7c3aed', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4'],
                    legend: { show: false }, dataLabels: { enabled: false }, stroke: { show: true, width: 2, colors: ['#fff'] },
                    plotOptions: { pie: { donut: { size: '60%', labels: { show: true, name: { show: true, fontSize: '11px' }, value: { show: true, fontSize: '13px', fontWeight: 'bold' }, total: { show: true, label: currentLocale === 'ar' ? 'المجموع' : 'Total', fontSize: '10px', formatter: () => totalTests } } } } },
                    tooltip: { y: { formatter: (val) => val + ' ' + (currentLocale === 'ar' ? 'اختبار' : 'tests') }, theme: 'dark' },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 160 } } }, { breakpoint: 480, options: { chart: { height: 140 } } }]
                }).render();
            }

            function renderContentPieChart() {
                const el = document.querySelector("#contentPieChart");
                if (!el || typeof ApexCharts === 'undefined') return;
                if (el.chart) el.chart.destroy();
                new ApexCharts(el, {
                    series: [contentDistribution.article.total, contentDistribution.video.total, contentDistribution.tip.total, contentDistribution.guide.total],
                    chart: { type: 'donut', height: 200, width: '100%', toolbar: { show: false }, animations: { enabled: true }, background: 'transparent', fontFamily: currentLocale === 'ar' ? 'Cairo' : 'Inter' },
                    labels: [contentLabels.article, contentLabels.video, contentLabels.tip, contentLabels.guide],
                    colors: [contentColors.article, contentColors.video, contentColors.tip, contentColors.guide],
                    legend: { show: false }, dataLabels: { enabled: false }, stroke: { show: true, width: 2, colors: ['#fff'] },
                    plotOptions: { pie: { donut: { size: '60%', labels: { show: true, name: { show: true, fontSize: '11px' }, value: { show: true, fontSize: '13px', fontWeight: 'bold' }, total: { show: true, label: currentLocale === 'ar' ? 'المجموع' : 'Total', fontSize: '10px', formatter: () => totalContent } } } } },
                    tooltip: { y: { formatter: (val) => val + ' ' + (currentLocale === 'ar' ? 'محتوى' : 'items') }, theme: 'dark' },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 160 } } }, { breakpoint: 480, options: { chart: { height: 140 } } }]
                }).render();
            }

            function getNiceTickInterval(maxValue) {
                const targetTicks = 8;
                const roughInterval = maxValue / targetTicks;
                const magnitude = Math.pow(10, Math.floor(Math.log10(roughInterval)));
                const residual = roughInterval / magnitude;
                let niceInterval;
                if (residual < 1.5) niceInterval = 1 * magnitude;
                else if (residual < 3) niceInterval = 2 * magnitude;
                else if (residual < 7) niceInterval = 5 * magnitude;
                else niceInterval = 10 * magnitude;
                return Math.max(niceInterval, 1);
            }

            function getNiceMax(maxValue, tickInterval) {
                return Math.ceil(maxValue / tickInterval) * tickInterval;
            }

            // Chart 1: New Users & Sessions
            function renderUsersSessionsChart() {
                const element = document.querySelector("#usersSessionsChart");
                if (!element || typeof ApexCharts === 'undefined') return;
                if (element.chart) element.chart.destroy();

                const maxUsers = Math.max(...monthlyUsers, 1);
                const maxSessions = Math.max(...monthlySessions, 1);

                const tickIntervalUsers = getNiceTickInterval(maxUsers);
                const tickIntervalSessions = getNiceTickInterval(maxSessions);
                const niceMaxUsers = getNiceMax(maxUsers, tickIntervalUsers);
                const niceMaxSessions = getNiceMax(maxSessions, tickIntervalSessions);
                const tickAmountUsers = Math.ceil(niceMaxUsers / tickIntervalUsers);
                const tickAmountSessions = Math.ceil(niceMaxSessions / tickIntervalSessions);

                const options = {
                    series: [
                        { name: chartLabels.newUsers, data: monthlyUsers, type: 'line' },
                        { name: chartLabels.sessions, data: monthlySessions, type: 'line' }
                    ],
                    chart: {
                        type: 'line',
                        height: 350,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    stroke: { curve: 'smooth', width: 3 },
                    colors: ['#10b981', '#7c3aed'],
                    markers: { size: 6, hover: { size: 10 }, strokeColors: '#ffffff', strokeWidth: 2 },
                    tooltip: {
                        enabled: true,
                        shared: true,
                        intersect: false,
                        theme: 'dark',
                        y: {
                            formatter: function (value, { seriesIndex }) {
                                if (seriesIndex === 0) return value.toLocaleString() + ' ' + (currentLocale === 'ar' ? 'مستخدم' : 'users');
                                return value.toLocaleString() + ' ' + (currentLocale === 'ar' ? 'جلسة' : 'sessions');
                            }
                        }
                    },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: {
                        categories: monthlyMonths,
                        title: { text: chartLabels.month, style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' } },
                        labels: { rotate: -35, style: { fontSize: '10px' } }
                    },
                    yaxis: [
                        {
                            title: { text: chartLabels.newUsers, style: { fontSize: '12px', fontWeight: 500, color: '#10b981' } },
                            labels: { formatter: (val) => Math.round(val).toLocaleString(), style: { colors: '#10b981' } },
                            min: 0,
                            max: niceMaxUsers,
                            tickAmount: tickAmountUsers,
                            forceNiceScale: true
                        },
                        {
                            opposite: true,
                            title: { text: chartLabels.sessions, style: { fontSize: '12px', fontWeight: 500, color: '#7c3aed' } },
                            labels: { formatter: (val) => Math.round(val).toLocaleString(), style: { colors: '#7c3aed' } },
                            min: 0,
                            max: niceMaxSessions,
                            tickAmount: tickAmountSessions,
                            forceNiceScale: true
                        }
                    ],
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'center',
                        labels: { colors: '#374151' },
                        markers: { width: 12, height: 12, radius: 6 }
                    },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 280 }, xaxis: { labels: { rotate: -45 } } } }]
                };

                options.series[0].yaxis = 0;
                options.series[1].yaxis = 1;

                const chart = new ApexCharts(element, options);
                chart.render();
                element.chart = chart;
            }

            // Chart 2: Points
            function renderPointsChart() {
                const element = document.querySelector("#pointsChart");
                if (!element || typeof ApexCharts === 'undefined') return;
                if (element.chart) element.chart.destroy();

                const maxPoints = Math.max(...monthlyPoints, 1);

                const tickInterval = getNiceTickInterval(maxPoints);
                const niceMax = getNiceMax(maxPoints, tickInterval);
                const tickAmount = Math.ceil(niceMax / tickInterval);

                const options = {
                    series: [{
                        name: chartLabels.tammanPoints,
                        data: monthlyPoints,
                        type: 'line'
                    }],
                    chart: {
                        type: 'line',
                        height: 350,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    stroke: { curve: 'smooth', width: 3 },
                    colors: ['#f59e0b'],
                    markers: { size: 6, hover: { size: 10 }, strokeColors: '#ffffff', strokeWidth: 2 },
                    tooltip: {
                        enabled: true,
                        shared: true,
                        intersect: false,
                        theme: 'dark',
                        y: {
                            formatter: (value) => value.toLocaleString() + ' ' + (currentLocale === 'ar' ? 'نقطة' : 'points')
                        }
                    },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: {
                        categories: monthlyMonths,
                        title: { text: chartLabels.month, style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' } },
                        labels: { rotate: -35, style: { fontSize: '10px' } }
                    },
                    yaxis: {
                        title: {
                            text: chartLabels.points,
                            style: { fontSize: '12px', fontWeight: 500, color: '#f59e0b' }
                        },
                        labels: {
                            formatter: (val) => {
                                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return (val / 1000).toFixed(0) + 'K';
                                return val.toLocaleString();
                            },
                            style: { colors: '#f59e0b', fontSize: '11px' }
                        },
                        min: 0,
                        max: niceMax,
                        tickAmount: tickAmount,
                        forceNiceScale: true,
                        decimalsInFloat: 0
                    },
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'center',
                        labels: { colors: '#374151' },
                        markers: { width: 12, height: 12, radius: 6 }
                    },
                    responsive: [
                        {
                            breakpoint: 768,
                            options: {
                                chart: { height: 280 },
                                xaxis: { labels: { rotate: -45 } },
                                yaxis: { labels: { fontSize: '9px' } }
                            }
                        }
                    ]
                };

                const chart = new ApexCharts(element, options);
                chart.render();
                element.chart = chart;
            }

            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(() => {
                    renderTestsPieChart();
                    renderContentPieChart();
                    renderUsersSessionsChart();
                    renderPointsChart();
                }, 300);

                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) sidebarToggle.addEventListener('click', () => setTimeout(() => {
                    renderTestsPieChart();
                    renderContentPieChart();
                    renderUsersSessionsChart();
                    renderPointsChart();
                }, 300));

                const mobileToggle = document.getElementById('mobileSidebarToggle');
                if (mobileToggle) mobileToggle.addEventListener('click', () => setTimeout(() => {
                    renderTestsPieChart();
                    renderContentPieChart();
                    renderUsersSessionsChart();
                    renderPointsChart();
                }, 350));

                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        renderTestsPieChart();
                        renderContentPieChart();
                        renderUsersSessionsChart();
                        renderPointsChart();
                    }, 250);
                });
            });
        </script>
    @endpush

@endsection