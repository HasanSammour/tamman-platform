{{-- resources/views/specialist/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', __('Specialist Dashboard') . ' - ' . __('Tamman'))

@section('page-title', __('Specialist Dashboard'))

@section('content')
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h2>{{ __('Welcome back') }}, {{ $user->name }}!</h2>
                <p>{{ __("Here's an overview of your practice.") }}</p>
                @if(!$isVerified)
                    <div class="verification-badge pending">
                        <i class="fas fa-clock"></i>
                        <span>{{ __('Verification Pending') }}</span>
                    </div>
                @else
                    <div class="verification-badge verified">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ __('Verified Specialist') }}</span>
                    </div>
                @endif
            </div>
            <div class="quick-actions">
                <a href="{{ route('specialist.schedule') }}" class="quick-action-btn">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ __('Manage Schedule') }}</span>
                </a>
                <a href="{{ route('specialist.treatment-plans.create') }}" class="quick-action-btn">
                    <i class="fas fa-tasks"></i>
                    <span>{{ __('Create Treatment Plan') }}</span>
                </a>
                <a href="{{ route('settings') }}" class="quick-action-btn">
                    <i class="fas fa-cog"></i>
                    <span>{{ __('Profile Settings') }}</span>
                </a>
            </div>
        </div>

        <!-- Profile Completion Card (if not 100%) -->
        @if($profileCompletion < 100 && !$isVerified)
            <div class="profile-completion-card">
                <div class="completion-content">
                    <i class="fas fa-user-edit"></i>
                    <div class="completion-info">
                        <h4>{{ __('Complete Your Profile') }}</h4>
                        <p>{{ __('Your profile is') }} {{ $profileCompletion }}%
                            {{ __('complete. Complete your profile to get verified faster.') }}</p>
                        <div class="completion-bar">
                            <div class="completion-fill" style="width: {{ $profileCompletion }}%"></div>
                        </div>
                    </div>
                    <a href="{{ route('specialist.settings') }}" class="btn-complete">{{ __('Complete Profile') }}</a>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                    <small>{{ number_format($stats['completed_sessions']) }} {{ __('completed') }}</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_clients']) }}</h3>
                    <p>{{ __('Total Clients') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>${{ number_format($stats['total_earnings'], 2) }}</h3>
                    <p>{{ __('Total Earnings') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['average_rating'], 1) }}</h3>
                    <p>{{ __('Average Rating') }}</p>
                    <small>({{ $stats['total_reviews'] }} {{ __('reviews') }})</small>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Today's Schedule -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-day"></i> {{ __("Today's Schedule") }}</h3>
                    <a href="{{ route('specialist.schedule') }}" class="view-all">{{ __('View Full Schedule') }}</a>
                </div>
                <div class="card-body">
                    @if($todaySessions->count() > 0)
                        @foreach($todaySessions as $session)
                            @php
                                $patientImage = $session->patient->getProfileImageUrl();
                                $patientInitial = mb_substr($session->patient->name, 0, 1, 'UTF-8');
                            @endphp
                            <div class="session-item">
                                <div class="session-avatar">
                                    @if($patientImage)
                                        <img src="{{ $patientImage }}" alt="{{ $session->patient->name }}">
                                    @else
                                        <div class="avatar-placeholder">{{ $patientInitial }}</div>
                                    @endif
                                </div>
                                <div class="session-time">
                                    <span class="time">{{ $session->session_datetime->translatedFormat('h:i A') }}</span>
                                </div>
                                <div class="session-info">
                                    <h4>{{ $session->patient->name }}</h4>
                                    <p>
                                        <i
                                            class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                                        {{ __(ucfirst($session->session_type)) }} {{ __('Session') }}
                                    </p>
                                </div>
                                <div class="session-action">
                                    <a href="{{ route('specialist.session-notes.edit', $session->id) }}" class="btn-prepare">
                                        <i class="fas fa-notes-medical"></i> {{ __('Notes') }}
                                    </a>
                                    @if($session->session_datetime <= now()->addMinutes(30))
                                        <a href="{{ $session->meeting_link ?? '#' }}" class="btn-join" target="_blank">
                                            <i class="fas fa-video"></i> {{ __('Join') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt"></i>
                            <p>{{ __('No sessions scheduled for today') }}</p>
                            <a href="{{ route('specialist.schedule') }}" class="btn-primary-sm">{{ __('Set Availability') }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-week"></i> {{ __('Upcoming Sessions') }}</h3>
                    <a href="{{ route('specialist.schedule') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($upcomingSessions->count() > 0)
                        @foreach($upcomingSessions as $session)
                            @php
                                $patientImage = $session->patient->getProfileImageUrl();
                                $patientInitial = mb_substr($session->patient->name, 0, 1, 'UTF-8');
                            @endphp
                            <div class="upcoming-item">
                                <div class="session-date">
                                    <span class="day">{{ $session->session_datetime->format('d') }}</span>
                                    <span class="month">{{ $session->session_datetime->translatedFormat('M') }}</span>
                                </div>
                                <div class="session-avatar small">
                                    @if($patientImage)
                                        <img src="{{ $patientImage }}" alt="{{ $session->patient->name }}">
                                    @else
                                        <div class="avatar-placeholder small">{{ $patientInitial }}</div>
                                    @endif
                                </div>
                                <div class="session-info">
                                    <h4>{{ $session->patient->name }}</h4>
                                    <p>{{ $session->session_datetime->translatedFormat('l, h:i A') }}</p>
                                    <span class="session-type-badge {{ $session->session_type }}">
                                        {{ __(ucfirst($session->session_type)) }}
                                    </span>
                                </div>
                                <div class="session-actions">
                                    <a href="{{ route('specialist.session-notes.edit', $session->id) }}" class="btn-icon"
                                        title="{{ __('Session Notes') }}">
                                        <i class="fas fa-notes-medical"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-week"></i>
                            <p>{{ __('No upcoming sessions') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Weekly Schedule Overview -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> {{ __('Weekly Schedule Overview') }}</h3>
                    <a href="{{ route('specialist.schedule') }}" class="view-all">{{ __('Edit') }}</a>
                </div>
                <div class="card-body schedule-body">
                    <div class="weekly-schedule">
                        @foreach($weeklySchedule as $day)
                            <div class="schedule-day {{ $day['has_availability'] ? 'has-slots' : 'no-slots' }}">
                                <div class="day-name">{{ __($day['day']) }}</div>
                                <div class="day-slots">
                                    @if($day['has_availability'])
                                        @foreach($day['slots'] as $slot)
                                            <span class="time-slot">{{ $slot['start'] }} - {{ $slot['end'] }}</span>
                                        @endforeach
                                    @else
                                        <span class="no-slots-text">{{ __('Not available') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Session Types Distribution - Pie Chart -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> {{ __('Session Types Distribution') }}</h3>
                </div>
                <div class="card-body session-types-body">
                    <div id="sessionTypesPieChart" class="pie-chart-container"></div>
                    <div class="pie-chart-legend">
                        <div class="legend-item">
                            <span class="legend-color video"></span>
                            <span class="legend-label">{{ __('Video Sessions') }}</span>
                            <span class="legend-value">{{ number_format($sessionTypes['video']) }}
                                ({{ $sessionTypes['video'] > 0 ? round($sessionTypes['video'] / array_sum($sessionTypes) * 100) : 0 }}%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color audio"></span>
                            <span class="legend-label">{{ __('Audio Sessions') }}</span>
                            <span class="legend-value">{{ number_format($sessionTypes['audio']) }}
                                ({{ $sessionTypes['audio'] > 0 ? round($sessionTypes['audio'] / array_sum($sessionTypes) * 100) : 0 }}%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color text"></span>
                            <span class="legend-label">{{ __('Text Sessions') }}</span>
                            <span class="legend-value">{{ number_format($sessionTypes['text']) }}
                                ({{ $sessionTypes['text'] > 0 ? round($sessionTypes['text'] / array_sum($sessionTypes) * 100) : 0 }}%)</span>
                        </div>
                    </div>
                    <div class="total-sessions-badge">
                        <i class="fas fa-chart-simple"></i>
                        <span>{{ __('Total') }}: {{ array_sum($sessionTypes) }} {{ __('sessions') }}</span>
                    </div>
                </div>
            </div>

            <!-- Two Charts Side by Side -->
            <div class="dashboard-card full-width charts-row">
                <div class="charts-row-container">
                    <div class="chart-box">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> {{ __('Monthly Sessions') }}</h3>
                        </div>
                        <div class="card-body chart-body">
                            <div id="sessionsChart" class="apex-chart"></div>
                        </div>
                    </div>
                    <div class="chart-box">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar"></i> {{ __('Monthly Earnings') }}</h3>
                        </div>
                        <div class="card-body chart-body">
                            <div id="earningsChart" class="apex-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-friends"></i> {{ __('Recent Clients') }}</h3>
                    <a href="{{ route('specialist.clients.index') }}" class="view-all">{{ __('View All Clients') }}</a>
                </div>
                <div class="card-body">
                    @if($recentClients->count() > 0)
                        @foreach($recentClients as $client)
                            @php
                                $clientImage = $client->getProfileImageUrl();
                                $clientInitial = mb_substr($client->name, 0, 1, 'UTF-8');
                            @endphp
                            <div class="client-item">
                                <div class="client-avatar">
                                    @if($clientImage)
                                        <img src="{{ $clientImage }}" alt="{{ $client->name }}">
                                    @else
                                        <div class="avatar-placeholder-client">{{ $clientInitial }}</div>
                                    @endif
                                </div>
                                <div class="client-info">
                                    <h4>{{ $client->name }}</h4>
                                    <p>{{ $client->email }}</p>
                                </div>
                                <div class="client-actions">
                                    <a href="{{ route('specialist.clients.show', $client->id) }}" class="btn-view"
                                        title="{{ __('View Client') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('chat.index', ['user' => $client->id]) }}" class="btn-message"
                                        title="{{ __('Send Message') }}">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>{{ __('No clients yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Treatment Plans - Fixed Mobile Layout -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-tasks"></i> {{ __('Active Treatment Plans') }}</h3>
                    <a href="{{ route('specialist.treatment-plans.index') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body treatment-plans-body">
                    @if($activeTreatmentPlans->count() > 0)
                        @foreach($activeTreatmentPlans as $plan)
                            @php
                                $completedTasks = $plan->tasks->where('is_completed', true)->count();
                                $totalTasks = $plan->tasks->count();
                                $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks * 100) : 0;
                            @endphp
                            <div class="plan-item">
                                <div class="plan-info">
                                    <div class="plan-header">
                                        <h4 class="plan-title">{{ $plan->title }}</h4>
                                        <span class="plan-status active">{{ __('Active') }}</span>
                                    </div>
                                    <p class="plan-patient">
                                        <i class="fas fa-user-circle"></i>
                                        {{ __('For') }}: {{ $plan->patient->name }}
                                    </p>
                                    <div class="plan-progress-wrapper">
                                        <div class="plan-progress">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="progress-text">{{ $completedTasks }}/{{ $totalTasks }}
                                                {{ __('tasks') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="plan-actions">
                                    <a href="{{ route('specialist.treatment-plans.show', $plan->id) }}" class="btn-view"
                                        title="{{ __('View Plan') }}">
                                        <i class="fas fa-eye"></i>
                                        <span class="btn-text">{{ __('View') }}</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-tasks"></i>
                            <p>{{ __('No active treatment plans') }}</p>
                            <a href="{{ route('specialist.treatment-plans.create') }}"
                                class="btn-primary-sm">{{ __('Create Treatment Plan') }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Sessions History -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> {{ __('Recent Sessions') }}</h3>
                    <a href="{{ route('specialist.schedule') }}" class="view-all">{{ __('View All') }}</a>
                </div>
                <div class="card-body">
                    @if($recentSessions->count() > 0)
                        @foreach($recentSessions as $session)
                            @php
                                $patientImage = $session->patient->getProfileImageUrl();
                                $patientInitial = mb_substr($session->patient->name, 0, 1, 'UTF-8');
                            @endphp
                            <div class="recent-session-item">
                                <div class="session-icon">
                                    @if($patientImage)
                                        <img src="{{ $patientImage }}" alt="{{ $session->patient->name }}" class="patient-avatar-sm">
                                    @else
                                        <div class="avatar-placeholder-sm">{{ $patientInitial }}</div>
                                    @endif
                                </div>
                                <div class="session-details">
                                    <h4>{{ $session->patient->name }}</h4>
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
                                        <span class="no-rating">{{ __('Awaiting review') }}</span>
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

            <!-- Recent Notifications -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> {{ __('Recent Notifications') }}</h3>
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
                                    <p>{{ $notification->message }}</p>
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

            <!-- Quick Tips Card -->
            <div class="dashboard-card full-width tips-card">
                <div class="card-header">
                    <h3><i class="fas fa-lightbulb" style="color: #fbbf24;"></i> <span
                            style="color: #1f2937;">{{ __('Quick Tips') }}</span></h3>
                </div>
                <div class="card-body">
                    <div class="tips-grid">
                        <div class="tip-item">
                            <i class="fas fa-check-circle" style="color: #fbbf24;"></i>
                            <span>{{ __('Keep your availability updated to receive more booking requests') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle" style="color: #fbbf24;"></i>
                            <span>{{ __('Complete session notes promptly after each session') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle" style="color: #fbbf24;"></i>
                            <span>{{ __('Respond to patient messages within 24 hours') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle" style="color: #fbbf24;"></i>
                            <span>{{ __('Create treatment plans to help patients track their progress') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle" style="color: #fbbf24;"></i>
                            <span>{{ __('Encourage patients to leave reviews after sessions') }}</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-check-circle" style="color: #fbbf24;"></i>
                            <span>{{ __('Regularly update your professional profile and qualifications') }}</span>
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
                margin-bottom: 10px;
            }

            .verification-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                border-radius: 50px;
                font-size: 0.75rem;
                font-weight: 500;
            }

            .verification-badge.pending {
                background: rgba(245, 158, 11, 0.2);
                color: #fbbf24;
            }

            .verification-badge.verified {
                background: rgba(16, 185, 129, 0.2);
                color: #34d399;
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

            .profile-completion-card {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                border-radius: 16px;
                padding: 20px 25px;
                margin-bottom: 30px;
            }

            .completion-content {
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .completion-content i {
                font-size: 2rem;
                color: #d97706;
            }

            .completion-info {
                flex: 1;
            }

            .completion-info h4 {
                color: #92400e;
                margin-bottom: 5px;
            }

            .completion-info p {
                color: #b45309;
                font-size: 0.8rem;
                margin-bottom: 10px;
            }

            .completion-bar {
                height: 6px;
                background: rgba(217, 119, 6, 0.2);
                border-radius: 3px;
                overflow: hidden;
                max-width: 300px;
            }

            .completion-fill {
                height: 100%;
                background: #d97706;
                border-radius: 3px;
            }

            .btn-complete {
                background: #d97706;
                color: white;
                padding: 8px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.8rem;
                transition: all 0.3s ease;
            }

            .btn-complete:hover {
                background: #b45309;
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

            .stat-info small {
                font-size: 0.65rem;
                color: #9ca3af;
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

            /* Weekly Schedule */
            .schedule-body {
                display: flex;
                flex-direction: column;
            }

            .weekly-schedule {
                display: flex;
                flex-direction: column;
                gap: 12px;
                flex: 1;
            }

            .schedule-day {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 8px 0;
                border-bottom: 1px solid #f3f4f6;
                flex-wrap: wrap;
            }

            .day-name {
                width: 100px;
                font-weight: 600;
                color: #374151;
                flex-shrink: 0;
            }

            .day-slots {
                flex: 1;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .time-slot {
                background: #ede9fe;
                color: #7c3aed;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
            }

            .no-slots-text {
                color: #9ca3af;
                font-size: 0.7rem;
            }

            .schedule-day.no-slots .day-name {
                color: #9ca3af;
            }

            /* Session Types Pie Chart */
            .session-types-body {
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

            .pie-chart-legend {
                width: 100%;
                margin-top: 15px;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .legend-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 6px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .legend-color {
                width: 16px;
                height: 16px;
                border-radius: 4px;
                flex-shrink: 0;
            }

            .legend-color.video {
                background: #7c3aed;
            }

            .legend-color.audio {
                background: #10b981;
            }

            .legend-color.text {
                background: #f59e0b;
            }

            .legend-label {
                flex: 1;
                font-size: 0.8rem;
                color: #374151;
            }

            .legend-value {
                font-size: 0.8rem;
                font-weight: 600;
                color: #1f2937;
            }

            .total-sessions-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f5f3ff;
                padding: 8px 16px;
                border-radius: 30px;
                font-size: 0.8rem;
                font-weight: 500;
                color: #7c3aed;
                margin-top: 20px;
            }

            .total-sessions-badge i {
                font-size: 0.9rem;
            }

            /* Active Treatment Plans - Fixed Mobile Layout */
            .treatment-plans-body {
                padding: 20px 25px;
            }

            .plan-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 15px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .plan-item:last-child {
                border-bottom: none;
            }

            .plan-info {
                flex: 1;
                min-width: 0;
            }

            .plan-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 6px;
            }

            .plan-title {
                font-size: 0.9rem;
                font-weight: 600;
                color: #1f2937;
                margin: 0;
                word-break: break-word;
            }

            .plan-status {
                font-size: 0.6rem;
                padding: 2px 8px;
                border-radius: 20px;
                background: #d1fae5;
                color: #065f46;
                white-space: nowrap;
            }

            .plan-status.active {
                background: #d1fae5;
                color: #065f46;
            }

            .plan-patient {
                font-size: 0.7rem;
                color: #6b7280;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .plan-patient i {
                font-size: 0.7rem;
                color: #7c3aed;
            }

            .plan-progress-wrapper {
                width: 100%;
            }

            .plan-progress {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .plan-progress .progress-bar {
                flex: 1;
                min-width: 120px;
                height: 6px;
                background: #e5e7eb;
                border-radius: 3px;
                overflow: hidden;
            }

            .plan-progress .progress-fill {
                height: 100%;
                background: #10b981;
                border-radius: 3px;
            }

            .progress-text {
                font-size: 0.7rem;
                font-weight: 500;
                color: #6b7280;
                white-space: nowrap;
            }

            .plan-actions {
                flex-shrink: 0;
            }

            .plan-actions .btn-view {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 8px 14px;
                background: #f3f4f6;
                border-radius: 10px;
                color: #6b7280;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .plan-actions .btn-view:hover {
                background: #ede9fe;
                color: #7c3aed;
            }

            .plan-actions .btn-view .btn-text {
                font-size: 0.75rem;
            }

            /* Charts Row */
            .charts-row {
                grid-column: span 2;
            }

            .charts-row-container {
                display: flex;
                flex-direction: row;
                gap: 25px;
                width: 100%;
            }

            .chart-box {
                flex: 1;
                min-width: 0;
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                display: flex;
                flex-direction: column;
            }

            .chart-box .card-header {
                padding: 20px 25px;
                border-bottom: 1px solid #e5e7eb;
                flex-shrink: 0;
            }

            .chart-box .card-header h3 {
                margin: 0;
                font-size: 1rem;
            }

            .chart-body {
                padding: 20px;
                flex: 1;
                min-height: 320px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .apex-chart {
                width: 100%;
                min-height: 280px;
            }

            /* Session Items */
            .session-item,
            .upcoming-item,
            .client-item,
            .recent-session-item,
            .notification-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
                flex-wrap: wrap;
            }

            .session-item:last-child,
            .upcoming-item:last-child,
            .client-item:last-child,
            .recent-session-item:last-child,
            .notification-item:last-child {
                border-bottom: none;
            }

            .session-avatar,
            .client-avatar {
                width: 45px;
                height: 45px;
                flex-shrink: 0;
            }

            .session-avatar img,
            .client-avatar img {
                width: 100%;
                height: 100%;
                border-radius: 12px;
                object-fit: cover;
            }

            .avatar-placeholder {
                width: 45px;
                height: 45px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1rem;
            }

            .avatar-placeholder.small {
                width: 35px;
                height: 35px;
                font-size: 0.8rem;
            }

            .avatar-placeholder-client {
                width: 45px;
                height: 45px;
                background: linear-gradient(135deg, #10b981, #059669);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1rem;
            }

            .avatar-placeholder-sm {
                width: 35px;
                height: 35px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 0.8rem;
            }

            .patient-avatar-sm {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                object-fit: cover;
            }

            .session-time .time {
                font-size: 0.8rem;
                font-weight: 600;
                color: #7c3aed;
                background: #ede9fe;
                padding: 4px 10px;
                border-radius: 20px;
            }

            .session-info,
            .client-info,
            .session-details,
            .notification-content {
                flex: 1;
            }

            .session-info h4,
            .client-info h4,
            .session-details h4 {
                font-size: 0.9rem;
                margin-bottom: 3px;
                color: #1f2937;
            }

            .session-info p,
            .client-info p,
            .session-details p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .btn-prepare,
            .btn-join,
            .btn-view,
            .btn-message,
            .btn-icon {
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.7rem;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .btn-prepare,
            .btn-view,
            .btn-message,
            .btn-icon {
                background: #f3f4f6;
                color: #6b7280;
            }

            .btn-prepare:hover,
            .btn-view:hover,
            .btn-message:hover,
            .btn-icon:hover {
                background: #e5e7eb;
                color: #7c3aed;
            }

            .btn-join {
                background: #10b981;
                color: white;
            }

            .btn-join:hover {
                background: #059669;
                transform: translateY(-2px);
                color: white;
            }

            .session-date {
                text-align: center;
                min-width: 45px;
            }

            .session-date .day {
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
                display: block;
            }

            .session-date .month {
                font-size: 0.6rem;
                color: #6b7280;
            }

            .session-type-badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 0.6rem;
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

            .stars {
                color: #fbbf24;
                font-size: 0.7rem;
                white-space: nowrap;
            }

            .no-rating {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .notification-item.unread {
                background: #f5f3ff;
                margin: 0 -25px;
                padding: 10px 25px;
            }

            .notification-icon i {
                color: #7c3aed;
            }

            .empty-state {
                text-align: center;
                padding: 30px 20px;
            }

            .empty-state i {
                font-size: 2.5rem;
                color: #c4b5fd;
                margin-bottom: 12px;
            }

            .empty-state p {
                color: #6b7280;
                margin-bottom: 12px;
                font-size: 0.8rem;
            }

            .btn-primary-sm {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 6px 16px;
                border-radius: 40px;
                text-decoration: none;
                font-size: 0.75rem;
                display: inline-block;
                transition: all 0.3s ease;
            }

            .btn-primary-sm:hover {
                transform: translateY(-2px);
                color: white;
            }

            .tips-card {
                background: linear-gradient(135deg, #1e1b4b, #2e1065);
            }

            .tips-card .card-header {
                border-bottom-color: rgba(255, 255, 255, 0.1);
            }

            .tips-card .card-header h3 {
                color: white;
            }

            .tips-card .card-header h3 i {
                color: #fbbf24;
            }

            .tips-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }

            .tip-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 14px 18px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                transition: all 0.3s ease;
            }

            .tip-item:hover {
                background: rgba(255, 255, 255, 0.15);
                transform: translateY(-2px);
            }

            .tip-item i {
                color: #fbbf24;
                font-size: 1.1rem;
                flex-shrink: 0;
            }

            .tip-item span {
                font-size: 0.8rem;
                color: rgba(255, 255, 255, 0.9);
                line-height: 1.4;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .tips-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .apex-chart {
                    min-height: 280px;
                }
            }

            @media (max-width: 992px) {
                .dashboard-grid {
                    grid-template-columns: 1fr;
                }

                .dashboard-card.full-width {
                    grid-column: span 1;
                }

                .charts-row {
                    grid-column: span 1;
                }

                .charts-row-container {
                    flex-direction: column;
                }

                .tips-grid {
                    grid-template-columns: 1fr;
                }

                .schedule-day {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .day-name {
                    width: 100%;
                }

                .day-slots {
                    width: 100%;
                }

                .apex-chart {
                    min-height: 260px;
                }

                .chart-body {
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

                .completion-content {
                    flex-direction: column;
                    text-align: center;
                }

                .completion-bar {
                    max-width: 100%;
                }

                .session-item,
                .upcoming-item {
                    flex-wrap: wrap;
                }

                .tip-item {
                    padding: 10px 12px;
                }

                .tip-item span {
                    font-size: 0.75rem;
                }

                .session-action {
                    width: 100%;
                    justify-content: flex-start;
                }

                .client-actions {
                    width: 100%;
                    justify-content: flex-start;
                }

                .recent-session-item {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .session-rating {
                    margin-top: 5px;
                }

                .apex-chart {
                    min-height: 240px;
                }

                .chart-body {
                    min-height: 280px;
                }

                .pie-chart-container {
                    max-width: 180px;
                }

                .legend-item {
                    gap: 8px;
                }

                .legend-label,
                .legend-value {
                    font-size: 0.7rem;
                }

                /* Treatment Plans Mobile Fix */
                .plan-item {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 12px;
                }

                .plan-info {
                    width: 100%;
                }

                .plan-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .plan-status {
                    align-self: flex-start;
                }

                .plan-progress {
                    flex-direction: column;
                    align-items: flex-start;
                    width: 100%;
                }

                .plan-progress .progress-bar {
                    width: 100%;
                    min-width: auto;
                }

                .progress-text {
                    white-space: normal;
                }

                .plan-actions {
                    width: 100%;
                }

                .plan-actions .btn-view {
                    justify-content: center;
                    width: 100%;
                }

                .plan-actions .btn-view .btn-text {
                    display: inline;
                }
            }

            @media (max-width: 480px) {
                .apex-chart {
                    min-height: 220px;
                }

                .chart-body {
                    padding: 15px;
                    min-height: 260px;
                }

                .pie-chart-container {
                    max-width: 150px;
                }

                .plan-title {
                    font-size: 0.85rem;
                }

                .plan-patient {
                    font-size: 0.65rem;
                }
            }

            /* RTL Support */
            body.rtl .card-header h3,
            body.rtl .tip-item,
            body.rtl .completion-content,
            body.rtl .schedule-day {
                flex-direction: row;
            }

            body.rtl .session-info p {
                margin-right: 0;
                margin-left: 10px;
            }

            body.rtl .day-name {
                text-align: right;
            }

            body.rtl .legend-item {
                flex-direction: row;
            }

            body.rtl .plan-header {
                flex-direction: row;
            }

            body.rtl .plan-actions .btn-view {
                flex-direction: row;
            }

            @media (max-width: 768px) {
                body.rtl .schedule-day {
                    flex-direction: column;
                    align-items: flex-start;
                }

                body.rtl .plan-item {
                    flex-direction: column;
                    align-items: flex-start;
                }

                body.rtl .plan-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                body.rtl .plan-progress {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Data passed from Laravel controller
            const monthsData = @json($monthlySessions['months']);
            const sessionsData = @json($monthlySessions['counts']);
            const earningsData = @json($monthlyEarnings['earnings']);
            const currentLocale = '{{ app()->getLocale() }}';
            const sessionTypes = @json($sessionTypes);
            const totalSessions = {{ array_sum($sessionTypes) }};

            // Month names in Arabic for translation
            const monthNamesAr = {
                'Jan': 'يناير', 'Feb': 'فبراير', 'Mar': 'مارس', 'Apr': 'أبريل',
                'May': 'مايو', 'Jun': 'يونيو', 'Jul': 'يوليو', 'Aug': 'أغسطس',
                'Sep': 'سبتمبر', 'Oct': 'أكتوبر', 'Nov': 'نوفمبر', 'Dec': 'ديسمبر'
            };

            function translateMonth(monthStr) {
                if (currentLocale !== 'ar') return monthStr;
                if (typeof monthStr !== 'string') return monthStr;
                const parts = monthStr.split(' ');
                if (parts.length >= 1) {
                    const monthAbbr = parts[0];
                    if (monthNamesAr[monthAbbr]) {
                        parts[0] = monthNamesAr[monthAbbr];
                        return parts.join(' ');
                    }
                }
                return monthStr;
            }

            // Session Types Pie Chart
            function renderPieChart() {
                const pieOptions = {
                    series: [sessionTypes.video, sessionTypes.audio, sessionTypes.text],
                    chart: {
                        type: 'donut',
                        height: 200,
                        width: '100%',
                        toolbar: { show: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    labels: [
                        currentLocale === 'ar' ? 'جلسات فيديو' : 'Video Sessions',
                        currentLocale === 'ar' ? 'جلسات صوتية' : 'Audio Sessions',
                        currentLocale === 'ar' ? 'جلسات نصية' : 'Text Sessions'
                    ],
                    colors: ['#7c3aed', '#10b981', '#f59e0b'],
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['#ffffff'] },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: { show: true, fontSize: '14px', fontFamily: currentLocale === 'ar' ? 'Cairo' : 'Inter', color: '#374151' },
                                    value: { show: true, fontSize: '16px', fontWeight: 'bold', color: '#1f2937', formatter: function (val) { return val; } },
                                    total: { show: true, label: currentLocale === 'ar' ? 'المجموع' : 'Total', fontSize: '12px', color: '#6b7280', formatter: function (w) { return totalSessions; } }
                                }
                            }
                        }
                    },
                    tooltip: {
                        y: { formatter: function (val) { return val + ' ' + (currentLocale === 'ar' ? 'جلسة' : 'sessions'); } },
                        theme: 'dark'
                    },
                    responsive: [
                        { breakpoint: 768, options: { chart: { height: 160 }, plotOptions: { pie: { donut: { size: '70%' } } } } },
                        { breakpoint: 480, options: { chart: { height: 140 }, plotOptions: { pie: { donut: { size: '75%' } } } } }
                    ]
                };

                const pieElement = document.querySelector("#sessionTypesPieChart");
                if (pieElement && typeof ApexCharts !== 'undefined') {
                    if (pieElement.chart) pieElement.chart.destroy();
                    const pieChart = new ApexCharts(pieElement, pieOptions);
                    pieChart.render();
                    pieElement.chart = pieChart;
                }
            }

            function renderCharts() {
                const translatedMonths = monthsData.map(translateMonth);

                // Sessions Chart
                const sessionsChartOptions = {
                    series: [{ name: currentLocale === 'ar' ? 'عدد الجلسات' : 'Number of Sessions', data: sessionsData }],
                    chart: { type: 'line', height: 300, toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: true, speed: 500 }, background: 'transparent', fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif' },
                    stroke: { curve: 'smooth', width: 3, colors: ['#7c3aed'] },
                    markers: { size: 6, hover: { size: 10 }, colors: ['#7c3aed'], strokeColors: '#ffffff', strokeWidth: 2, radius: 6 },
                    tooltip: { enabled: true, shared: false, intersect: true, followCursor: true, theme: 'dark', style: { fontSize: '12px' }, y: { formatter: (value) => value, title: { formatter: () => currentLocale === 'ar' ? 'عدد الجلسات: ' : 'Sessions: ' } } },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: { categories: translatedMonths, title: { text: currentLocale === 'ar' ? 'الشهر' : 'Month', style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' } }, labels: { rotate: -35, style: { fontSize: '11px' } } },
                    yaxis: { title: { text: currentLocale === 'ar' ? 'عدد الجلسات' : 'Number of Sessions', style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' } }, labels: { formatter: (value) => Math.round(value) } },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 100], colorStops: [{ offset: 0, color: '#7c3aed', opacity: 0.3 }, { offset: 100, color: '#7c3aed', opacity: 0.05 }] } },
                    legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 250 }, markers: { size: 5 }, xaxis: { labels: { rotate: -45, style: { fontSize: '9px' } } } } }, { breakpoint: 480, options: { chart: { height: 220 } } }]
                };

                const sessionsChartElement = document.querySelector("#sessionsChart");
                if (sessionsChartElement && typeof ApexCharts !== 'undefined') {
                    if (sessionsChartElement.chart) sessionsChartElement.chart.destroy();
                    const sessionsChart = new ApexCharts(sessionsChartElement, sessionsChartOptions);
                    sessionsChart.render();
                    sessionsChartElement.chart = sessionsChart;
                }

                // Earnings Chart
                const earningsChartOptions = {
                    series: [{ name: currentLocale === 'ar' ? 'الأرباح (دولار)' : 'Earnings (USD)', data: earningsData }],
                    chart: { type: 'bar', height: 300, toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: true, speed: 500 }, background: 'transparent', fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif' },
                    plotOptions: { bar: { borderRadius: 8, borderRadiusApplication: 'end', columnWidth: '60%' } },
                    colors: ['#10b981'],
                    tooltip: { enabled: true, theme: 'dark', style: { fontSize: '12px' }, y: { formatter: (value) => '$' + value.toFixed(2), title: { formatter: () => currentLocale === 'ar' ? 'الأرباح: ' : 'Earnings: ' } } },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: { categories: translatedMonths, title: { text: currentLocale === 'ar' ? 'الشهر' : 'Month', style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' } }, labels: { rotate: -35, style: { fontSize: '11px' } } },
                    yaxis: { title: { text: currentLocale === 'ar' ? 'الأرباح (دولار)' : 'Earnings (USD)', style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' } }, labels: { formatter: (value) => '$' + value } },
                    legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 250 }, plotOptions: { bar: { borderRadius: 6, columnWidth: '70%' } }, xaxis: { labels: { rotate: -45, style: { fontSize: '9px' } } } } }, { breakpoint: 480, options: { chart: { height: 220 } } }]
                };

                const earningsChartElement = document.querySelector("#earningsChart");
                if (earningsChartElement && typeof ApexCharts !== 'undefined') {
                    if (earningsChartElement.chart) earningsChartElement.chart.destroy();
                    const earningsChart = new ApexCharts(earningsChartElement, earningsChartOptions);
                    earningsChart.render();
                    earningsChartElement.chart = earningsChart;
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    renderPieChart();
                    renderCharts();
                }, 300);

                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) sidebarToggle.addEventListener('click', function () { setTimeout(function () { renderPieChart(); renderCharts(); }, 300); });

                const mobileToggle = document.getElementById('mobileSidebarToggle');
                if (mobileToggle) mobileToggle.addEventListener('click', function () { setTimeout(function () { renderPieChart(); renderCharts(); }, 350); });

                let resizeTimer;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function () { renderPieChart(); renderCharts(); }, 250);
                });
            });
        </script>
    @endpush

@endsection