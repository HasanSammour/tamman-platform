{{-- resources/views/specialist/clients/show.blade.php --}}
@extends('layouts.app')

@section('title', __('Client Profile') . ' - ' . __('Tamman'))

@section('page-title', __('Client Profile'))

@section('content')
    <div class="client-profile-container">
        <!-- Back Button -->
        <div class="back-nav">
            <a href="{{ route('specialist.clients.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Clients') }}
            </a>
        </div>

        <!-- Client Header Card -->
        <div class="client-header-card">
            <div class="client-avatar-large">
                @php
                    $clientImage = $client->getProfileImageUrl();
                    $clientInitial = mb_substr($client->name, 0, 1, 'UTF-8');
                @endphp
                @if($clientImage)
                    <img src="{{ $clientImage }}" alt="{{ $client->name }}">
                @else
                    <div class="avatar-placeholder-large">{{ $clientInitial }}</div>
                @endif
            </div>
            <div class="client-header-info">
                <h1>{{ $client->name }}</h1>
                <div class="client-meta">
                    <span><i class="fas fa-envelope"></i> {{ $client->email }}</span>
                    @if($client->phone)
                        <span><i class="fas fa-phone"></i> {{ $client->phone }}</span>
                    @endif
                    @if($client->gender)
                        <span><i class="fas fa-venus-mars"></i> {{ __(ucfirst($client->gender)) }}</span>
                    @endif
                    @if($client->date_of_birth)
                        <span><i class="fas fa-birthday-cake"></i> {{ $client->date_of_birth->translatedFormat('M d, Y') }}
                            ({{ $client->age }} {{ __('years') }})</span>
                    @endif
                    <span><i class="fas fa-calendar-alt"></i> {{ __('Joined') }}:
                        {{ $client->created_at->translatedFormat('M d, Y') }}</span>
                </div>
                @if($rating)
                    <div class="client-rating">
                        <span class="rating-label">{{ __('Your Rating') }}:</span>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $rating->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        @if($rating->comment)
                            <span class="rating-comment">"{{ $rating->comment }}"</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="client-header-actions">
                <a href="{{ route('chat.index', ['user' => $client->id]) }}" class="btn-message">
                    <i class="fas fa-comment-dots"></i> {{ __('Send Message') }}
                </a>
                <a href="{{ route('specialist.treatment-plans.create', ['patient' => $client->id]) }}"
                    class="btn-treatment">
                    <i class="fas fa-tasks"></i> {{ __('Create Treatment Plan') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['completed_sessions']) }}</h3>
                    <p>{{ __('Completed') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-star"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_points']) }}</h3>
                    <p>{{ __('Points') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-chart-line"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['average_mood'], 1) }}/10</h3>
                    <p>{{ __('Avg Mood') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon indigo"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['total_tests']) }}</h3>
                    <p>{{ __('Tests Taken') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon teal"><i class="fas fa-tasks"></i></div>
                <div class="stat-data">
                    <h3>{{ number_format($stats['active_treatment_plans']) }}</h3>
                    <p>{{ __('Active Plans') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="profile-tabs">
            <button class="tab-btn active" data-tab="overview">
                <i class="fas fa-chart-pie"></i> <span>{{ __('Overview') }}</span>
            </button>
            <button class="tab-btn" data-tab="sessions">
                <i class="fas fa-calendar-alt"></i> <span>{{ __('Sessions') }}</span>
            </button>
            <button class="tab-btn mood-tab-desktop" data-tab="mood">
                <i class="fas fa-chart-line"></i> <span>{{ __('Mood Trends') }}</span>
            </button>
            <button class="tab-btn" data-tab="tests">
                <i class="fas fa-clipboard-list"></i> <span>{{ __('Test Results') }}</span>
            </button>
            <button class="tab-btn" data-tab="treatment">
                <i class="fas fa-tasks"></i> <span>{{ __('Treatment Plans') }}</span>
            </button>
        </div>

        <!-- Tab 1: Overview -->
        <div class="tab-content active" id="tab-overview">
            <div class="info-card full-width">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> {{ __('Recent Activity') }}</h3>
                </div>
                <div class="card-body" id="recentActivity">
                    <div class="loading-spinner-small"></div>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-bolt"></i> {{ __('Quick Actions') }}</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('specialist.treatment-plans.create', ['patient' => $client->id]) }}"
                            class="quick-action">
                            <i class="fas fa-tasks"></i>
                            <span>{{ __('Create Plan') }}</span>
                        </a>
                        <a href="{{ route('chat.index', ['user' => $client->id]) }}" class="quick-action">
                            <i class="fas fa-comment-dots"></i>
                            <span>{{ __('Send Message') }}</span>
                        </a>
                        <a href="{{ route('specialist.session-notes.index') }}?client={{ $client->id }}"
                            class="quick-action">
                            <i class="fas fa-notes-medical"></i>
                            <span>{{ __('Session Notes') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Sessions -->
        <div class="tab-content" id="tab-sessions">
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> {{ __('Session History') }}</h3>
                    <div class="session-filters">
                        <select id="sessionTypeFilter" class="filter-select-sm">
                            <option value="all">{{ __('All Types') }}</option>
                            <option value="video">{{ __('Video') }}</option>
                            <option value="audio">{{ __('Audio') }}</option>
                            <option value="text">{{ __('Text') }}</option>
                        </select>
                        <select id="sessionStatusFilter" class="filter-select-sm">
                            <option value="all">{{ __('All Status') }}</option>
                            <option value="scheduled">{{ __('Scheduled') }}</option>
                            <option value="completed">{{ __('Completed') }}</option>
                            <option value="cancelled">{{ __('Cancelled') }}</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Desktop Table View -->
                    <div class="table-responsive-wrapper">
                        <div class="table-responsive">
                            <table class="sessions-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Time') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Rating') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="sessionsTableBody">
                                    <tr class="loading-row">
                                        <td colspan="6">
                                            <div class="loading-spinner-small"></div>
                                            <p>{{ __('Loading sessions...') }}</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Cards View -->
                    <div id="sessionsCardsContainer" class="sessions-cards-container" style="display: none;">
                        <div class="loading-spinner-small"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Mood Trends (Desktop Only) -->
        <div class="tab-content" id="tab-mood">
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> {{ __('Mood Trends (Last 30 Days)') }}</h3>
                </div>
                <div class="card-body">
                    <div id="moodChartContainer" class="mood-chart-container">
                        <div id="moodChart"></div>
                    </div>
                    <div class="mood-stats" id="moodStats"></div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Test Results -->
        <div class="tab-content" id="tab-tests">
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list"></i> {{ __('Test Results') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Desktop Table View -->
                    <div class="table-responsive-wrapper">
                        <div class="table-responsive">
                            <table class="tests-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Test') }}</th>
                                        <th>{{ __('Score') }}</th>
                                        <th>{{ __('Level') }}</th>
                                        <th>{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="testsTableBody">
                                    <tr class="loading-row">
                                        <td colspan="4">
                                            <div class="loading-spinner-small"></div>
                                            <p>{{ __('Loading tests...') }}</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Cards View -->
                    <div id="testsCardsContainer" class="tests-cards-container" style="display: none;">
                        <div class="loading-spinner-small"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 5: Treatment Plans -->
        <div class="tab-content" id="tab-treatment">
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-tasks"></i> {{ __('Treatment Plans') }}</h3>
                    <a href="{{ route('specialist.treatment-plans.create', ['patient' => $client->id]) }}"
                        class="btn-add-plan">
                        <i class="fas fa-plus"></i> {{ __('New Plan') }}
                    </a>
                </div>
                <div class="card-body">
                    <div id="treatmentPlansList" class="treatment-plans-grid">
                        <div class="loading-spinner-small"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .client-profile-container {
                max-width: 1400px;
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

            .client-header-card {
                background: white;
                border-radius: 24px;
                padding: 25px;
                display: flex;
                gap: 25px;
                flex-wrap: wrap;
                margin-bottom: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .client-avatar-large img,
            .avatar-placeholder-large {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder-large {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                font-weight: 600;
                color: white;
            }

            .client-header-info {
                flex: 1;
            }

            .client-header-info h1 {
                font-size: 1.5rem;
                margin: 0 0 8px;
                color: #1f2937;
            }

            .client-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                margin-bottom: 10px;
            }

            .client-meta span {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 0.8rem;
                color: #6b7280;
            }

            .client-meta i {
                color: #7c3aed;
            }

            .client-rating {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-top: 8px;
            }

            .rating-label {
                font-size: 0.75rem;
                color: #6b7280;
            }

            .stars {
                color: #fbbf24;
                font-size: 0.8rem;
            }

            .rating-comment {
                font-size: 0.75rem;
                color: #10b981;
                font-style: italic;
            }

            .client-header-actions {
                display: flex;
                gap: 12px;
                align-items: flex-start;
            }

            .btn-message,
            .btn-treatment {
                padding: 8px 18px;
                border-radius: 40px;
                font-size: 0.75rem;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-message {
                background: #ede9fe;
                color: #7c3aed;
            }

            .btn-message:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            .btn-treatment {
                background: #10b981;
                color: white;
            }

            .btn-treatment:hover {
                background: #059669;
                color: white;
                transform: translateY(-2px);
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 15px;
                margin-bottom: 25px;
            }

            .stat-card {
                background: white;
                border-radius: 16px;
                padding: 15px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 45px;
                height: 45px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stat-icon i {
                font-size: 1.2rem;
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

            .stat-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .stat-icon.indigo {
                background: linear-gradient(135deg, #6366f1, #4f46e5);
            }

            .stat-icon.teal {
                background: linear-gradient(135deg, #14b8a6, #0d9488);
            }

            .stat-data h3 {
                font-size: 1.2rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-data p {
                font-size: 0.65rem;
                color: #6b7280;
                margin: 0;
            }

            .profile-tabs {
                display: flex;
                justify-content: center;
                gap: 5px;
                margin-bottom: 25px;
                border-bottom: 1px solid #e5e7eb;
                flex-wrap: wrap;
            }

            .tab-btn {
                padding: 10px 20px;
                background: none;
                border: none;
                font-size: 0.8rem;
                font-weight: 500;
                color: #6b7280;
                cursor: pointer;
                transition: all 0.3s ease;
                border-radius: 40px 40px 0 0;
                position: relative;
            }

            .tab-btn i {
                margin-right: 8px;
                font-size: 0.8rem;
            }

            body.rtl .tab-btn i {
                margin-right: 0;
                margin-left: 8px;
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

            /* Hide mood tab on mobile */
            @media (max-width: 768px) {
                .mood-tab-desktop {
                    display: none !important;
                }
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

            .info-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
            }

            .info-card.full-width {
                grid-column: span 2;
            }

            .card-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .card-header h3 {
                margin: 0;
                font-size: 0.95rem;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1f2937;
            }

            .card-header h3 i {
                color: #7c3aed;
            }

            .card-body {
                padding: 20px;
            }

            /* Mood Chart Container - No scrollbars */
            .mood-chart-container {
                width: 100%;
                overflow: visible;
                position: relative;
            }

            #moodChart {
                width: 100%;
                min-height: 350px;
            }

            .quick-actions-grid {
                display: flex;
                flex-direction: row;
                gap: 15px;
                justify-content: center;
            }

            .quick-action {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                padding: 12px 16px;
                background: #f9fafb;
                border-radius: 12px;
                text-decoration: none;
                transition: all 0.3s ease;
                font-size: 0.75rem;
                color: #374151;
            }

            .quick-action:hover {
                background: #ede9fe;
                transform: translateY(-3px);
                color: #7c3aed;
            }

            .quick-action i {
                width: 28px;
                height: 28px;
                background: #ede9fe;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.7rem;
                transition: all 0.3s ease;
            }

            .quick-action:hover i {
                background: #7c3aed;
                color: white;
            }

            .btn-add-plan {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 5px 14px;
                border-radius: 40px;
                font-size: 0.7rem;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
            }

            .btn-add-plan:hover {
                background: #6d28d9;
                color: white;
                transform: translateY(-2px);
            }

            .session-filters {
                display: flex;
                gap: 10px;
            }

            .filter-select-sm {
                padding: 5px 10px;
                border: 1px solid #e5e7eb;
                border-radius: 20px;
                font-size: 0.7rem;
                background: white;
            }

            /* Desktop Table Styles */
            .table-responsive-wrapper {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive {
                min-width: 600px;
                width: 100%;
            }

            .sessions-table,
            .tests-table {
                width: 100%;
                border-collapse: collapse;
            }

            .sessions-table th,
            .sessions-table td,
            .tests-table th,
            .tests-table td {
                padding: 10px 14px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
            }

            body.rtl .sessions-table th,
            body.rtl .sessions-table td,
            body.rtl .tests-table th,
            body.rtl .tests-table td {
                text-align: right;
            }

            .sessions-table th,
            .tests-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.7rem;
                color: #374151;
            }

            .sessions-table td,
            .tests-table td {
                font-size: 0.75rem;
                color: #4b5563;
            }

            .type-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 3px 8px;
                border-radius: 20px;
                font-size: 0.6rem;
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

            .session-row.clickable {
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .session-row.clickable:hover {
                background: #f5f3ff;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 8px;
                border-radius: 20px;
                font-size: 0.6rem;
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

            .action-buttons-sm {
                display: flex;
                gap: 6px;
            }

            .btn-icon-sm {
                width: 28px;
                height: 28px;
                background: #f3f4f6;
                border-radius: 6px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #6b7280;
                transition: all 0.3s ease;
                font-size: 0.7rem;
            }

            .btn-icon-sm:hover {
                background: #ede9fe;
                color: #7c3aed;
                transform: translateY(-2px);
            }

            /* Mobile Cards Styles */
            .sessions-cards-container,
            .tests-cards-container {
                display: grid;
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .session-card {
                background: white;
                border-radius: 16px;
                padding: 0;
                transition: all 0.3s ease;
                border: 1px solid #eef2ff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                cursor: pointer;
                overflow: hidden;
            }

            .session-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
                border-color: #c4b5fd;
            }

            .session-card-header {
                background: linear-gradient(135deg, #f8fafc, #f1f5f9);
                padding: 14px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                border-bottom: 1px solid #e2e8f0;
            }

            .session-card-date {
                font-weight: 700;
                color: #7c3aed;
                font-size: 0.8rem;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .session-card-date i {
                font-size: 0.7rem;
            }

            .session-card-time {
                font-size: 0.7rem;
                color: #64748b;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                background: white;
                padding: 3px 8px;
                border-radius: 20px;
            }

            .session-card-time i {
                font-size: 0.6rem;
            }

            .session-card-body {
                padding: 14px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 12px;
                background: white;
            }

            .session-card-info {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .session-card-type {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 600;
                width: fit-content;
            }

            .session-card-type.video {
                background: #ede9fe;
                color: #7c3aed;
            }

            .session-card-type.audio {
                background: #d1fae5;
                color: #059669;
            }

            .session-card-type.text {
                background: #fef3c7;
                color: #d97706;
            }

            .session-card-rating {
                display: inline-flex;
                align-items: center;
                gap: 2px;
                font-size: 0.7rem;
            }

            .session-card-status {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 500;
            }

            .session-card-status.scheduled {
                background: #ede9fe;
                color: #7c3aed;
            }

            .session-card-status.completed {
                background: #d1fae5;
                color: #065f46;
            }

            .session-card-status.cancelled {
                background: #fee2e2;
                color: #991b1b;
            }

            .session-card-footer {
                padding: 12px 16px;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                background: #fafcff;
                border-top: 1px solid #eef2ff;
            }

            .session-card-footer .btn-icon-sm {
                background: white;
                border: 1px solid #e2e8f0;
                padding: 5px 12px;
                width: auto;
                gap: 6px;
                font-size: 0.65rem;
            }

            .session-card-footer .btn-icon-sm:hover {
                background: #ede9fe;
                border-color: #c4b5fd;
            }

            .test-card {
                background: white;
                border-radius: 16px;
                padding: 14px 16px;
                transition: all 0.3s ease;
                border: 1px solid #eef2ff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .test-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
                border-color: #c4b5fd;
            }

            .test-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
                flex-wrap: wrap;
                gap: 8px;
            }

            .test-card-title {
                font-weight: 700;
                color: #1f2937;
                font-size: 0.85rem;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .test-card-title i {
                color: #7c3aed;
                font-size: 0.75rem;
            }

            .test-card-date {
                font-size: 0.65rem;
                color: #64748b;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .test-card-body {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
                padding-top: 8px;
                border-top: 1px solid #f0f0f0;
            }

            .test-card-score {
                font-weight: 700;
                color: #7c3aed;
                font-size: 0.9rem;
            }

            .test-card-level {
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.6rem;
                background: #fef3c7;
                color: #d97706;
                font-weight: 600;
            }

            /* Modern Treatment Plans Cards */
            .treatment-plans-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                min-height: 200px;
            }

            .plan-card {
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                border-radius: 20px;
                padding: 20px;
                transition: all 0.3s ease;
                border: 1px solid rgba(124, 58, 237, 0.1);
                position: relative;
                overflow: hidden;
                cursor: pointer;
            }

            .plan-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #7c3aed, #a78bfa, #7c3aed);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .plan-card:hover::before {
                opacity: 1;
            }

            .plan-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 28px rgba(124, 58, 237, 0.15);
                border-color: rgba(124, 58, 237, 0.3);
            }

            .plan-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 15px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .plan-title-section {
                flex: 1;
            }

            .plan-title {
                font-weight: 700;
                font-size: 1rem;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 5px;
            }

            .plan-title i {
                color: #7c3aed;
                font-size: 1rem;
            }

            .plan-description {
                font-size: 0.7rem;
                color: #6b7280;
                line-height: 1.4;
                margin-top: 4px;
            }

            .plan-status {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.65rem;
                font-weight: 600;
                white-space: nowrap;
            }

            .plan-status.active {
                background: #d1fae5;
                color: #065f46;
            }

            .plan-status.completed {
                background: #e0e7ff;
                color: #3730a3;
            }

            .plan-status.pending {
                background: #fed7aa;
                color: #9a3412;
            }

            .plan-progress-section {
                margin: 16px 0;
            }

            .plan-progress-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
                font-size: 0.7rem;
                color: #6b7280;
            }

            .plan-progress-bar {
                height: 8px;
                background: #e5e7eb;
                border-radius: 10px;
                overflow: hidden;
            }

            .plan-progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #7c3aed, #a78bfa);
                border-radius: 10px;
                transition: width 0.5s ease;
                position: relative;
            }

            .plan-progress-fill::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(90deg, rgba(255, 255, 255, 0.2), transparent);
                border-radius: 10px;
            }

            .plan-stats {
                display: flex;
                justify-content: space-between;
                gap: 15px;
                margin-top: 16px;
                padding-top: 12px;
                border-top: 1px solid #e5e7eb;
            }

            .plan-stat-item {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.7rem;
                color: #4b5563;
            }

            .plan-stat-item i {
                color: #7c3aed;
                font-size: 0.7rem;
            }

            .plan-footer {
                margin-top: 16px;
                display: flex;
                justify-content: flex-end;
            }

            .plan-view-btn {
                padding: 6px 14px;
                background: #ede9fe;
                color: #7c3aed;
                border-radius: 20px;
                text-decoration: none;
                font-size: 0.7rem;
                font-weight: 500;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .plan-view-btn:hover {
                background: #7c3aed;
                color: white;
                transform: translateY(-2px);
            }

            /* Activity List */
            .activity-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .activity-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .activity-item:last-child {
                border-bottom: none;
            }

            .activity-item i {
                width: 35px;
                height: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
            }

            .activity-detail {
                flex: 1;
            }

            .activity-title {
                font-size: 0.8rem;
                font-weight: 500;
                color: #1f2937;
            }

            .activity-value {
                font-size: 0.7rem;
                color: #6b7280;
            }

            .activity-date {
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .loading-spinner-small {
                width: 30px;
                height: 30px;
                border: 3px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 20px auto;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .empty-state-small {
                text-align: center;
                padding: 40px 20px;
            }

            .empty-state-small i {
                font-size: 2.5rem;
                color: #c4b5fd;
                margin-bottom: 10px;
                display: block;
            }

            .mood-stats {
                margin-top: 20px;
                text-align: center;
            }

            /* Responsive Breakpoints */
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 992px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .treatment-plans-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .client-profile-container {
                    padding: 15px;
                }

                .client-header-card {
                    flex-direction: column;
                    text-align: center;
                }

                .client-meta {
                    justify-content: center;
                }

                .client-rating {
                    justify-content: center;
                }

                .client-header-actions {
                    justify-content: center;
                }

                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 12px;
                }

                .profile-tabs {
                    justify-content: center;
                    gap: 2px;
                }

                .tab-btn {
                    padding: 8px 12px;
                    font-size: 0.7rem;
                }

                .tab-btn i {
                    font-size: 0.7rem;
                    margin-right: 4px;
                }

                .tab-btn span {
                    font-size: 0.7rem;
                }

                .session-filters {
                    width: 100%;
                    justify-content: space-between;
                }

                .filter-select-sm {
                    font-size: 0.65rem;
                    padding: 4px 8px;
                }

                .quick-actions-grid {
                    flex-direction: column;
                }

                .quick-action {
                    justify-content: center;
                    padding: 10px 12px;
                    font-size: 0.7rem;
                }

                .quick-action i {
                    width: 24px;
                    height: 24px;
                    font-size: 0.6rem;
                }

                /* Hide desktop tables, show mobile cards */
                .table-responsive-wrapper {
                    display: none;
                }

                .sessions-cards-container,
                .tests-cards-container {
                    display: grid !important;
                }
            }

            @media (min-width: 769px) {

                .sessions-cards-container,
                .tests-cards-container {
                    display: none !important;
                }

                .table-responsive-wrapper {
                    display: block;
                }
            }

            @media (max-width: 480px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .treatment-plans-grid {
                    grid-template-columns: 1fr;
                }

                .client-header-actions {
                    flex-direction: column;
                    width: 100%;
                }

                .btn-message,
                .btn-treatment {
                    width: 100%;
                    justify-content: center;
                    font-size: 0.7rem;
                    padding: 8px 12px;
                }

                .profile-tabs {
                    gap: 0;
                }

                .tab-btn {
                    padding: 6px 8px;
                }

                .tab-btn i {
                    margin-right: 3px;
                }

                .tab-btn span {
                    font-size: 0.65rem;
                }
            }

            body.rtl .client-header-card {
                text-align: right;
            }

            body.rtl .client-meta {
                flex-direction: row;
            }

            body.rtl .stats-grid {
                direction: rtl;
            }

            body.rtl .quick-action {
                flex-direction: row;
            }

            body.rtl .plan-header {
                flex-direction: row;
            }

            body.rtl .plan-stats {
                flex-direction: row;
            }

            body.rtl .activity-item {
                flex-direction: row;
            }

            body.rtl .session-card-header,
            body.rtl .test-card-header {
                flex-direction: row;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            const clientId = {{ $client->id }};
            const currentLocale = '{{ app()->getLocale() }}';
            let moodChart = null;
            let chartDataLoaded = false;

            // Tab Switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const tabId = this.dataset.tab;
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    const activeTab = document.getElementById(`tab-${tabId}`);
                    if (activeTab) activeTab.classList.add('active');

                    if (tabId === 'sessions') loadSessions();
                    if (tabId === 'mood' && window.innerWidth >= 769) {
                        setTimeout(() => loadMoodChart(), 150);
                    }
                    if (tabId === 'tests') loadTests();
                    if (tabId === 'treatment') loadTreatmentPlans();
                });
            });

            // Load Recent Activity
            async function loadRecentActivity() {
                const container = document.getElementById('recentActivity');
                if (!container) return;
                container.innerHTML = '<div class="loading-spinner-small"></div>';

                try {
                    const response = await fetch(`/specialist/clients/${clientId}/activity`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success && data.activity && data.activity.length > 0) {
                        let html = '<div class="activity-list">';
                        data.activity.forEach(act => {
                            let icon = act.type === 'mood' ? 'fa-smile' : (act.type === 'session' ? 'fa-calendar-check' : 'fa-clipboard-list');
                            let color = act.type === 'mood' ? '#f59e0b' : (act.type === 'session' ? '#7c3aed' : '#10b981');
                            html += `
                                        <div class="activity-item">
                                            <i class="fas ${icon}" style="color: ${color}; background: ${color}10; padding: 8px; border-radius: 10px;"></i>
                                            <div class="activity-detail">
                                                <div class="activity-title">${escapeHtml(act.title)}</div>
                                                <div class="activity-value">${escapeHtml(act.value)}</div>
                                            </div>
                                            <div class="activity-date">${new Date(act.date).toLocaleDateString()}</div>
                                        </div>
                                    `;
                        });
                        html += '</div>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<div class="empty-state-small"><i class="fas fa-history"></i><p>{{ __("No recent activity") }}</p></div>';
                    }
                } catch (error) {
                    console.error('Error loading activity:', error);
                    container.innerHTML = '<div class="empty-state-small"><i class="fas fa-exclamation-triangle"></i><p>{{ __("Error loading activity") }}</p></div>';
                }
            }

            // Load Sessions
            async function loadSessions() {
                const tbody = document.getElementById('sessionsTableBody');
                const cardsContainer = document.getElementById('sessionsCardsContainer');
                if (!tbody || !cardsContainer) return;

                tbody.innerHTML = `<td><td colspan="6"><div class="loading-spinner-small"></div>`;
                cardsContainer.innerHTML = '<div class="loading-spinner-small"></div>';

                try {
                    const response = await fetch(`/specialist/clients/${clientId}/sessions`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success && data.sessions && data.sessions.length > 0) {
                        currentSessionsData = data.sessions;
                        renderSessionsTable(currentSessionsData);
                        renderSessionsCards(currentSessionsData);
                    } else {
                        tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state-small"><i class="fas fa-calendar-alt"></i><p>{{ __('No sessions found') }}</p></div>`;
                        cardsContainer.innerHTML = '<div class="empty-state-small"><i class="fas fa-calendar-alt"></i><p>{{ __("No sessions found") }}</p></div>';
                    }
                } catch (error) {
                    console.error('Error loading sessions:', error);
                    tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state-small"><i class="fas fa-exclamation-triangle"></i><p>{{ __('Error loading sessions') }}</p></div>`;
                    cardsContainer.innerHTML = '<div class="empty-state-small"><i class="fas fa-exclamation-triangle"></i><p>{{ __("Error loading sessions") }}</p></div>';
                }
            }

            function renderSessionsTable(sessions) {
                const tbody = document.getElementById('sessionsTableBody');
                if (!tbody) return;

                tbody.innerHTML = sessions.map(session => `
                            <tr class="session-row clickable" data-href="/specialist/sessions/${session.id}">
                                <td>${session.date_formatted || '—'}</td>
                                <td>${session.time_formatted || '—'}</td>
                                <td><span class="type-badge ${session.type}"><i class="fas ${session.type_icon}"></i> ${capitalize(session.type)}</span></td>
                                <td>${session.status_badge || '—'}</td>
                                <td>${session.rating ? getRatingStars(session.rating) : '—'}</td>
                                <td>
                                    <div class="action-buttons-sm">
                                        <a href="/specialist/session-notes/${session.id}" class="btn-icon-sm" title="{{ __('Session Notes') }}" onclick="event.stopPropagation()">
                                            <i class="fas fa-notes-medical"></i>
                                        </a>
                                        ${session.can_join ? `<a href="/specialist/sessions/${session.id}/join" class="btn-icon-sm" title="{{ __('Join Session') }}" target="_blank" onclick="event.stopPropagation()"><i class="fas fa-video"></i></a>` : ''}
                                    </div>
                                </td>
                            </table>
                        `).join('');

                document.querySelectorAll('.session-row.clickable').forEach(row => {
                    row.addEventListener('click', function (e) {
                        if (e.target.closest('.action-buttons-sm')) return;
                        window.location.href = this.dataset.href;
                    });
                });
            }

            function renderSessionsCards(sessions) {
                const container = document.getElementById('sessionsCardsContainer');
                if (!container) return;

                container.innerHTML = sessions.map(session => {
                    let statusClass = '';
                    let statusText = '';
                    if (session.status === 'scheduled') { statusClass = 'scheduled'; statusText = 'Scheduled'; }
                    else if (session.status === 'completed') { statusClass = 'completed'; statusText = 'Completed'; }
                    else if (session.status === 'cancelled') { statusClass = 'cancelled'; statusText = 'Cancelled'; }
                    else { statusClass = 'scheduled'; statusText = 'Scheduled'; }

                    return `
                                <div class="session-card" data-href="/specialist/sessions/${session.id}">
                                    <div class="session-card-header">
                                        <span class="session-card-date"><i class="fas fa-calendar-alt"></i> ${session.date_formatted || '—'}</span>
                                        <span class="session-card-time"><i class="fas fa-clock"></i> ${session.time_formatted || '—'}</span>
                                    </div>
                                    <div class="session-card-body">
                                        <div class="session-card-info">
                                            <span class="session-card-type ${session.type}"><i class="fas ${session.type_icon}"></i> ${capitalize(session.type)}</span>
                                            <div class="session-card-rating">${session.rating ? getRatingStars(session.rating) : '<span style="color:#94a3b8; font-size:0.65rem;">No rating</span>'}</div>
                                        </div>
                                        <div class="session-card-status ${statusClass}"><i class="fas ${session.status === 'completed' ? 'fa-check-circle' : (session.status === 'cancelled' ? 'fa-times-circle' : 'fa-hourglass-half')}"></i> ${statusText}</div>
                                    </div>
                                    <div class="session-card-footer">
                                        <a href="/specialist/session-notes/${session.id}" class="btn-icon-sm" title="{{ __('Session Notes') }}" onclick="event.stopPropagation()">
                                            <i class="fas fa-notes-medical"></i> Notes
                                        </a>
                                        ${session.can_join ? `<a href="/specialist/sessions/${session.id}/join" class="btn-icon-sm" title="{{ __('Join Session') }}" target="_blank" onclick="event.stopPropagation()"><i class="fas fa-video"></i> Join</a>` : ''}
                                    </div>
                                </div>
                            `;
                }).join('');

                document.querySelectorAll('.session-card').forEach(card => {
                    card.addEventListener('click', function (e) {
                        if (e.target.closest('.btn-icon-sm')) return;
                        window.location.href = this.dataset.href;
                    });
                });
            }

            // Load Mood Chart - Fixed: No scrollbars, proper loading state
            async function loadMoodChart() {
                // Only load on desktop
                if (window.innerWidth < 769) return;

                const chartContainer = document.getElementById('moodChart');
                if (!chartContainer) return;

                // Show loading state
                chartContainer.innerHTML = '<div class="loading-spinner-small"></div>';

                try {
                    const response = await fetch(`/specialist/clients/${clientId}/mood`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success && data.labels && data.labels.length > 0) {
                        renderMoodChart(data.labels, data.values, data.average);
                        chartDataLoaded = true;
                    } else {
                        chartContainer.innerHTML = '<div class="empty-state-small"><i class="fas fa-chart-line"></i><p>{{ __('No mood data available') }}</p></div>';
                    }
                } catch (error) {
                    console.error('Error loading mood chart:', error);
                    chartContainer.innerHTML = '<div class="empty-state-small"><i class="fas fa-exclamation-triangle"></i><p>{{ __('Error loading mood data') }}</p></div>';
                }
            }

            function renderMoodChart(labels, values, average) {
                const chartContainer = document.getElementById('moodChart');
                if (!chartContainer) return;

                // Clear container first
                chartContainer.innerHTML = '';

                // Destroy existing chart if it exists
                if (moodChart) {
                    moodChart.destroy();
                    moodChart = null;
                }

                const markerColors = values.map(v => {
                    if (v <= 3) return '#ef4444';
                    if (v <= 5) return '#f59e0b';
                    if (v <= 7) return '#10b981';
                    return '#7c3aed';
                });

                const options = {
                    series: [{
                        name: currentLocale === 'ar' ? 'مستوى المزاج' : 'Mood Level',
                        data: values
                    }],
                    chart: {
                        type: 'line',
                        height: 350,
                        width: '100%',
                        toolbar: { show: false },
                        animations: { enabled: true },
                        background: 'transparent'
                    },
                    stroke: { curve: 'smooth', width: 3, colors: ['#6d28d9'] },
                    markers: { size: 5, hover: { size: 8 }, colors: markerColors, strokeColors: '#fff', strokeWidth: 2 },
                    tooltip: { y: { formatter: (v) => v + '/10' } },
                    xaxis: {
                        categories: labels,
                        labels: { rotate: -35, style: { fontSize: '10px' } }
                    },
                    yaxis: {
                        min: 0,
                        max: 10,
                        tickAmount: 5,
                        title: { text: currentLocale === 'ar' ? 'مستوى المزاج' : 'Mood Level' }
                    },
                    grid: {
                        padding: { left: 10, right: 10 }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 0.3,
                            opacityFrom: 0.4,
                            opacityTo: 0.1
                        }
                    }
                };

                try {
                    moodChart = new ApexCharts(chartContainer, options);
                    moodChart.render();

                    const moodStats = document.getElementById('moodStats');
                    if (moodStats) {
                        moodStats.innerHTML = `
                                    <div class="stat-card" style="display: inline-block; padding: 8px 16px;">
                                        <strong>{{ __('Average Mood') }}: ${average}/10</strong>
                                    </div>
                                `;
                    }
                } catch (error) {
                    console.error('Error rendering chart:', error);
                    chartContainer.innerHTML = '<div class="empty-state-small"><i class="fas fa-chart-line"></i><p>{{ __('Error rendering chart') }}</p></div>';
                }
            }

            // Load Tests
            async function loadTests() {
                const tbody = document.getElementById('testsTableBody');
                const cardsContainer = document.getElementById('testsCardsContainer');
                if (!tbody || !cardsContainer) return;

                tbody.innerHTML = `<tr><td colspan="4"><div class="loading-spinner-small"></div>`;
                cardsContainer.innerHTML = '<div class="loading-spinner-small"></div>';

                try {
                    const response = await fetch(`/specialist/clients/${clientId}/tests`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success && data.tests && data.tests.length > 0) {
                        renderTestsTable(data.tests);
                        renderTestsCards(data.tests);
                    } else {
                        tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state-small"><i class="fas fa-clipboard-list"></i><p>{{ __('No test results found') }}</p></div>`;
                        cardsContainer.innerHTML = '<div class="empty-state-small"><i class="fas fa-clipboard-list"></i><p>{{ __("No test results found") }}</p></div>';
                    }
                } catch (error) {
                    console.error('Error loading tests:', error);
                    tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state-small"><i class="fas fa-exclamation-triangle"></i><p>{{ __('Error loading tests') }}</p></div>`;
                    cardsContainer.innerHTML = '<div class="empty-state-small"><i class="fas fa-exclamation-triangle"></i><p>{{ __("Error loading tests") }}</p></div>';
                }
            }

            function renderTestsTable(tests) {
                const tbody = document.getElementById('testsTableBody');
                if (!tbody) return;

                tbody.innerHTML = tests.map(test => `
                            <tr>
                                <td>${escapeHtml(test.type_name)}</td>
                                <td><strong>${test.score}</strong></td>
                                <td><span class="status-badge" style="background:#fef3c7;color:#d97706;">${test.level_ar}</span></td>
                                <td>${test.date}</td>
                            </tr>
                        `).join('');
            }

            function renderTestsCards(tests) {
                const container = document.getElementById('testsCardsContainer');
                if (!container) return;

                container.innerHTML = tests.map(test => `
                            <div class="test-card">
                                <div class="test-card-header">
                                    <span class="test-card-title"><i class="fas fa-clipboard-list"></i> ${escapeHtml(test.type_name)}</span>
                                    <span class="test-card-date"><i class="fas fa-calendar-alt"></i> ${test.date}</span>
                                </div>
                                <div class="test-card-body">
                                    <span class="test-card-score">Score: ${test.score}</span>
                                    <span class="test-card-level">${test.level_ar}</span>
                                </div>
                            </div>
                        `).join('');
            }

            // Load Treatment Plans
            async function loadTreatmentPlans() {
                const container = document.getElementById('treatmentPlansList');
                if (!container) return;

                container.innerHTML = '<div class="loading-spinner-small" style="grid-column:span 2;"></div>';

                try {
                    const response = await fetch(`/specialist/clients/${clientId}/treatment`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success && data.plans && data.plans.length > 0) {
                        renderTreatmentPlans(data.plans);
                    } else {
                        container.innerHTML = '<div class="empty-state-small" style="grid-column:span 2;"><i class="fas fa-tasks"></i><p>{{ __('No treatment plans yet') }}</p><a href="{{ route("specialist.treatment-plans.create", ["patient" => $client->id]) }}" class="btn-add-plan" style="margin-top:10px;">{{ __("Create First Plan") }}</a></div>';
                    }
                } catch (error) {
                    console.error('Error loading treatment plans:', error);
                    container.innerHTML = '<div class="empty-state-small" style="grid-column:span 2;"><i class="fas fa-exclamation-triangle"></i><p>{{ __('Error loading treatment plans') }}</p></div>';
                }
            }

            function renderTreatmentPlans(plans) {
                const container = document.getElementById('treatmentPlansList');
                if (!container) return;

                container.innerHTML = plans.map(plan => {
                    let statusClass = 'active';
                    let statusText = 'Active';
                    if (plan.status === 'completed') {
                        statusClass = 'completed';
                        statusText = 'Completed';
                    } else if (plan.status === 'pending') {
                        statusClass = 'pending';
                        statusText = 'Pending';
                    }

                    return `
                                <div class="plan-card" onclick="window.location.href='/specialist/treatment-plans/${plan.id}'">
                                    <div class="plan-header">
                                        <div class="plan-title-section">
                                            <div class="plan-title">
                                                <i class="fas fa-clinic-medical"></i>
                                                ${escapeHtml(plan.title)}
                                            </div>
                                            <div class="plan-description">${escapeHtml(plan.description?.substring(0, 80)) || 'No description'}</div>
                                        </div>
                                        <div class="plan-status ${statusClass}">${statusText}</div>
                                    </div>
                                    <div class="plan-progress-section">
                                        <div class="plan-progress-header">
                                            <span>Progress</span>
                                            <span>${plan.progress}%</span>
                                        </div>
                                        <div class="plan-progress-bar">
                                            <div class="plan-progress-fill" style="width: ${plan.progress}%"></div>
                                        </div>
                                    </div>
                                    <div class="plan-stats">
                                        <div class="plan-stat-item">
                                            <i class="fas fa-check-circle"></i>
                                            <span>${plan.tasks?.completed || 0}/${plan.tasks?.total || 0} Tasks</span>
                                        </div>
                                        <div class="plan-stat-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>${plan.created_at || 'Recent'}</span>
                                        </div>
                                    </div>
                                    <div class="plan-footer">
                                        <a href="/specialist/treatment-plans/${plan.id}" class="plan-view-btn" onclick="event.stopPropagation()">
                                            <i class="fas fa-arrow-right"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            `;
                }).join('');
            }

            // Helper Functions
            function capitalize(str) {
                if (!str) return '';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
            }

            function getRatingStars(rating) {
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    stars += i <= rating ? '<i class="fas fa-star" style="color:#fbbf24; font-size:0.7rem;"></i>' : '<i class="far fa-star" style="color:#d1d5db; font-size:0.7rem;"></i>';
                }
                return stars;
            }

            // Filter functionality for sessions
            let currentSessionsData = [];

            async function applyFilters() {
                const typeFilter = document.getElementById('sessionTypeFilter')?.value || 'all';
                const statusFilter = document.getElementById('sessionStatusFilter')?.value || 'all';

                if (currentSessionsData.length === 0) {
                    try {
                        const response = await fetch(`/specialist/clients/${clientId}/sessions`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        if (data.success) currentSessionsData = data.sessions;
                    } catch (error) {
                        return;
                    }
                }

                let filtered = [...currentSessionsData];
                if (typeFilter !== 'all') {
                    filtered = filtered.filter(s => s.type === typeFilter);
                }
                if (statusFilter !== 'all') {
                    filtered = filtered.filter(s => s.status === statusFilter);
                }

                if (filtered.length > 0) {
                    renderSessionsTable(filtered);
                    renderSessionsCards(filtered);
                } else {
                    const tbody = document.getElementById('sessionsTableBody');
                    const cardsContainer = document.getElementById('sessionsCardsContainer');
                    if (tbody) tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state-small"><i class="fas fa-filter"></i><p>{{ __('No sessions match filters') }}</p></div>`;
                    if (cardsContainer) cardsContainer.innerHTML = '<div class="empty-state-small"><i class="fas fa-filter"></i><p>{{ __("No sessions match filters") }}</p></div>';
                }
            }

            // Add filter listeners
            const typeFilter = document.getElementById('sessionTypeFilter');
            const statusFilter = document.getElementById('sessionStatusFilter');
            if (typeFilter) typeFilter.addEventListener('change', applyFilters);
            if (statusFilter) statusFilter.addEventListener('change', applyFilters);

            // Initial load
            loadRecentActivity();
            loadSessions();

            // Only load chart if mood tab is active and on desktop
            if (document.querySelector('.tab-btn.active')?.dataset.tab === 'mood' && window.innerWidth >= 769) {
                setTimeout(() => loadMoodChart(), 200);
            }
        </script>
    @endpush
@endsection