{{-- resources/views/admin/analytics/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Analytics Dashboard') . ' - ' . __('Tamman'))

@section('page-title', __('Analytics Dashboard'))

@section('content')
    <div class="analytics-container">
        <!-- Date Range Picker -->
        <div class="date-range-card animate-fade-in">
            <div class="date-range-content">
                <div class="range-info">
                    <i class="fas fa-chart-line"></i>
                    <div>
                        <h3>{{ __('Platform Analytics') }}</h3>
                        <p>{{ __('Comprehensive insights and trends of your platform') }}</p>
                    </div>
                </div>
                <div class="range-controls">
                    <div class="date-input-group">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" id="startDate" class="date-input" value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <span class="date-sep">{{ __('to') }}</span>
                    <div class="date-input-group">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" id="endDate" class="date-input" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <button class="btn-apply" id="applyDateRange">
                        <i class="fas fa-sync-alt"></i> {{ __('Apply') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="loading-overlay" style="display: none;">
            <div class="loading-spinner"></div>
            <p>{{ __('Loading analytics data...') }}</p>
        </div>

        <!-- Stats Cards Row -->
        <div class="stats-grid">
            <div class="stat-card animate-slide-up">
                <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                <div class="stat-data">
                    <h3 id="statTotalUsers">0</h3>
                    <p>{{ __('Total Users') }}</p>
                    <small id="statNewUsers" class="trend-up">+0%</small>
                </div>
            </div>
            <div class="stat-card animate-slide-up" style="animation-delay: 0.05s">
                <div class="stat-icon green"><i class="fas fa-user-md"></i></div>
                <div class="stat-data">
                    <h3 id="statSpecialists">0</h3>
                    <p>{{ __('Specialists') }}</p>
                    <small id="statNewSpecialists" class="trend-up">+0</small>
                </div>
            </div>
            <div class="stat-card animate-slide-up" style="animation-delay: 0.1s">
                <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-data">
                    <h3 id="statSessions">0</h3>
                    <p>{{ __('Completed Sessions') }}</p>
                    <small id="statSessionsTrend" class="trend-up">+0%</small>
                </div>
            </div>
            <div class="stat-card animate-slide-up" style="animation-delay: 0.15s">
                <div class="stat-icon orange"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-data">
                    <h3 id="statRevenue">$0</h3>
                    <p>{{ __('Revenue') }}</p>
                    <small id="statRevenueTrend" class="trend-up">+0%</small>
                </div>
            </div>
            <div class="stat-card animate-slide-up" style="animation-delay: 0.2s">
                <div class="stat-icon pink"><i class="fas fa-star"></i></div>
                <div class="stat-data">
                    <h3 id="statPointsEarned">0</h3>
                    <p>{{ __('Points Earned') }}</p>
                </div>
            </div>
            <div class="stat-card animate-slide-up" style="animation-delay: 0.25s">
                <div class="stat-icon teal"><i class="fas fa-gift"></i></div>
                <div class="stat-data">
                    <h3 id="statPointsRedeemed">0</h3>
                    <p>{{ __('Points Redeemed') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="analytics-tabs">
            <button class="tab-btn active" data-tab="overview">
                <i class="fas fa-chart-pie"></i> {{ __('Overview') }}
            </button>
            <button class="tab-btn" data-tab="users">
                <i class="fas fa-users"></i> {{ __('Users') }}
            </button>
            <button class="tab-btn" data-tab="sessions">
                <i class="fas fa-calendar-alt"></i> {{ __('Sessions') }}
            </button>
            <button class="tab-btn" data-tab="financial">
                <i class="fas fa-chart-line"></i> {{ __('Financial') }}
            </button>
            <button class="tab-btn" data-tab="points">
                <i class="fas fa-star"></i> {{ __('Points & Rewards') }}
            </button>
            <button class="tab-btn" data-tab="tests">
                <i class="fas fa-clipboard-list"></i> {{ __('Tests') }}
            </button>
            <button class="tab-btn" data-tab="specialists">
                <i class="fas fa-user-md"></i> {{ __('Specialists') }}
            </button>
        </div>

        <!-- Tab Content: Overview -->
        <div class="tab-content active" id="tab-overview">
            <div class="charts-grid">
                <div class="chart-card animate-fade-in">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('User Growth') }}</h3>
                        <span class="chart-subtitle">{{ __('Last 12 months') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="userGrowthChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.1s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> {{ __('Session Trends') }}</h3>
                        <span class="chart-subtitle">{{ __('Completed vs Cancelled') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="sessionTrendChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.2s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> {{ __('Session Types') }}</h3>
                        <span class="chart-subtitle">{{ __('Distribution') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="sessionTypeChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.3s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('Revenue Trend') }}</h3>
                        <span class="chart-subtitle">{{ __('Monthly revenue') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="revenueTrendChart" class="apex-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Users -->
        <div class="tab-content" id="tab-users">
            <div class="charts-grid">
                <div class="chart-card full-width animate-fade-in">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('User Growth (New vs Active)') }}</h3>
                        <span class="chart-subtitle">{{ __('Last 12 months') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="userGrowthDetailChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.1s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> {{ __('User Retention') }}</h3>
                        <span class="chart-subtitle">{{ __('30-day cohort retention') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="userRetentionChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.2s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> {{ __('Gender Distribution') }}</h3>
                        <span class="chart-subtitle">{{ __('Patient demographics') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="genderChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card full-width animate-fade-in" style="animation-delay: 0.3s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> {{ __('Age Groups') }}</h3>
                        <span class="chart-subtitle">{{ __('Age distribution') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="ageGroupChart" class="apex-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Sessions -->
        <div class="tab-content" id="tab-sessions">
            <div class="charts-grid">
                <div class="chart-card full-width animate-fade-in">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('Session Trends Over Time') }}</h3>
                        <span class="chart-subtitle">{{ __('Last 12 months') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="sessionDetailTrendChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.1s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> {{ __('Session Status Distribution') }}</h3>
                        <span class="chart-subtitle">{{ __('Scheduled, Completed, Cancelled') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="sessionStatusChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.2s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('Average Session Rating') }}</h3>
                        <span class="chart-subtitle">{{ __('Last 6 months') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="sessionRatingChart" class="apex-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Financial -->
        <div class="tab-content" id="tab-financial">
            <div class="charts-grid">
                <div class="chart-card full-width animate-fade-in">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('Revenue & Platform Fees') }}</h3>
                        <span class="chart-subtitle">{{ __('Last 12 months') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="revenueDetailChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.1s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('Donations Trend') }}</h3>
                        <span class="chart-subtitle">{{ __('Monthly donations received') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="donationChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.2s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('Specialist Payouts') }}</h3>
                        <span class="chart-subtitle">{{ __('Monthly payouts') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="payoutChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card full-width animate-fade-in" style="animation-delay: 0.3s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> {{ __('Credit Usage') }}</h3>
                        <span class="chart-subtitle">{{ __('Credit vs Cash payments') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="creditUsageChart" class="apex-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Points & Rewards -->
        <div class="tab-content" id="tab-points">
            <div class="charts-grid">
                <div class="chart-card animate-fade-in">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> {{ __('Popular Rewards') }}</h3>
                        <span class="chart-subtitle">{{ __('Most redeemed rewards') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="popularRewardsChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card animate-fade-in" style="animation-delay: 0.1s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> {{ __('Points Distribution') }}</h3>
                        <span class="chart-subtitle">{{ __('User points ranges') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="pointsDistributionChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card full-width animate-fade-in" style="animation-delay: 0.2s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> {{ __('Points by Source') }}</h3>
                        <span class="chart-subtitle">{{ __('How users earn points') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="pointsSourceChart" class="apex-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Tests -->
        <div class="tab-content" id="tab-tests">
            <div class="charts-grid">
                <div class="chart-card full-width animate-fade-in">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> {{ __('Test Distribution (All 6 Types)') }}</h3>
                        <span class="chart-subtitle">{{ __('Number of tests taken by type') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="testDistributionChart" class="apex-chart"></div>
                    </div>
                </div>
                <div class="chart-card full-width animate-fade-in" style="animation-delay: 0.1s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> {{ __('Test Trends (All Types)') }}</h3>
                        <span class="chart-subtitle">{{ __('Monthly test completions for all 6 test types') }}</span>
                    </div>
                    <div class="chart-body">
                        <div id="testTrendChart" class="apex-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Specialists -->
        <div class="tab-content" id="tab-specialists">
            <div class="charts-grid">
                <div class="chart-card full-width animate-fade-in">
                    <div class="chart-header">
                        <h3><i class="fas fa-trophy"></i> {{ __('Top Performing Specialists') }}</h3>
                        <span class="chart-subtitle">{{ __('By number of sessions') }}</span>
                    </div>
                    <div class="chart-body" style="min-height: 500px;">
                        <div id="topSpecialistsChart" class="apex-chart" style="height: 450px;"></div>
                    </div>
                </div>
                <div class="chart-card full-width animate-fade-in" style="animation-delay: 0.1s">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> {{ __('Specialist Earnings Distribution') }}</h3>
                        <span class="chart-subtitle">{{ __('Earnings ranges') }}</span>
                    </div>
                    <div class="chart-body"
                        style="min-height: 500px; display: flex; justify-content: center; align-items: center;">
                        <div id="specialistEarningsChart" class="apex-chart" style="height: 420px; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .analytics-container {
                max-width: 100%;
                margin: 0 auto;
                padding: 20px;
            }

            /* Date Range Card */
            .date-range-card {
                background: white;
                border-radius: 20px;
                padding: 20px 25px;
                margin-bottom: 25px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .date-range-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
            }

            .range-info {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .range-info i {
                font-size: 2rem;
                color: #7c3aed;
            }

            .range-info h3 {
                margin: 0;
                font-size: 1.1rem;
                color: #1f2937;
            }

            .range-info p {
                margin: 0;
                font-size: 0.75rem;
                color: #6b7280;
            }

            .range-controls {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .date-input-group {
                position: relative;
                display: inline-flex;
                align-items: center;
            }

            .date-input-group i {
                position: absolute;
                left: 12px;
                color: #9ca3af;
            }

            .date-input {
                padding: 10px 12px 10px 38px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.85rem;
                background: #f9fafb;
            }

            .date-input:focus {
                outline: none;
                border-color: #7c3aed;
                box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
            }

            .date-sep {
                color: #9ca3af;
                font-size: 0.85rem;
            }

            .btn-apply {
                padding: 10px 20px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
            }

            .btn-apply:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
            }

            /* Loading Overlay */
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(4px);
                z-index: 10000;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 15px;
            }

            .loading-spinner {
                width: 50px;
                height: 50px;
                border: 3px solid rgba(124, 58, 237, 0.2);
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            .loading-overlay p {
                color: white;
                font-size: 0.9rem;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            /* Stats Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 18px;
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
                width: 50px;
                height: 50px;
                border-radius: 15px;
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

            .stat-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon.pink {
                background: linear-gradient(135deg, #ec4899, #db2777);
            }

            .stat-icon.teal {
                background: linear-gradient(135deg, #14b8a6, #0d9488);
            }

            .stat-data h3 {
                font-size: 1.4rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
            }

            .stat-data p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 0;
            }

            .stat-data small {
                font-size: 0.65rem;
            }

            .trend-up {
                color: #10b981;
            }

            .trend-down {
                color: #ef4444;
            }

            /* Tabs - Centered */
            .analytics-tabs {
                display: flex;
                justify-content: center;
                gap: 8px;
                margin-bottom: 25px;
                flex-wrap: wrap;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 0;
            }

            .tab-btn {
                padding: 12px 24px;
                background: none;
                border: none;
                font-size: 0.85rem;
                font-weight: 500;
                color: #6b7280;
                cursor: pointer;
                transition: all 0.3s ease;
                border-radius: 40px 40px 0 0;
                position: relative;
            }

            .tab-btn i {
                margin-right: 8px;
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

            /* Tab Content */
            .tab-content {
                display: none;
                animation: fadeIn 0.4s ease;
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

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.5s ease forwards;
            }

            .animate-slide-up {
                animation: slideUp 0.4s ease forwards;
                opacity: 0;
            }

            /* Charts Grid */
            .charts-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }

            .chart-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .chart-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .chart-card.full-width {
                grid-column: span 2;
            }

            .chart-header {
                padding: 18px 20px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .chart-header h3 {
                margin: 0;
                font-size: 0.95rem;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1f2937;
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
                min-height: 350px;
            }

            .apex-chart {
                width: 100%;
                min-height: 300px;
            }

            /* Credit Usage Chart - Better color */
            .credit-usage-chart {
                background: transparent;
            }

            /* Responsive */
            @media (max-width: 1400px) {
                .stats-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 992px) {
                .charts-grid {
                    grid-template-columns: 1fr;
                }

                .chart-card.full-width {
                    grid-column: span 1;
                }

                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .date-range-content {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .range-controls {
                    width: 100%;
                    justify-content: flex-start;
                }
            }

            @media (max-width: 768px) {
                .analytics-container {
                    padding: 15px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .analytics-tabs {
                    justify-content: center;
                }

                .tab-btn {
                    padding: 8px 14px;
                    font-size: 0.75rem;
                }

                .chart-body {
                    padding: 15px;
                    min-height: 280px;
                }

                .apex-chart {
                    min-height: 250px;
                }

                .range-controls {
                    flex-wrap: wrap;
                }

                .date-input-group {
                    flex: 1;
                }

                .date-input {
                    width: 100%;
                }
            }

            @media (max-width: 480px) {
                .stats-grid {
                    gap: 12px;
                }

                .stat-card {
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
            body.rtl .date-input-group i {
                left: auto;
                right: 12px;
            }

            body.rtl .date-input {
                padding: 10px 38px 10px 12px;
            }

            body.rtl .tab-btn i {
                margin-right: 0;
                margin-left: 8px;
            }

            body.rtl .range-info {
                flex-direction: row;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Global chart instances
            let charts = {};

            // Current date range
            let startDate = document.getElementById('startDate').value;
            let endDate = document.getElementById('endDate').value;
            const currentLocale = '{{ app()->getLocale() }}';

            // Helper functions
            function showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            function hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            function formatCurrency(value) {
                return '$' + parseFloat(value).toLocaleString();
            }

            function formatNumber(value) {
                return parseInt(value).toLocaleString();
            }

            function getPercentClass(value) {
                return value >= 0 ? 'trend-up' : 'trend-down';
            }

            function getPercentSign(value) {
                return value >= 0 ? `+${value}%` : `${value}%`;
            }

            // Destroy chart safely
            function destroyChart(chartName) {
                if (charts[chartName] && typeof charts[chartName].destroy === 'function') {
                    try {
                        charts[chartName].destroy();
                    } catch (e) { console.warn(e); }
                    charts[chartName] = null;
                }
            }

            // Load all analytics
            async function loadAllAnalytics() {
                showLoading();

                try {
                    await loadOverviewStats();
                    await Promise.all([
                        loadUserAnalytics(),
                        loadSessionAnalytics(),
                        loadFinancialAnalytics(),
                        loadPointsAnalytics(),
                        loadTestAnalytics(),
                        loadSpecialistAnalytics()
                    ]);
                } catch (error) {
                    console.error('Error loading analytics:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Failed to load analytics data. Please refresh the page.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                } finally {
                    hideLoading();
                }
            }

            // Load Overview Stats
            async function loadOverviewStats() {
                const response = await fetch(`/admin/analytics/overview?start_date=${startDate}&end_date=${endDate}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    const stats = data.data;

                    document.getElementById('statTotalUsers').innerText = formatNumber(stats.total_users);
                    document.getElementById('statSpecialists').innerText = formatNumber(stats.total_specialists);
                    document.getElementById('statSessions').innerText = formatNumber(stats.completed_sessions);
                    document.getElementById('statRevenue').innerText = formatCurrency(stats.revenue);
                    document.getElementById('statPointsEarned').innerText = formatNumber(stats.points_earned);
                    document.getElementById('statPointsRedeemed').innerText = formatNumber(stats.points_redeemed);

                    const newUsersEl = document.getElementById('statNewUsers');
                    newUsersEl.innerText = getPercentSign(stats.new_users_percent);
                    newUsersEl.className = getPercentClass(stats.new_users_percent);

                    const sessionsTrendEl = document.getElementById('statSessionsTrend');
                    sessionsTrendEl.innerText = getPercentSign(stats.completed_sessions_percent);
                    sessionsTrendEl.className = getPercentClass(stats.completed_sessions_percent);

                    const revenueTrendEl = document.getElementById('statRevenueTrend');
                    revenueTrendEl.innerText = getPercentSign(stats.revenue_percent);
                    revenueTrendEl.className = getPercentClass(stats.revenue_percent);

                    document.getElementById('statNewSpecialists').innerHTML = `<i class="fas fa-user-plus"></i> +${stats.new_specialists}`;
                }
            }

            // Load User Analytics
            async function loadUserAnalytics() {
                const response = await fetch(`/admin/analytics/users?start_date=${startDate}&end_date=${endDate}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    // User Growth Chart (Overview)
                    destroyChart('userGrowth');
                    charts.userGrowth = new ApexCharts(document.querySelector("#userGrowthChart"), {
                        series: [
                            { name: '{{ __("New Users") }}', data: data.growth.new_users },
                            { name: '{{ __("Active Users") }}', data: data.growth.active_users }
                        ],
                        chart: { type: 'line', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 3 },
                        colors: ['#10b981', '#7c3aed'],
                        markers: { size: 5, hover: { size: 8 } },
                        tooltip: { shared: true, intersect: false, theme: 'dark' },
                        grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                        xaxis: { categories: data.growth.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Number of Users") }}' }, labels: { formatter: (val) => Math.round(val) } },
                        legend: { position: 'top', labels: { colors: '#374151' } }
                    });
                    charts.userGrowth.render();

                    // User Growth Detail Chart
                    destroyChart('userGrowthDetail');
                    charts.userGrowthDetail = new ApexCharts(document.querySelector("#userGrowthDetailChart"), {
                        series: [
                            { name: '{{ __("New Users") }}', data: data.growth.new_users },
                            { name: '{{ __("Active Users") }}', data: data.growth.active_users }
                        ],
                        chart: { type: 'area', height: 350, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 2 },
                        colors: ['#10b981', '#7c3aed'],
                        fill: { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.3, opacityTo: 0.05 } },
                        markers: { size: 4 },
                        tooltip: { shared: true, intersect: false, theme: 'dark' },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.growth.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Number of Users") }}' } },
                        legend: { position: 'top' }
                    });
                    charts.userGrowthDetail.render();

                    // User Retention Chart
                    destroyChart('userRetention');
                    charts.userRetention = new ApexCharts(document.querySelector("#userRetentionChart"), {
                        series: [{ name: '{{ __("Retention Rate") }}', data: data.retention.retention_rates }],
                        chart: { type: 'bar', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '60%' } },
                        colors: ['#f59e0b'],
                        tooltip: { theme: 'dark', y: { formatter: (val) => val + '%' } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.retention.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Retention Rate (%)") }}' }, min: 0, max: 100, labels: { formatter: (val) => val + '%' } }
                    });
                    charts.userRetention.render();

                    // Gender Distribution Chart
                    destroyChart('gender');
                    const genderData = data.demographics.gender;
                    const genderLabels = genderData.map(g => g.gender === 'male' ? '{{ __("Male") }}' : (g.gender === 'female' ? '{{ __("Female") }}' : '{{ __("Other") }}'));
                    const genderValues = genderData.map(g => g.total);
                    const totalGender = genderValues.reduce((a, b) => a + b, 0);

                    charts.gender = new ApexCharts(document.querySelector("#genderChart"), {
                        series: genderValues,
                        chart: { type: 'donut', height: 300, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        labels: genderLabels,
                        colors: ['#7c3aed', '#ec4899', '#f59e0b'],
                        legend: { position: 'bottom', labels: { colors: '#374151' } },
                        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: '{{ __("Total") }}', formatter: () => totalGender } } } } },
                        tooltip: { theme: 'dark' }
                    });
                    charts.gender.render();

                    // Age Groups Chart (Full Width)
                    destroyChart('ageGroups');
                    const ageGroups = data.demographics.age_groups;
                    charts.ageGroups = new ApexCharts(document.querySelector("#ageGroupChart"), {
                        series: [{ name: '{{ __("Users") }}', data: Object.values(ageGroups) }],
                        chart: { type: 'bar', height: 350, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '50%' } },
                        colors: ['#10b981'],
                        tooltip: { theme: 'dark' },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: Object.keys(ageGroups), title: { text: '{{ __("Age Group") }}' } },
                        yaxis: { title: { text: '{{ __("Number of Users") }}' } }
                    });
                    charts.ageGroups.render();
                }
            }

            // Load Session Analytics
            async function loadSessionAnalytics() {
                const response = await fetch(`/admin/analytics/sessions?start_date=${startDate}&end_date=${endDate}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    // Session Trend Chart (Overview)
                    destroyChart('sessionTrend');
                    charts.sessionTrend = new ApexCharts(document.querySelector("#sessionTrendChart"), {
                        series: [
                            { name: '{{ __("Completed") }}', data: data.trend.sessions },
                            { name: '{{ __("Cancelled") }}', data: data.trend.cancelled }
                        ],
                        chart: { type: 'line', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 3 },
                        colors: ['#10b981', '#ef4444'],
                        markers: { size: 5 },
                        tooltip: { shared: true, intersect: false, theme: 'dark' },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.trend.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Number of Sessions") }}' } },
                        legend: { position: 'top' }
                    });
                    charts.sessionTrend.render();

                    // Session Type Chart
                    destroyChart('sessionType');
                    charts.sessionType = new ApexCharts(document.querySelector("#sessionTypeChart"), {
                        series: Object.values(data.types.percentages),
                        chart: { type: 'donut', height: 300, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        labels: ['{{ __("Video") }}', '{{ __("Audio") }}', '{{ __("Text") }}'],
                        colors: ['#7c3aed', '#10b981', '#f59e0b'],
                        tooltip: { theme: 'dark', y: { formatter: (val) => val + '%' } },
                        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: '{{ __("Total") }}', formatter: () => '100%' } } } } },
                        legend: { position: 'bottom' }
                    });
                    charts.sessionType.render();

                    // Session Detail Trend Chart
                    destroyChart('sessionDetailTrend');
                    charts.sessionDetailTrend = new ApexCharts(document.querySelector("#sessionDetailTrendChart"), {
                        series: [
                            { name: '{{ __("Sessions") }}', data: data.trend.sessions },
                            { name: '{{ __("Cancelled") }}', data: data.trend.cancelled }
                        ],
                        chart: { type: 'line', height: 350, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 3 },
                        colors: ['#7c3aed', '#ef4444'],
                        markers: { size: 5 },
                        tooltip: { shared: true, intersect: false, theme: 'dark' },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.trend.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Number of Sessions") }}' } },
                        legend: { position: 'top' }
                    });
                    charts.sessionDetailTrend.render();

                    // Session Status Chart
                    destroyChart('sessionStatus');
                    const statusData = data.status_distribution;
                    const totalStatus = Object.values(statusData).reduce((a, b) => a + b, 0);
                    charts.sessionStatus = new ApexCharts(document.querySelector("#sessionStatusChart"), {
                        series: Object.values(statusData),
                        chart: { type: 'donut', height: 300, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        labels: ['{{ __("Scheduled") }}', '{{ __("Completed") }}', '{{ __("Cancelled") }}', '{{ __("No Show") }}'],
                        colors: ['#3b82f6', '#10b981', '#ef4444', '#f59e0b'],
                        tooltip: { theme: 'dark' },
                        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: '{{ __("Total") }}', formatter: () => totalStatus } } } } },
                        legend: { position: 'bottom' }
                    });
                    charts.sessionStatus.render();

                    // Session Rating Chart
                    destroyChart('sessionRating');
                    charts.sessionRating = new ApexCharts(document.querySelector("#sessionRatingChart"), {
                        series: [{ name: '{{ __("Average Rating") }}', data: data.average_rating.ratings }],
                        chart: { type: 'line', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 3, colors: ['#f59e0b'] },
                        markers: { size: 5 },
                        tooltip: { theme: 'dark', y: { formatter: (val) => val + ' / 5' } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.average_rating.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Rating (1-5)") }}' }, min: 0, max: 5, tickAmount: 5 }
                    });
                    charts.sessionRating.render();
                }
            }

            // Load Financial Analytics
            async function loadFinancialAnalytics() {
                const response = await fetch(`/admin/analytics/financial?start_date=${startDate}&end_date=${endDate}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    // Revenue Trend Chart
                    destroyChart('revenueTrend');
                    charts.revenueTrend = new ApexCharts(document.querySelector("#revenueTrendChart"), {
                        series: [{ name: '{{ __("Revenue") }}', data: data.revenue.revenue }],
                        chart: { type: 'area', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 2 },
                        colors: ['#10b981'],
                        fill: { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.4, opacityTo: 0.05 } },
                        tooltip: { theme: 'dark', y: { formatter: (val) => '$' + val.toLocaleString() } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.revenue.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Revenue (USD)") }}' }, labels: { formatter: (val) => '$' + val.toLocaleString() } }
                    });
                    charts.revenueTrend.render();

                    // Revenue Detail Chart (NO numbers on bars)
                    destroyChart('revenueDetail');
                    charts.revenueDetail = new ApexCharts(document.querySelector("#revenueDetailChart"), {
                        series: [
                            { name: '{{ __("Revenue") }}', data: data.revenue.revenue },
                            { name: '{{ __("Platform Fees") }}', data: data.revenue.platform_fees }
                        ],
                        chart: { type: 'bar', height: 350, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 3 },
                        colors: ['#7c3aed', '#f59e0b'],
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '60%', dataLabels: { position: 'top' } } },
                        dataLabels: { enabled: false },
                        tooltip: { shared: true, intersect: false, theme: 'dark', y: { formatter: (val) => '$' + val.toLocaleString() } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.revenue.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Amount (USD)") }}' }, labels: { formatter: (val) => '$' + val.toLocaleString() } },
                        legend: { position: 'top' }
                    });
                    charts.revenueDetail.render();

                    // Donation Chart
                    destroyChart('donation');
                    charts.donation = new ApexCharts(document.querySelector("#donationChart"), {
                        series: [
                            { name: '{{ __("Donations") }}', data: data.donations.donations },
                            { name: '{{ __("Donors") }}', data: data.donations.donors }
                        ],
                        chart: { type: 'line', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        colors: ['#ec4899', '#f59e0b'],
                        stroke: { curve: 'smooth', width: 3 },
                        markers: { size: 5 },
                        tooltip: { shared: true, intersect: false, theme: 'dark' },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.donations.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: [
                            { title: { text: '{{ __("Donations (USD)") }}' }, labels: { formatter: (val) => '$' + val.toLocaleString() } },
                            { opposite: true, title: { text: '{{ __("Number of Donors") }}' }, labels: { formatter: (val) => Math.round(val) } }
                        ],
                        legend: { position: 'top' }
                    });
                    charts.donation.render();

                    // Payout Chart
                    destroyChart('payout');
                    charts.payout = new ApexCharts(document.querySelector("#payoutChart"), {
                        series: [{ name: '{{ __("Payouts") }}', data: data.payouts.payouts }],
                        chart: { type: 'line', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 3, colors: ['#10b981'] },
                        markers: { size: 5 },
                        tooltip: { theme: 'dark', y: { formatter: (val) => '$' + val.toLocaleString() } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.payouts.months, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Payouts (USD)") }}' }, labels: { formatter: (val) => '$' + val.toLocaleString() } }
                    });
                    charts.payout.render();

                    // Credit Usage Chart (Full Width - Better Color)
                    destroyChart('creditUsage');
                    const creditPercent = data.credit_usage.credit_sessions_percent;
                    charts.creditUsage = new ApexCharts(document.querySelector("#creditUsageChart"), {
                        series: [creditPercent, 100 - creditPercent],
                        chart: { type: 'donut', height: 350, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        labels: ['{{ __("Credit Payments") }}', '{{ __("Cash Payments") }}'],
                        colors: ['#7c3aed', '#c4b5fd'],
                        tooltip: { theme: 'dark', y: { formatter: (val) => val + '%' } },
                        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: '{{ __("Total") }}', formatter: () => '100%' } } } } },
                        legend: { position: 'bottom' }
                    });
                    charts.creditUsage.render();
                }
            }

            // Load Points Analytics
            async function loadPointsAnalytics() {
                const response = await fetch(`/admin/analytics/points?start_date=${startDate}&end_date=${endDate}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    // Points Distribution Chart
                    destroyChart('pointsDistribution');
                    const distData = Object.values(data.distribution);
                    const totalUsers = distData.reduce((a, b) => a + b, 0);
                    charts.pointsDistribution = new ApexCharts(document.querySelector("#pointsDistributionChart"), {
                        series: distData,
                        chart: { type: 'donut', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        labels: Object.keys(data.distribution).map(k => k + ' {{ __("points") }}'),
                        colors: ['#7c3aed', '#10b981', '#f59e0b', '#ef4444', '#ec4899'],
                        tooltip: { theme: 'dark' },
                        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: '{{ __("Total Users") }}', formatter: () => totalUsers } } } } },
                        legend: { position: 'bottom' }
                    });
                    charts.pointsDistribution.render();

                    // Points Source Chart (Full Width)
                    destroyChart('pointsSource');
                    const sourceLabels = {
                        mood_tracking: '{{ __("Mood Tracking") }}',
                        session_attendance: '{{ __("Session Attendance") }}',
                        test_completed: '{{ __("Test Completed") }}',
                        task_completed: '{{ __("Task Completed") }}',
                        referral: '{{ __("Referral") }}',
                        specialist_rating: '{{ __("Specialist Rating") }}',
                        streak_bonus: '{{ __("Streak Bonus") }}'
                    };
                    const sourceData = Object.values(data.earnings_by_source);
                    const sourceCategories = Object.keys(data.earnings_by_source).map(k => sourceLabels[k] || k);

                    charts.pointsSource = new ApexCharts(document.querySelector("#pointsSourceChart"), {
                        series: [{ name: '{{ __("Points Earned") }}', data: sourceData }],
                        chart: { type: 'bar', height: 380, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '60%', horizontal: true } },
                        colors: ['#7c3aed'],
                        tooltip: { theme: 'dark', y: { formatter: (val) => formatNumber(val) } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: sourceCategories, title: { text: '{{ __("Points Earned") }}' } },
                        yaxis: { title: { text: '{{ __("Source") }}' } }
                    });
                    charts.pointsSource.render();

                    // Popular Rewards Chart
                    destroyChart('popularRewards');
                    const rewardsData = data.popular_rewards;
                    charts.popularRewards = new ApexCharts(document.querySelector("#popularRewardsChart"), {
                        series: [{ name: '{{ __("Redemptions") }}', data: rewardsData.map(r => r.redemptions) }],
                        chart: { type: 'bar', height: 320, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '60%' } },
                        colors: ['#f59e0b'],
                        tooltip: { theme: 'dark', y: { formatter: (val) => formatNumber(val) } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: rewardsData.map(r => r.name), labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Number of Redemptions") }}' } }
                    });
                    charts.popularRewards.render();
                }
            }

            // Load Test Analytics (All 6 tests)
            async function loadTestAnalytics() {
                const response = await fetch(`/admin/analytics/tests?start_date=${startDate}&end_date=${endDate}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    // Test Distribution Chart (All 6 tests)
                    destroyChart('testDistribution');
                    const testNames = {
                        phq9: 'PHQ-9 ({{ __("Depression") }})',
                        gad7: 'GAD-7 ({{ __("Anxiety") }})',
                        pcl5: 'PCL-5 ({{ __("PTSD") }})',
                        isi: 'ISI ({{ __("Insomnia") }})',
                        pss: 'PSS ({{ __("Stress") }})',
                        cis: 'CIS ({{ __("Functioning") }})'
                    };
                    const testData = Object.values(data.distribution);
                    const testCategories = Object.keys(data.distribution).map(k => testNames[k] || k);

                    charts.testDistribution = new ApexCharts(document.querySelector("#testDistributionChart"), {
                        series: [{ name: '{{ __("Tests Taken") }}', data: testData }],
                        chart: { type: 'bar', height: 400, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '60%' } },
                        colors: ['#7c3aed'],
                        tooltip: { theme: 'dark', y: { formatter: (val) => formatNumber(val) } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: testCategories, labels: { rotate: -35, style: { fontSize: '10px' } } },
                        yaxis: { title: { text: '{{ __("Number of Tests Taken") }}' } }
                    });
                    charts.testDistribution.render();

                    // Test Trend Chart (All 6 tests - using multiple lines)
                    destroyChart('testTrend');

                    // Prepare series data for all 6 tests
                    const testTypes = ['phq9', 'gad7', 'pcl5', 'isi', 'pss', 'cis'];
                    const testColors = ['#7c3aed', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4'];
                    const testNameMap = {
                        phq9: 'PHQ-9',
                        gad7: 'GAD-7',
                        pcl5: 'PCL-5',
                        isi: 'ISI',
                        pss: 'PSS',
                        cis: 'CIS'
                    };

                    // Fetch individual test trend data if available, or use the existing data structure
                    // For now, we'll use the available data or create a more comprehensive approach
                    let trendSeries = [];

                    // If the API returns trends for all tests, use them
                    if (data.trends && data.trends.all_tests) {
                        for (let i = 0; i < testTypes.length; i++) {
                            trendSeries.push({
                                name: testNameMap[testTypes[i]],
                                data: data.trends.all_tests[testTypes[i]] || Array(data.trends.months.length).fill(0),
                                type: 'line'
                            });
                        }
                    } else {
                        // Fallback: Use PHQ9 and GAD7 data, add zeros for others
                        for (let i = 0; i < testTypes.length; i++) {
                            if (testTypes[i] === 'phq9') {
                                trendSeries.push({ name: 'PHQ-9', data: data.trends.phq9 || Array(data.trends.months.length).fill(0), type: 'line' });
                            } else if (testTypes[i] === 'gad7') {
                                trendSeries.push({ name: 'GAD-7', data: data.trends.gad7 || Array(data.trends.months.length).fill(0), type: 'line' });
                            } else {
                                trendSeries.push({ name: testNameMap[testTypes[i]], data: Array(data.trends.months.length).fill(0), type: 'line' });
                            }
                        }
                    }

                    charts.testTrend = new ApexCharts(document.querySelector("#testTrendChart"), {
                        series: trendSeries,
                        chart: { type: 'line', height: 400, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        stroke: { curve: 'smooth', width: 3 },
                        colors: testColors,
                        markers: { size: 4 },
                        tooltip: { shared: true, intersect: false, theme: 'dark' },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: data.trends.months, labels: { rotate: -35, style: { fontSize: '10px' } }, title: { text: '{{ __("Month") }}' } },
                        yaxis: { title: { text: '{{ __("Number of Tests") }}' } },
                        legend: { position: 'top', labels: { colors: '#374151' } }
                    });
                    charts.testTrend.render();
                }
            }

            // Load Specialist Analytics
            async function loadSpecialistAnalytics() {
                const response = await fetch(`/admin/analytics/specialists?start_date=${startDate}&end_date=${endDate}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    // Top Specialists Chart
                    destroyChart('topSpecialists');
                    const topSpecialists = data.top_specialists;
                    charts.topSpecialists = new ApexCharts(document.querySelector("#topSpecialistsChart"), {
                        series: [{ name: '{{ __("Sessions") }}', data: topSpecialists.map(s => s.sessions_count) }],
                        chart: { type: 'bar', height: 450, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '60%', horizontal: true } },
                        colors: ['#7c3aed'],
                        tooltip: { theme: 'dark', y: { formatter: (val) => formatNumber(val) } },
                        grid: { borderColor: '#e5e7eb' },
                        xaxis: { categories: topSpecialists.map(s => s.name), title: { text: '{{ __("Sessions Completed") }}' } },
                        yaxis: { title: { text: '{{ __("Specialist") }}' } }
                    });
                    charts.topSpecialists.render();

                    // Specialist Earnings Distribution Chart (Full Width Square)
                    destroyChart('specialistEarnings');
                    const earningsValues = Object.values(data.earnings_distribution);
                    const totalSpecialists = earningsValues.reduce((a, b) => a + b, 0);
                    charts.specialistEarnings = new ApexCharts(document.querySelector("#specialistEarningsChart"), {
                        series: earningsValues,
                        chart: { type: 'donut', height: 380, toolbar: { show: false }, animations: { enabled: true }, background: 'transparent' },
                        labels: Object.keys(data.earnings_distribution).map(r => '$' + r),
                        colors: ['#7c3aed', '#10b981', '#f59e0b', '#ef4444', '#ec4899'],
                        tooltip: { theme: 'dark' },
                        plotOptions: { pie: { donut: { size: '60%', labels: { show: true, total: { show: true, label: '{{ __("Total Specialists") }}', formatter: () => totalSpecialists } } } } },
                        legend: { position: 'bottom', labels: { colors: '#374151' } }
                    });
                    charts.specialistEarnings.render();
                }
            }

            // Tab switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tabId = btn.dataset.tab;
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');

                    setTimeout(() => {
                        Object.values(charts).forEach(chart => {
                            if (chart && typeof chart.updateOptions === 'function') {
                                try {
                                    chart.updateOptions({});
                                } catch (e) { console.warn(e); }
                            }
                        });
                    }, 200);
                });
            });

            // Apply date range
            document.getElementById('applyDateRange').addEventListener('click', () => {
                startDate = document.getElementById('startDate').value;
                endDate = document.getElementById('endDate').value;
                loadAllAnalytics();
            });

            // Initial load
            loadAllAnalytics();

            // Re-render charts on window resize
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    Object.values(charts).forEach(chart => {
                        if (chart && typeof chart.updateOptions === 'function') {
                            try {
                                chart.updateOptions({});
                            } catch (e) { console.warn(e); }
                        }
                    });
                }, 250);
            });
        </script>
    @endpush
@endsection