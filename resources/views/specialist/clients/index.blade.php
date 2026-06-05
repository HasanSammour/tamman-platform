{{-- resources/views/specialist/clients/index.blade.php --}}
@extends('layouts.app')

@section('title', __('My Clients') . ' - ' . __('Tamman'))

@section('page-title', __('Client Management Hub'))

@section('content')
    <div class="clients-hub-container">

        <!-- Welcome Section with Quick Guide -->
        <div class="welcome-guide-card animate-slide-down">
            <div class="guide-content">
                <div class="guide-icon">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <div class="guide-text">
                    <h3>{{ __('Your Client Management Hub') }}</h3>
                    <p>{{ __('Here you can view all your clients, track their progress, manage sessions, and create treatment plans.') }}
                    </p>
                </div>
                <button class="btn-guide" id="toggleGuideBtn">
                    <i class="fas fa-lightbulb"></i> {{ __('Quick Guide') }}
                </button>
            </div>

            <!-- Quick Guide Panel (Hidden by default) -->
            <div class="guide-panel" id="guidePanel" style="display: none;">
                <div class="guide-steps">
                    <div class="guide-step">
                        <div class="step-icon">1</div>
                        <div class="step-info">
                            <h4>{{ __('Manage Sessions') }}</h4>
                            <p>{{ __('Go to Sessions tab to view upcoming, past, and cancelled sessions. Join video calls directly from there.') }}
                            </p>
                        </div>
                    </div>
                    <div class="guide-step">
                        <div class="step-icon">2</div>
                        <div class="step-info">
                            <h4>{{ __('Client Profiles') }}</h4>
                            <p>{{ __('Click on any client to view detailed profile with mood trends, test results, and session history.') }}
                            </p>
                        </div>
                    </div>
                    <div class="guide-step">
                        <div class="step-icon">3</div>
                        <div class="step-info">
                            <h4>{{ __('Treatment Plans') }}</h4>
                            <p>{{ __('Create personalized treatment plans with tasks. Patients earn points when completing tasks.') }}
                            </p>
                        </div>
                    </div>
                    <div class="guide-step">
                        <div class="step-icon">4</div>
                        <div class="step-info">
                            <h4>{{ __('Session Notes') }}</h4>
                            <p>{{ __('After each session, add clinical notes. These are private and help you track progress.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up">
                <div class="stat-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_clients']) }}</h3>
                    <p>{{ __('Total Clients') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon green">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['active_clients']) }}</h3>
                    <p>{{ __('Active Clients') }}</p>
                    <small>{{ __('Last 30 days') }}</small>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon blue">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['completed_sessions']) }}</h3>
                    <p>{{ __('Completed Sessions') }}</p>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-icon orange">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['rated_clients']) }}</h3>
                    <p>{{ __('Rated You') }}</p>
                    <small>{{ __('Clients who left reviews') }}</small>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="dashboard-two-columns">

            <!-- Left Column: Upcoming Sessions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-week"></i> {{ __('Upcoming Sessions') }}</h3>
                </div>
                <div class="card-body">
                    @if($upcomingSessions->count() > 0)
                        @foreach($upcomingSessions as $session)
                            @php
                                $clientImage = $session->patient->getProfileImageUrl();
                                $clientInitial = mb_substr($session->patient->name, 0, 1, 'UTF-8');
                                $canJoin = $session->session_datetime <= now()->addMinutes(30) && $session->session_datetime >= now()->subMinutes(30);
                            @endphp
                            <div class="upcoming-session-item">
                                <div class="session-time-badge">
                                    <span class="time">{{ $session->session_datetime->format('h:i A') }}</span>
                                    <span class="date">{{ $session->session_datetime->translatedFormat('M d') }}</span>
                                </div>
                                <div class="client-avatar">
                                    @if($clientImage)
                                        <img src="{{ $clientImage }}" alt="{{ $session->patient->name }}">
                                    @else
                                        <div class="avatar-placeholder">{{ $clientInitial }}</div>
                                    @endif
                                </div>
                                <div class="session-info">
                                    <h4>{{ $session->patient->name }}</h4>
                                    <p><i
                                            class="fas {{ $session->session_type == 'video' ? 'fa-video' : ($session->session_type == 'audio' ? 'fa-phone-alt' : 'fa-comment-dots') }}"></i>
                                        {{ __(ucfirst($session->session_type)) }}</p>
                                </div>
                                <div class="session-actions">
                                    @if($canJoin && $session->session_type != 'text')
                                        <a href="{{ route('specialist.sessions.join', $session->id) }}" class="btn-join-sm"
                                            target="_blank">
                                            <i class="fas fa-video"></i> {{ __('Join') }}
                                        </a>
                                    @endif
                                    <a href="{{ route('specialist.sessions.show', $session->id) }}" class="btn-details-sm"
                                        title="{{ __('View Details') }}">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-week"></i>
                            <p>{{ __('No upcoming sessions') }}</p>
                            <a href="{{ route('specialist.schedule') }}" class="btn-primary-sm">{{ __('Set Availability') }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Recent Clients -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-friends"></i> {{ __('Recent Clients') }}</h3>
                    <!-- REMOVED: View All Button -->
                </div>
                <div class="card-body">
                    @if($recentClients->count() > 0)
                        @foreach($recentClients as $client)
                            @php
                                $clientImage = $client->getProfileImageUrl();
                                $clientInitial = mb_substr($client->name, 0, 1, 'UTF-8');
                                $lastSession = $client->therapySessionsAsPatient->first();
                            @endphp
                            <div class="recent-client-item">
                                <div class="client-avatar">
                                    @if($clientImage)
                                        <img src="{{ $clientImage }}" alt="{{ $client->name }}">
                                    @else
                                        <div class="avatar-placeholder">{{ $clientInitial }}</div>
                                    @endif
                                </div>
                                <div class="client-info">
                                    <h4>{{ $client->name }}</h4>
                                    <p>{{ $client->email }}</p>
                                    @if($lastSession)
                                        <small><i class="fas fa-clock"></i> {{ __('Last session') }}:
                                            {{ Carbon\Carbon::parse($lastSession->session_datetime)->diffForHumans() }}</small>
                                    @endif
                                </div>
                                <div class="client-actions">
                                    <a href="{{ route('specialist.clients.show', $client->id) }}" class="btn-icon"
                                        title="{{ __('View Profile') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('chat.index', $client->id) }}" class="btn-icon"
                                        title="{{ __('Send Message') }}">
                                        <i class="fas fa-comment-dots"></i>
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
        </div>

        <!-- Clients Table Section with Enhanced Responsiveness -->
        <div class="dashboard-card full-width">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> {{ __('All Clients') }}</h3>
                <div class="table-controls">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="{{ __('Search clients...') }}">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Table View (Desktop) -->
                <div class="table-responsive-wrapper">
                    <div class="table-responsive">
                        <table class="clients-table" id="clientsTable">
                            <thead>
                                <tr>
                                    <th data-sort="id" class="sortable">ID <i class="fas fa-sort"></i></th>
                                    <th data-sort="name" class="sortable">{{ __('Client') }} <i class="fas fa-sort"></i>
                                    </th>
                                    <th data-sort="email" class="sortable">{{ __('Email') }} <i class="fas fa-sort"></i>
                                    </th>
                                    <th data-sort="total_sessions">{{ __('Sessions') }}</th>
                                    <th data-sort="completed_sessions">{{ __('Completed') }}</th>
                                    <th data-sort="rating">{{ __('Rating') }}</th>
                                    <th data-sort="last_session">{{ __('Last Session') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="clientsTableBody">
                                <tr class="loading-row">
                                    <td colspan="8">
                                        <div class="loading-spinner"></div>
                                        <p>{{ __('Loading clients...') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Cards View (Mobile - Shown when table is hidden) -->
                <div id="clientsCardsContainer" class="clients-cards-container" style="display: none;">
                    <!-- Cards will be dynamically populated here -->
                </div>

                <div class="table-footer">
                    <div class="pagination-info" id="paginationInfo"></div>
                    <div class="pagination-controls" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .clients-hub-container {
                max-width: 100%;
                margin: 0 auto;
                padding: 20px;
            }

            /* Welcome Guide Card */
            .welcome-guide-card {
                background: linear-gradient(135deg, #1e1b4b, #2e1065);
                border-radius: 24px;
                margin-bottom: 30px;
                overflow: hidden;
                color: white;
                animation: slideDown 0.5s ease;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .guide-content {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 25px 30px;
                flex-wrap: wrap;
            }

            .guide-icon {
                width: 60px;
                height: 60px;
                background: rgba(255, 255, 255, 0.15);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .guide-icon i {
                font-size: 2rem;
                color: #fbbf24;
            }

            .guide-text {
                flex: 1;
            }

            .guide-text h3 {
                font-size: 1.2rem;
                margin-bottom: 5px;
                color: white;
            }

            .guide-text p {
                color: rgba(255, 255, 255, 0.8);
                margin: 0;
            }

            .btn-guide {
                background: rgba(255, 255, 255, 0.15);
                border: none;
                padding: 10px 20px;
                border-radius: 40px;
                color: white;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-guide:hover {
                background: rgba(255, 255, 255, 0.25);
                transform: translateY(-2px);
            }

            .guide-panel {
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding: 20px 30px;
            }

            .guide-steps {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }

            .guide-step {
                display: flex;
                gap: 12px;
            }

            .step-icon {
                width: 32px;
                height: 32px;
                background: #fbbf24;
                color: #1e1b4b;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                flex-shrink: 0;
            }

            .step-info h4 {
                font-size: 0.85rem;
                margin-bottom: 4px;
                color: white;
            }

            .step-info p {
                font-size: 0.7rem;
                color: rgba(255, 255, 255, 0.7);
                margin: 0;
            }

            /* Stats Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 25px;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                animation: fadeInUp 0.5s ease forwards;
                opacity: 0;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stat-icon.purple {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .stat-icon.green {
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .stat-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon i {
                font-size: 1.4rem;
                color: white;
            }

            .stat-info h3 {
                font-size: 1.6rem;
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
                font-size: 0.6rem;
                color: #9ca3af;
            }

            /* Dashboard Two Columns */
            .dashboard-two-columns {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 25px;
                margin-bottom: 25px;
            }

            .dashboard-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .dashboard-card.full-width {
                grid-column: span 2;
            }

            .card-header {
                padding: 18px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .card-header h3 {
                margin: 0;
                font-size: 1rem;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1f2937;
            }

            .view-all {
                font-size: 0.75rem;
                color: #7c3aed;
                text-decoration: none;
            }

            .card-body {
                padding: 20px;
            }

            /* Upcoming Session Item */
            .upcoming-session-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .upcoming-session-item:last-child {
                border-bottom: none;
            }

            .session-time-badge {
                text-align: center;
                min-width: 60px;
            }

            .session-time-badge .time {
                display: block;
                font-size: 0.8rem;
                font-weight: 700;
                color: #7c3aed;
            }

            .session-time-badge .date {
                display: block;
                font-size: 0.65rem;
                color: #9ca3af;
            }

            /* Client Avatar */
            .client-avatar {
                width: 45px;
                height: 45px;
                flex-shrink: 0;
            }

            .client-avatar img {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder {
                width: 45px;
                height: 45px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1rem;
            }

            .session-info {
                flex: 1;
            }

            .session-info h4 {
                font-size: 0.85rem;
                margin-bottom: 3px;
                color: #1f2937;
            }

            .session-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .session-actions {
                display: flex;
                gap: 8px;
            }

            .btn-join-sm,
            .btn-details-sm {
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .btn-join-sm {
                background: #10b981;
                color: white;
            }

            .btn-join-sm:hover {
                background: #059669;
                transform: translateY(-2px);
                color: white;
            }

            .btn-details-sm {
                background: #f3f4f6;
                color: #6b7280;
            }

            .btn-details-sm:hover {
                background: #e5e7eb;
                color: #7c3aed;
                transform: translateY(-2px);
            }

            /* Recent Client Item */
            .recent-client-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .recent-client-item:last-child {
                border-bottom: none;
            }

            .client-info {
                flex: 1;
            }

            .client-info h4 {
                font-size: 0.85rem;
                margin-bottom: 2px;
                color: #1f2937;
            }

            .client-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .client-info small {
                font-size: 0.6rem;
                color: #9ca3af;
            }

            .client-actions {
                display: flex;
                gap: 8px;
            }

            .btn-icon {
                width: 32px;
                height: 32px;
                background: #f3f4f6;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #6b7280;
                transition: all 0.3s ease;
            }

            .btn-icon:hover {
                background: #ede9fe;
                color: #7c3aed;
                transform: translateY(-2px);
            }

            /* ========== RESPONSIVE TABLE & CARDS STYLES ========== */

            /* Desktop Table Styles */
            .table-responsive-wrapper {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive {
                min-width: 800px;
                width: 100%;
            }

            .clients-table {
                width: 100%;
                border-collapse: collapse;
            }

            .clients-table th,
            .clients-table td {
                padding: 12px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
                vertical-align: middle;
            }

            .clients-table th {
                background: #fafafa;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
            }

            .clients-table td {
                font-size: 0.8rem;
                color: #4b5563;
            }

            .sortable {
                cursor: pointer;
                user-select: none;
                transition: color 0.2s;
            }

            .sortable:hover {
                color: #7c3aed;
            }

            .sortable i {
                margin-left: 5px;
                font-size: 0.7rem;
                color: #9ca3af;
            }

            /* User Avatar Cell */
            .user-avatar-cell {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .avatar-img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1rem;
                overflow: hidden;
                flex-shrink: 0;
            }

            .avatar-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .user-name {
                font-weight: 600;
                color: #1f2937;
            }

            /* Table Controls */
            .table-controls {
                display: flex;
                gap: 12px;
                align-items: center;
            }

            .search-box {
                position: relative;
            }

            .search-box i {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.8rem;
            }

            .search-box input {
                padding: 8px 12px 8px 35px;
                border: 1px solid #e5e7eb;
                border-radius: 30px;
                font-size: 0.8rem;
                width: 200px;
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                gap: 8px;
            }

            /* Table Footer & Pagination */
            .table-footer {
                padding: 16px 20px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
            }

            .pagination-info {
                font-size: 0.75rem;
                color: #6b7280;
            }

            .pagination-controls {
                display: flex;
                gap: 5px;
                flex-wrap: wrap;
            }

            .page-btn {
                min-width: 36px;
                height: 36px;
                padding: 0 10px;
                border: 1px solid #e5e7eb;
                background: white;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.8rem;
            }

            .page-btn:hover:not(:disabled) {
                background: #ede9fe;
                border-color: #7c3aed;
                color: #7c3aed;
            }

            .page-btn.active {
                background: #7c3aed;
                border-color: #7c3aed;
                color: white;
            }

            .page-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            /* Rating Stars */
            .rating-stars {
                white-space: nowrap;
            }

            .rating-stars i {
                margin-right: 2px;
            }

            /* Loading & Empty States */
            .loading-row td {
                text-align: center;
                padding: 60px 20px !important;
            }

            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 3px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
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
                font-size: 0.75rem;
                display: inline-block;
                transition: all 0.3s ease;
            }

            .btn-primary-sm:hover {
                transform: translateY(-2px);
                color: white;
            }

            /* ========== MOBILE CARDS STYLES ========== */
            .clients-cards-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 16px;
                padding: 4px 0;
            }

            .client-card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                border: 1px solid #f0f0f0;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .client-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            }

            .client-card-header {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 16px;
                border-bottom: 1px solid #f0f0f0;
                background: #fafafa;
            }

            .client-card-avatar {
                width: 52px;
                height: 52px;
                flex-shrink: 0;
            }

            .client-card-avatar img,
            .client-card-avatar .avatar-placeholder-card {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
            }

            .avatar-placeholder-card {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
                font-size: 1.3rem;
            }

            .client-card-title {
                flex: 1;
            }

            .client-card-title h4 {
                margin: 0 0 4px 0;
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
            }

            .client-card-title p {
                margin: 0;
                font-size: 0.7rem;
                color: #6b7280;
                word-break: break-all;
            }

            .client-card-body {
                padding: 14px 16px;
            }

            .client-card-info-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #f5f5f5;
            }

            .client-card-info-row:last-child {
                border-bottom: none;
            }

            .info-label {
                font-size: 0.7rem;
                color: #9ca3af;
                font-weight: 500;
            }

            .info-value {
                font-size: 0.8rem;
                color: #374151;
                font-weight: 500;
            }

            .client-card-footer {
                padding: 12px 16px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                background: #fefefe;
            }

            .card-action-btn {
                padding: 8px 16px;
                border-radius: 30px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: all 0.3s ease;
                background: #f3f4f6;
                color: #4b5563;
            }

            .card-action-btn i {
                font-size: 0.75rem;
            }

            .card-action-btn:hover {
                background: #ede9fe;
                color: #7c3aed;
                transform: translateY(-2px);
            }

            /* CHANGE 1: Smaller font size for profile button on mobile */
            @media (max-width: 768px) {
                .card-action-btn {
                    font-size: 0.65rem;
                    padding: 6px 12px;
                }

                .card-action-btn i {
                    font-size: 0.65rem;
                }
            }

            /* Responsive Breakpoints */
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 992px) {
                .dashboard-two-columns {
                    grid-template-columns: 1fr;
                }

                .guide-steps {
                    grid-template-columns: repeat(2, 1fr);
                }

                .dashboard-card.full-width {
                    grid-column: span 1;
                }
            }

            @media (max-width: 768px) {
                .clients-hub-container {
                    padding: 15px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .guide-content {
                    flex-direction: column;
                    text-align: center;
                }

                .guide-steps {
                    grid-template-columns: 1fr;
                }

                .table-controls {
                    width: 100%;
                    justify-content: space-between;
                }

                .search-box input {
                    width: 150px;
                }

                .table-footer {
                    flex-direction: column;
                    text-align: center;
                }

                /* Hide table on mobile, show cards */
                .table-responsive-wrapper {
                    display: none;
                }

                .clients-cards-container {
                    display: grid !important;
                }

                /* CHANGE 3: Make pagination smaller on mobile */
                .pagination-controls {
                    gap: 3px;
                }

                .page-btn {
                    min-width: 30px;
                    height: 30px;
                    padding: 0 6px;
                    font-size: 0.7rem;
                }

                .pagination-info {
                    font-size: 0.7rem;
                }
            }

            @media (min-width: 769px) {
                .clients-cards-container {
                    display: none !important;
                }

                .table-responsive-wrapper {
                    display: block;
                }
            }

            /* Small mobile devices */
            @media (max-width: 480px) {
                .clients-cards-container {
                    grid-template-columns: 1fr;
                }

                .card-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .table-controls {
                    width: 100%;
                }

                .search-box {
                    width: 100%;
                }

                .search-box input {
                    width: 100%;
                }

                /* CHANGE 3: Even smaller pagination for very small screens */
                .page-btn {
                    min-width: 28px;
                    height: 28px;
                    padding: 0 4px;
                    font-size: 0.65rem;
                }
            }

            /* RTL Support */
            body.rtl .search-box i {
                left: auto;
                right: 12px;
            }

            body.rtl .search-box input {
                padding: 8px 35px 8px 12px;
            }

            body.rtl .sortable i {
                margin-left: 0;
                margin-right: 5px;
            }

            body.rtl .user-avatar-cell {
                flex-direction: row;
            }

            body.rtl .rating-stars i {
                margin-right: 0;
                margin-left: 2px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Toggle Guide Panel
            const toggleBtn = document.getElementById('toggleGuideBtn');
            const guidePanel = document.getElementById('guidePanel');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    if (guidePanel.style.display === 'none') {
                        guidePanel.style.display = 'block';
                        toggleBtn.innerHTML = '<i class="fas fa-times"></i> {{ __("Hide Guide") }}';
                    } else {
                        guidePanel.style.display = 'none';
                        toggleBtn.innerHTML = '<i class="fas fa-lightbulb"></i> {{ __("Quick Guide") }}';
                    }
                });
            }

            // DataTable Variables
            let currentPage = 1, perPage = 15, sortField = 'created_at', sortDirection = 'desc';
            let search = '';

            const tableBody = document.getElementById('clientsTableBody');
            const cardsContainer = document.getElementById('clientsCardsContainer');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationControls = document.getElementById('paginationControls');
            const searchInput = document.getElementById('searchInput');
            const baseUrl = '{{ url("/") }}';

            // Sort functionality
            document.querySelectorAll('.sortable').forEach(th => {
                th.addEventListener('click', () => {
                    const field = th.dataset.sort;
                    if (sortField === field) {
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortField = field;
                        sortDirection = 'asc';
                    }
                    document.querySelectorAll('.sortable i').forEach(icon => icon.className = 'fas fa-sort');
                    th.querySelector('i').className = sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                    currentPage = 1;
                    loadClients();
                });
            });

            // Search input
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    search = searchInput.value;
                    currentPage = 1;
                    loadClients();
                });
            }

            async function loadClients() {
                // Show loading state in both table and cards
                tableBody.innerHTML = `<tr class="loading-row"><td colspan="8"><div class="loading-spinner"></div><p>{{ __('Loading clients...') }}</p></tr>`;
                cardsContainer.innerHTML = `<div class="empty-state"><div class="loading-spinner"></div><p>{{ __('Loading clients...') }}</p></div>`;

                try {
                    const url = `/specialist/clients/data?page=${currentPage}&per_page=${perPage}&sort_field=${sortField}&sort_direction=${sortDirection}&search=${encodeURIComponent(search)}`;
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();

                    if (data.success) {
                        renderTable(data.data);
                        renderCards(data.data);
                        renderPagination(data);
                    } else {
                        showError();
                    }
                } catch (error) {
                    console.error('Error loading clients:', error);
                    showError();
                }
            }

            function renderTable(clients) {
                if (!clients || !clients.length) {
                    tableBody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><i class="fas fa-users"></i><p>{{ __('No clients found') }}</p></div></td></tr>`;
                    return;
                }

                tableBody.innerHTML = clients.map(client => {
                    const profileImg = client.profile_image_url;
                    const initial = client.name?.charAt(0) || 'U';
                    const avatarHtml = profileImg ? `<img src="${profileImg}" alt="${escapeHtml(client.name)}">` : initial;
                    const ratingStars = getRatingStars(client.rating);

                    return `
                                <tr>
                                    <td>#${client.id}</td>
                                    <td>
                                        <div class="user-avatar-cell">
                                            <div class="avatar-img">${avatarHtml}</div>
                                            <div class="user-name">${escapeHtml(client.name)}</div>
                                        </div>
                                    </td>
                                    <td>${escapeHtml(client.email)}</td>
                                    <td>${client.total_sessions || 0}</td>
                                    <td>${client.completed_sessions || 0}</td>
                                    <td class="rating-stars">${ratingStars}</td>
                                    <td>${client.last_session ? new Date(client.last_session).toLocaleDateString() : '—'}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="/specialist/clients/${client.id}" class="btn-icon" title="{{ __('View Profile') }}"><i class="fas fa-eye"></i></a>
                                            <a href="/chat/${client.id}" class="btn-icon" title="{{ __('Send Message') }}"><i class="fas fa-comment-dots"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                }).join('');
            }

            function renderCards(clients) {
                if (!clients || !clients.length) {
                    cardsContainer.innerHTML = `<div class="empty-state"><i class="fas fa-users"></i><p>{{ __('No clients found') }}</p></div>`;
                    return;
                }

                cardsContainer.innerHTML = clients.map(client => {
                    const profileImg = client.profile_image_url;
                    const initial = client.name?.charAt(0) || 'U';
                    const avatarHtml = profileImg ? `<img src="${profileImg}" alt="${escapeHtml(client.name)}">` : `<div class="avatar-placeholder-card">${initial}</div>`;
                    const ratingStars = getRatingStars(client.rating);

                    return `
                                <div class="client-card">
                                    <div class="client-card-header">
                                        <div class="client-card-avatar">
                                            ${avatarHtml}
                                        </div>
                                        <div class="client-card-title">
                                            <h4>${escapeHtml(client.name)}</h4>
                                            <p>${escapeHtml(client.email)}</p>
                                        </div>
                                    </div>
                                    <div class="client-card-body">
                                        <div class="client-card-info-row">
                                            <span class="info-label"><i class="fas fa-id-badge"></i> {{ __('ID') }}</span>
                                            <span class="info-value">#${client.id}</span>
                                        </div>
                                        <div class="client-card-info-row">
                                            <span class="info-label"><i class="fas fa-calendar-check"></i> {{ __('Sessions') }}</span>
                                            <span class="info-value">${client.total_sessions || 0} (${client.completed_sessions || 0} {{ __('completed') }})</span>
                                        </div>
                                        <div class="client-card-info-row">
                                            <span class="info-label"><i class="fas fa-star"></i> {{ __('Rating') }}</span>
                                            <span class="info-value rating-stars">${ratingStars}</span>
                                        </div>
                                        <div class="client-card-info-row">
                                            <span class="info-label"><i class="fas fa-clock"></i> {{ __('Last Session') }}</span>
                                            <span class="info-value">${client.last_session ? new Date(client.last_session).toLocaleDateString() : '—'}</span>
                                        </div>
                                    </div>
                                    <div class="client-card-footer">
                                        <a href="/specialist/clients/${client.id}" class="card-action-btn" title="{{ __('View Profile') }}">
                                            <i class="fas fa-eye"></i> {{ __('Profile') }}
                                        </a>
                                        <a href="/chat/${client.id}" class="card-action-btn" title="{{ __('Send Message') }}">
                                            <i class="fas fa-comment-dots"></i> {{ __('Message') }}
                                        </a>
                                    </div>
                                </div>
                            `;
                }).join('');
            }

            function getRatingStars(rating) {
                if (!rating || rating === 0) return '—';
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= rating) stars += '<i class="fas fa-star" style="color:#fbbf24;"></i>';
                    else stars += '<i class="far fa-star" style="color:#d1d5db;"></i>';
                }
                return stars;
            }

            function renderPagination(data) {
                const total = data.total, current = data.current_page, last = data.last_page;
                const from = (current - 1) * perPage + 1;
                const to = Math.min(current * perPage, total);

                paginationInfo.innerHTML = `{{ __('Showing') }} ${from} - ${to} {{ __('of') }} ${total} {{ __('clients') }}`;

                let html = '';
                html += `<button class="page-btn" onclick="goToPage(1)" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-double-left"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${current - 1})" ${current === 1 ? 'disabled' : ''}><i class="fas fa-angle-left"></i></button>`;

                for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                    html += `<button class="page-btn ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                }

                html += `<button class="page-btn" onclick="goToPage(${current + 1})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-right"></i></button>`;
                html += `<button class="page-btn" onclick="goToPage(${last})" ${current === last ? 'disabled' : ''}><i class="fas fa-angle-double-right"></i></button>`;

                paginationControls.innerHTML = html;
            }

            function goToPage(page) {
                currentPage = page;
                loadClients();
            }

            function showError() {
                tableBody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>{{ __('Error loading clients') }}</p><button class="btn-primary-sm" onclick="loadClients()">{{ __('Retry') }}</button></div></td></tr>`;
                cardsContainer.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>{{ __('Error loading clients') }}</p><button class="btn-primary-sm" onclick="loadClients()">{{ __('Retry') }}</button></div>`;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
            }

            // Initial load
            loadClients();
        </script>
    @endpush
@endsection