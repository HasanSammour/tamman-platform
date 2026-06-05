{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Reports Dashboard') . ' - ' . __('Tamman'))

@section('page-title', __('Reports Dashboard'))

@section('content')
    <div class="reports-container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3 id="statTotalUsers">{{ number_format($stats['total_users']) }}</h3>
                    <p>{{ __('Total Users') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_specialists']) }}</h3>
                    <p>{{ __('Total Specialists') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Total Sessions') }}</p>
                    <small>{{ number_format($stats['completed_sessions']) }} {{ __('completed') }}</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>${{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p>{{ __('Total Revenue') }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_points_earned']) }}</h3>
                    <p>{{ __('Points Earned') }}</p>
                    <small>{{ number_format($stats['total_points_redeemed']) }} {{ __('redeemed') }}</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="stat-info">
                    <h3>${{ number_format($stats['total_donations'], 2) }}</h3>
                    <p>{{ __('Total Donations') }}</p>
                </div>
            </div>
        </div>

        <!-- Report Cards Grid (Export Buttons Removed) -->
        <div class="reports-grid">
            <!-- Users Report Card -->
            <div class="report-card">
                <div class="report-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="report-info">
                    <h3>{{ __('Users Report') }}</h3>
                    <p>{{ __('View and export users data with filters') }}</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('admin.reports.users') }}" class="btn-view-report">
                        <i class="fas fa-chart-line"></i> {{ __('View Report') }}
                    </a>
                </div>
            </div>

            <!-- Sessions Report Card -->
            <div class="report-card">
                <div class="report-icon blue">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="report-info">
                    <h3>{{ __('Sessions Report') }}</h3>
                    <p>{{ __('Session statistics, trends and revenue') }}</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('admin.reports.sessions') }}" class="btn-view-report">
                        <i class="fas fa-chart-line"></i> {{ __('View Report') }}
                    </a>
                </div>
            </div>

            <!-- Financial Report Card -->
            <div class="report-card">
                <div class="report-icon green">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="report-info">
                    <h3>{{ __('Financial Report') }}</h3>
                    <p>{{ __('Revenue, donations, credits summary') }}</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('admin.reports.financial') }}" class="btn-view-report">
                        <i class="fas fa-chart-line"></i> {{ __('View Report') }}
                    </a>
                </div>
            </div>

            <!-- Specialists Report Card -->
            <div class="report-card">
                <div class="report-icon orange">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="report-info">
                    <h3>{{ __('Specialists Report') }}</h3>
                    <p>{{ __('Specialist performance and earnings') }}</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('admin.reports.specialists') }}" class="btn-view-report">
                        <i class="fas fa-chart-line"></i> {{ __('View Report') }}
                    </a>
                </div>
            </div>

            <!-- Points Report Card -->
            <div class="report-card">
                <div class="report-icon teal">
                    <i class="fas fa-star"></i>
                </div>
                <div class="report-info">
                    <h3>{{ __('Points Report') }}</h3>
                    <p>{{ __('Points earned and redeemed statistics') }}</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('admin.reports.points') }}" class="btn-view-report">
                        <i class="fas fa-chart-line"></i> {{ __('View Report') }}
                    </a>
                </div>
            </div>

            <!-- Tests Report Card -->
            <div class="report-card">
                <div class="report-icon pink">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="report-info">
                    <h3>{{ __('Tests Report') }}</h3>
                    <p>{{ __('Psychological test completion statistics') }}</p>
                </div>
                <div class="report-actions">
                    <a href="{{ route('admin.reports.tests') }}" class="btn-view-report">
                        <i class="fas fa-chart-line"></i> {{ __('View Report') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats Charts Row -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> {{ __('Platform Activity (Last 30 Days)') }}</h3>
                </div>
                <div class="chart-body">
                    <div id="activityChart" class="apex-chart"></div>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-pie"></i> {{ __('Session Types Distribution') }}</h3>
                </div>
                <div class="chart-body">
                    <div id="sessionTypesChart" class="apex-chart"></div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* ========================================
                                       REPORTS DASHBOARD - FULL RESPONSIVE STYLES
                                       ======================================== */

            .reports-container {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                overflow-x: hidden;
            }

            /* Stats Grid - 6 columns desktop, responsive */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 16px;
                margin-bottom: 24px;
            }

            .stat-card {
                background: white;
                border-radius: 16px;
                padding: 16px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 48px;
                height: 48px;
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

            .stat-icon.blue {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .stat-icon.orange {
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .stat-icon.teal {
                background: linear-gradient(135deg, #14b8a6, #0d9488);
            }

            .stat-icon.pink {
                background: linear-gradient(135deg, #ec4899, #db2777);
            }

            .stat-info {
                flex: 1;
                min-width: 0;
            }

            .stat-info h3 {
                font-size: 1.25rem;
                font-weight: 700;
                margin: 0;
                color: #1f2937;
                line-height: 1.2;
            }

            .stat-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 4px 0 0;
            }

            .stat-info small {
                font-size: 0.6rem;
                color: #9ca3af;
                display: block;
                margin-top: 2px;
            }

            /* Reports Grid */
            .reports-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .report-card {
                background: white;
                border-radius: 16px;
                padding: 18px;
                display: flex;
                align-items: center;
                gap: 14px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
            }

            .report-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            }

            .report-icon {
                width: 52px;
                height: 52px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .report-icon i {
                font-size: 1.4rem;
                color: white;
            }

            .report-info {
                flex: 1;
                min-width: 0;
            }

            .report-info h3 {
                font-size: 0.95rem;
                font-weight: 600;
                margin: 0;
                color: #1f2937;
            }

            .report-info p {
                font-size: 0.7rem;
                color: #6b7280;
                margin: 5px 0 0;
                line-height: 1.3;
            }

            .report-actions {
                display: flex;
                gap: 8px;
                flex-shrink: 0;
            }

            .btn-view-report {
                padding: 7px 16px;
                background: #ede9fe;
                color: #7c3aed;
                border-radius: 40px;
                font-size: 0.7rem;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.3s ease;
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .btn-view-report:hover {
                background: #ddd6fe;
                transform: translateY(-2px);
            }

            /* Charts Row */
            .charts-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .chart-card {
                background: white;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            }

            .chart-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f0f0f0;
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

            .chart-body {
                padding: 16px;
                min-height: 300px;
            }

            .apex-chart {
                width: 100%;
                min-height: 280px;
            }

            /* ========================================
                                       RESPONSIVE BREAKPOINTS
                                       ======================================== */

            @media (max-width: 1400px) {
                .stats-grid {
                    gap: 14px;
                }

                .stat-icon {
                    width: 44px;
                    height: 44px;
                }

                .stat-icon i {
                    font-size: 1.2rem;
                }

                .stat-info h3 {
                    font-size: 1.1rem;
                }
            }

            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 14px;
                }
            }

            @media (max-width: 992px) {
                .reports-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 16px;
                }

                .charts-row {
                    gap: 16px;
                }

                .reports-container {
                    padding: 0;
                }
            }

            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 12px;
                    margin-bottom: 20px;
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

                .stat-info h3 {
                    font-size: 1rem;
                }

                .stat-info p {
                    font-size: 0.65rem;
                }

                .reports-grid {
                    grid-template-columns: 1fr;
                    gap: 12px;
                    margin-bottom: 20px;
                }

                .report-card {
                    padding: 14px;
                }

                .report-icon {
                    width: 46px;
                    height: 46px;
                }

                .report-icon i {
                    font-size: 1.2rem;
                }

                .report-info h3 {
                    font-size: 0.85rem;
                }

                .report-info p {
                    font-size: 0.65rem;
                }

                .charts-row {
                    grid-template-columns: 1fr;
                    gap: 16px;
                    margin-bottom: 20px;
                }

                .chart-header {
                    padding: 12px 16px;
                }

                .chart-header h3 {
                    font-size: 0.85rem;
                }

                .chart-body {
                    padding: 12px;
                    min-height: 260px;
                }
            }

            @media (max-width: 550px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                    gap: 10px;
                }

                .stat-card {
                    padding: 10px;
                }
            }

            @media (max-width: 380px) {
                .report-actions {
                    width: 100%;
                    margin-top: 8px;
                }

                .btn-view-report {
                    width: 100%;
                    justify-content: center;
                }

                .report-card {
                    flex-direction: column;
                    text-align: center;
                }

                .report-actions {
                    justify-content: center;
                }
            }

            /* ========================================
                                       RTL SUPPORT
                                       ======================================== */

            body.rtl .stat-card,
            body.rtl .report-card {
                text-align: right;
            }

            body.rtl .stat-info h3,
            body.rtl .stat-info p,
            body.rtl .report-info h3,
            body.rtl .report-info p {
                text-align: right;
            }

            body.rtl .btn-view-report i {
                margin-left: 6px;
                margin-right: 0;
            }

            body.rtl .chart-header h3 {
                justify-content: flex-start;
            }

            /* Animations */
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

            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .stat-card {
                animation: fadeInUp 0.4s ease forwards;
                opacity: 0;
            }

            .stat-card:nth-child(1) {
                animation-delay: 0s;
            }

            .stat-card:nth-child(2) {
                animation-delay: 0.05s;
            }

            .stat-card:nth-child(3) {
                animation-delay: 0.1s;
            }

            .stat-card:nth-child(4) {
                animation-delay: 0.15s;
            }

            .stat-card:nth-child(5) {
                animation-delay: 0.2s;
            }

            .stat-card:nth-child(6) {
                animation-delay: 0.25s;
            }

            .report-card {
                animation: scaleIn 0.4s ease forwards;
                opacity: 0;
            }

            .report-card:nth-child(1) {
                animation-delay: 0.1s;
            }

            .report-card:nth-child(2) {
                animation-delay: 0.15s;
            }

            .report-card:nth-child(3) {
                animation-delay: 0.2s;
            }

            .report-card:nth-child(4) {
                animation-delay: 0.25s;
            }

            .report-card:nth-child(5) {
                animation-delay: 0.3s;
            }

            .report-card:nth-child(6) {
                animation-delay: 0.35s;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            let activityChart = null;
            let sessionTypesChart = null;
            let chartsRendered = false;

            // Render Charts
            function renderCharts() {
                const currentLocale = '{{ app()->getLocale() }}';

                @php
                    // Get last 30 days activity data
                    $activityData = [];
                    for ($i = 29; $i >= 0; $i--) {
                        $date = Carbon\Carbon::now()->subDays($i);
                        $dateStart = $date->copy()->startOfDay();
                        $dateEnd = $date->copy()->endOfDay();

                        $activityData['labels'][] = $date->translatedFormat('M d');
                        $activityData['sessions'][] = \App\Models\TherapySession::whereBetween('session_datetime', [$dateStart, $dateEnd])->count();
                        $activityData['new_users'][] = \App\Models\User::role('patient')->whereBetween('created_at', [$dateStart, $dateEnd])->count();
                    }

                    $sessionTypesDist = [
                        'video' => \App\Models\TherapySession::where('session_type', 'video')->count(),
                        'audio' => \App\Models\TherapySession::where('session_type', 'audio')->count(),
                        'text' => \App\Models\TherapySession::where('session_type', 'text')->count(),
                    ];
                @endphp

                const activityOptions = {
                    series: [
                        { name: currentLocale === 'ar' ? 'جلسات' : 'Sessions', data: @json($activityData['sessions']), type: 'line' },
                        { name: currentLocale === 'ar' ? 'مستخدمين جدد' : 'New Users', data: @json($activityData['new_users']), type: 'line' }
                    ],
                    chart: {
                        type: 'line',
                        height: 300,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    stroke: { curve: 'smooth', width: 3 },
                    colors: ['#7c3aed', '#10b981'],
                    markers: { size: 4, hover: { size: 7 }, strokeColors: '#ffffff', strokeWidth: 2 },
                    tooltip: { enabled: true, shared: true, intersect: false, theme: 'dark' },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: {
                        categories: @json($activityData['labels']),
                        labels: { rotate: -45, style: { fontSize: '9px' }, maxHeight: 80 },
                        tickAmount: 10
                    },
                    yaxis: {
                        title: { text: currentLocale === 'ar' ? 'العدد' : 'Count', style: { fontSize: '11px' } },
                        labels: { formatter: (val) => Math.round(val) }
                    },
                    legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: { height: 250 },
                            xaxis: { labels: { rotate: -45, style: { fontSize: '8px' } } }
                        }
                    }]
                };

                const sessionTypesOptions = {
                    series: [{{ $sessionTypesDist['video'] }}, {{ $sessionTypesDist['audio'] }}, {{ $sessionTypesDist['text'] }}],
                    chart: {
                        type: 'donut',
                        height: 300,
                        toolbar: { show: false },
                        animations: { enabled: true, speed: 500 },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo' : 'Inter'
                    },
                    labels: [
                        currentLocale === 'ar' ? 'جلسات فيديو' : 'Video Sessions',
                        currentLocale === 'ar' ? 'جلسات صوتية' : 'Audio Sessions',
                        currentLocale === 'ar' ? 'جلسات نصية' : 'Text Sessions'
                    ],
                    colors: ['#7c3aed', '#10b981', '#f59e0b'],
                    legend: { position: 'bottom', labels: { colors: '#374151' } },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%',
                                labels: {
                                    show: true,
                                    name: { show: true, fontSize: '13px' },
                                    value: { show: true, fontSize: '14px', fontWeight: 'bold', formatter: (val) => val },
                                    total: { show: true, label: currentLocale === 'ar' ? 'المجموع' : 'Total', fontSize: '13px' }
                                }
                            }
                        }
                    },
                    tooltip: { y: { formatter: (val) => val + ' ' + (currentLocale === 'ar' ? 'جلسة' : 'sessions'), theme: 'dark' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 260 } } }]
                };

                const activityElement = document.querySelector("#activityChart");
                const sessionTypesElement = document.querySelector("#sessionTypesChart");

                if (activityElement && typeof ApexCharts !== 'undefined') {
                    if (activityChart) activityChart.destroy();
                    activityChart = new ApexCharts(activityElement, activityOptions);
                    activityChart.render();
                }

                if (sessionTypesElement && typeof ApexCharts !== 'undefined') {
                    if (sessionTypesChart) sessionTypesChart.destroy();
                    sessionTypesChart = new ApexCharts(sessionTypesElement, sessionTypesOptions);
                    sessionTypesChart.render();
                }

                chartsRendered = true;
            }

            // Wait for DOM and sidebar animation to complete
            document.addEventListener('DOMContentLoaded', function () {
                // Load charts after sidebar animation completes
                setTimeout(renderCharts, 350);

                // Re-render when sidebar toggle is clicked
                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', function () {
                        setTimeout(() => {
                            if (chartsRendered) renderCharts();
                        }, 300);
                    });
                }

                // Re-render when mobile sidebar opens/closes
                const mobileToggle = document.getElementById('mobileSidebarToggle');
                if (mobileToggle) {
                    mobileToggle.addEventListener('click', function () {
                        setTimeout(() => {
                            if (chartsRendered) renderCharts();
                        }, 350);
                    });
                }

                // Re-render on window resize (debounced)
                let resizeTimer;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        if (chartsRendered) renderCharts();
                    }, 250);
                });
            });
        </script>
    @endpush
@endsection