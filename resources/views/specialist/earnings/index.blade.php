{{-- resources/views/specialist/earnings/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Earnings') . ' - ' . __('Tamman'))

@section('page-title', __('My Earnings'))

@section('content')
    <div class="earnings-container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card animate-fade-in-up">
                <div class="stat-icon purple">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>${{ number_format($stats['total_earnings'], 2) }}</h3>
                    <p>{{ __('Total Earnings') }}</p>
                    <small>{{ __('All time') }}</small>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon green">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>${{ number_format($stats['current_month_earnings'], 2) }}</h3>
                    <p>{{ __('This Month') }}</p>
                    <small>{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>${{ number_format($stats['pending_payout'], 2) }}</h3>
                    <p>{{ __('Pending Payout') }}</p>
                    <small>{{ __('Awaiting transfer') }}</small>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-icon teal">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                    <p>{{ __('Completed Sessions') }}</p>
                    <small>{{ $stats['total_clients'] }} {{ __('clients') }}</small>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> {{ __('Monthly Earnings') }}</h3>
                    <span class="chart-subtitle">{{ __('Last 6 months') }}</span>
                </div>
                <div class="chart-body">
                    <div id="earningsChart" class="apex-chart"></div>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-bar"></i> {{ __('Monthly Sessions') }}</h3>
                    <span class="chart-subtitle">{{ __('Last 6 months') }}</span>
                </div>
                <div class="chart-body">
                    <div id="sessionsChart" class="apex-chart"></div>
                </div>
            </div>
        </div>

        <!-- Session Type Breakdown & Payment History -->
        <div class="details-grid">
            <!-- Session Type Breakdown -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> {{ __('Sessions Breakdown') }}</h3>
                    <button class="btn-refresh" id="refreshBreakdownBtn" title="{{ __('Refresh') }}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body no-scrollbar" id="breakdownContent">
                    <div class="loading-spinner-small"></div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> {{ __('Payment History') }}</h3>
                    <button class="btn-refresh" id="refreshPaymentsBtn" title="{{ __('Refresh') }}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body no-scrollbar">
                    <div id="paymentsList">
                        <div class="loading-spinner-small"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Payout Section -->
        <div class="payout-section">
            <div class="payout-card">
                <div class="payout-info">
                    <div class="payout-icon">
                        <i class="fas fa-hand-holding-usd" style="font-size: 1.8rem; color: #fbbf24;"></i>
                    </div>
                    <div class="payout-text">
                        <h3>{{ __('Request Payout') }}</h3>
                        <p>{{ __('Minimum payout amount is $50. Payments are processed within 5-7 business days.') }}</p>
                        @if($stats['pending_payout'] >= 50)
                            <div class="payout-amount">
                                <span>{{ __('Available for payout') }}:</span>
                                <strong>${{ number_format($stats['pending_payout'], 2) }}</strong>
                            </div>
                        @else
                            <div class="payout-warning">
                                <i class="fas fa-info-circle"></i>
                                {{ __('You need at least $50 to request payout. Current pending: $:amount', ['amount' => number_format($stats['pending_payout'], 2)]) }}
                            </div>
                        @endif
                    </div>
                </div>
                @if($stats['pending_payout'] >= 50)
                    <button class="btn-request-payout" id="requestPayoutBtn">
                        <i class="fas fa-paper-plane"></i> {{ __('Request Payout') }}
                    </button>
                @else
                    <button class="btn-request-payout disabled" disabled>
                        <i class="fas fa-lock"></i> {{ __('Minimum $50 Required') }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .earnings-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Stats Grid */
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
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 55px;
                height: 55px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .stat-icon i {
                font-size: 1.5rem;
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

            /* Charts Row */
            .charts-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 25px;
                margin-bottom: 30px;
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
                grid-template-columns: 1fr 1fr;
                gap: 25px;
                margin-bottom: 30px;
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

            .btn-refresh {
                background: #f3f4f6;
                border: none;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #6b7280;
            }

            .btn-refresh:hover {
                background: #e5e7eb;
                transform: rotate(180deg);
            }

            /* Remove scrollbar from card body */
            .card-body.no-scrollbar {
                padding: 16px 20px;
                max-height: none;
                overflow-y: visible;
            }

            /* Desktop Breakdown Items */
            .breakdown-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 15px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .breakdown-item:last-child {
                border-bottom: none;
            }

            .breakdown-icon {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .breakdown-icon.video {
                background: #ede9fe;
                color: #7c3aed;
            }

            .breakdown-icon.audio {
                background: #d1fae5;
                color: #059669;
            }

            .breakdown-icon.text {
                background: #fef3c7;
                color: #d97706;
            }

            .breakdown-icon i {
                font-size: 1.2rem;
            }

            .breakdown-info {
                flex: 1;
            }

            .breakdown-title {
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 4px;
            }

            .breakdown-stats {
                font-size: 0.75rem;
                color: #6b7280;
            }

            .breakdown-earnings {
                text-align: right;
            }

            .breakdown-earnings .amount {
                font-weight: 700;
                color: #1f2937;
                display: block;
            }

            .breakdown-earnings .count {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            /* Mobile Breakdown Cards */
            .breakdown-cards {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .breakdown-card {
                background: white;
                border-radius: 20px;
                padding: 18px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                border: 1px solid #f0f0f0;
                transition: all 0.3s ease;
            }

            .breakdown-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .breakdown-card .card-icon {
                width: 50px;
                height: 50px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .breakdown-card.video .card-icon {
                background: #ede9fe;
                color: #7c3aed;
            }

            .breakdown-card.audio .card-icon {
                background: #d1fae5;
                color: #059669;
            }

            .breakdown-card.text .card-icon {
                background: #fef3c7;
                color: #d97706;
            }

            .breakdown-card .card-icon i {
                font-size: 1.3rem;
            }

            .breakdown-card .card-info {
                flex: 1;
            }

            .breakdown-card .card-info h4 {
                margin: 0 0 4px;
                font-size: 0.9rem;
                font-weight: 600;
                color: #1f2937;
            }

            .breakdown-card .card-info .stats {
                margin: 0 0 2px;
                font-size: 0.7rem;
                color: #6b7280;
            }

            .breakdown-card .card-info .fee {
                margin: 0;
                font-size: 0.65rem;
                color: #9ca3af;
            }

            .breakdown-card .card-earnings {
                text-align: right;
            }

            .breakdown-card .card-earnings .amount {
                font-weight: 700;
                font-size: 1.1rem;
                color: #10b981;
            }

            /* Payment History Items */
            .payment-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .payment-item:last-child {
                border-bottom: none;
            }

            .payment-month {
                min-width: 80px;
                font-weight: 600;
                color: #1f2937;
            }

            .payment-details {
                flex: 1;
            }

            .payment-amount {
                font-weight: 700;
                color: #10b981;
            }

            .payment-fee {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .payment-status {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 500;
            }

            .payment-status.paid {
                background: #d1fae5;
                color: #065f46;
            }

            .payment-status.pending {
                background: #fef3c7;
                color: #d97706;
            }

            .payment-status.failed {
                background: #fee2e2;
                color: #991b1b;
            }

            .btn-invoice {
                background: #f3f4f6;
                border: none;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.7rem;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #7c3aed;
            }

            .btn-invoice:hover {
                background: #ede9fe;
            }

            .empty-state {
                text-align: center;
                padding: 40px 20px;
            }

            .empty-state i {
                font-size: 2.5rem;
                color: #c4b5fd;
                margin-bottom: 12px;
            }

            .empty-state p {
                color: #6b7280;
                margin: 0;
            }

            /* Payout Section - Fixed Icon */
            .payout-section {
                margin-top: 0;
            }

            .payout-card {
                background: linear-gradient(135deg, #1e1b4b, #2e1065);
                border-radius: 24px;
                padding: 25px 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
            }

            .payout-info {
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .payout-icon {
                width: 60px;
                height: 60px;
                background: rgba(255, 255, 255, 0.15);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .payout-icon i {
                font-size: 1.8rem;
                color: #fbbf24;
                display: block;
            }

            .payout-text h3 {
                color: white;
                margin: 0 0 5px;
                font-size: 1.2rem;
            }

            .payout-text p {
                color: rgba(255, 255, 255, 0.7);
                margin: 0;
                font-size: 0.8rem;
            }

            .payout-amount {
                margin-top: 8px;
                color: #fbbf24;
                font-size: 0.9rem;
            }

            .payout-warning {
                margin-top: 8px;
                background: rgba(245, 158, 11, 0.2);
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.75rem;
                color: #fbbf24;
                display: inline-block;
            }

            .btn-request-payout {
                background: #fbbf24;
                color: #1e1b4b;
                border: none;
                padding: 12px 28px;
                border-radius: 40px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                flex-shrink: 0;
            }

            .btn-request-payout:hover:not(.disabled) {
                background: #f59e0b;
                transform: translateY(-2px);
            }

            .btn-request-payout.disabled {
                opacity: 0.5;
                cursor: not-allowed;
                background: #6b7280;
            }

            /* Loading Spinner */
            .loading-spinner-small {
                width: 40px;
                height: 40px;
                border: 3px solid #e5e7eb;
                border-top-color: #7c3aed;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 30px auto;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
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

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease forwards;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .stats-grid {
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
            }

            /* Mobile Improvements */
            @media (max-width: 768px) {
                .earnings-container {
                    padding: 15px;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .payout-card {
                    flex-direction: column;
                    text-align: center;
                }

                .payout-info {
                    flex-direction: column;
                    text-align: center;
                }

                .payment-item {
                    flex-wrap: wrap;
                }

                .payment-month {
                    width: 100%;
                }

                /* Hide desktop breakdown items on mobile */
                .breakdown-item {
                    display: none;
                }

                /* Show mobile cards */
                .breakdown-cards {
                    display: flex;
                }
            }

            /* Desktop: Show normal items, hide mobile cards */
            @media (min-width: 769px) {
                .breakdown-cards {
                    display: none;
                }

                .breakdown-item {
                    display: flex;
                }
            }

            /* Small mobile devices (under 480px) */
            @media (max-width: 480px) {
                .breakdown-card {
                    flex-wrap: wrap;
                    text-align: center;
                    justify-content: center;
                }

                .breakdown-card .card-info {
                    text-align: center;
                    width: 100%;
                }

                .breakdown-card .card-earnings {
                    text-align: center;
                    width: 100%;
                    margin-top: 8px;
                    padding-top: 8px;
                    border-top: 1px solid #e5e7eb;
                }

                /* Fix payout icon on very small screens */
                .payout-icon {
                    width: 50px;
                    height: 50px;
                }

                .payout-icon i {
                    font-size: 1.5rem;
                }
            }

            /* RTL Support */
            body.rtl .breakdown-earnings {
                text-align: right;
            }

            body.rtl .btn-refresh:hover {
                transform: rotate(-180deg);
            }

            body.rtl .payment-item {
                flex-direction: row;
            }

            /* RTL for mobile cards */
            body.rtl .breakdown-card {
                direction: rtl;
            }

            body.rtl .breakdown-card .card-earnings {
                text-align: left;
            }

            @media (max-width: 480px) {
                body.rtl .breakdown-card .card-earnings {
                    text-align: center;
                }

                body.rtl .breakdown-card .card-info {
                    text-align: center;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Chart data from PHP
            const earningsMonths = @json($monthlyEarningsData['months']);
            const earningsValues = @json($monthlyEarningsData['earnings']);
            const sessionsMonths = @json($monthlySessionsData['months']);
            const sessionsValues = @json($monthlySessionsData['sessions']);
            const currentLocale = '{{ app()->getLocale() }}';
            const totalSessions = {{ number_format($stats['total_sessions']) }};
            const videoCount = {{ number_format($stats['video_count']) }};

            let earningsChart = null;
            let sessionsChart = null;

            // Render Earnings Chart
            function renderEarningsChart() {
                const element = document.querySelector("#earningsChart");
                if (!element) return;
                if (earningsChart) earningsChart.destroy();

                earningsChart = new ApexCharts(element, {
                    series: [{
                        name: currentLocale === 'ar' ? 'الأرباح (دولار)' : 'Earnings (USD)',
                        data: earningsValues
                    }],
                    chart: {
                        type: 'bar',
                        height: 280,
                        toolbar: { show: false },
                        animations: { enabled: true },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    plotOptions: {
                        bar: { borderRadius: 8, columnWidth: '60%' }
                    },
                    colors: ['#10b981'],
                    tooltip: {
                        y: {
                            formatter: (val) => '$' + val.toFixed(2),
                            title: { formatter: () => currentLocale === 'ar' ? 'الأرباح: ' : 'Earnings: ' }
                        },
                        theme: 'dark'
                    },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: {
                        categories: earningsMonths,
                        labels: { rotate: -35, style: { fontSize: '10px' } }
                    },
                    yaxis: {
                        title: { text: currentLocale === 'ar' ? 'الأرباح (دولار)' : 'Earnings (USD)' },
                        labels: { formatter: (val) => '$' + val.toFixed(0) }
                    },
                    legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 240 } } }]
                });
                earningsChart.render();
            }

            // Render Sessions Chart
            function renderSessionsChart() {
                const element = document.querySelector("#sessionsChart");
                if (!element) return;
                if (sessionsChart) sessionsChart.destroy();

                sessionsChart = new ApexCharts(element, {
                    series: [{
                        name: currentLocale === 'ar' ? 'عدد الجلسات' : 'Number of Sessions',
                        data: sessionsValues
                    }],
                    chart: {
                        type: 'line',
                        height: 280,
                        toolbar: { show: false },
                        animations: { enabled: true },
                        background: 'transparent',
                        fontFamily: currentLocale === 'ar' ? 'Cairo, sans-serif' : 'Inter, sans-serif'
                    },
                    stroke: { curve: 'smooth', width: 3, colors: ['#7c3aed'] },
                    markers: { size: 5, hover: { size: 8 }, colors: ['#7c3aed'], strokeColors: '#fff' },
                    tooltip: { y: { formatter: (val) => val, title: { formatter: () => currentLocale === 'ar' ? 'الجلسات: ' : 'Sessions: ' } }, theme: 'dark' },
                    grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                    xaxis: { categories: sessionsMonths, labels: { rotate: -35, style: { fontSize: '10px' } } },
                    yaxis: { title: { text: currentLocale === 'ar' ? 'عدد الجلسات' : 'Number of Sessions' } },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.4, opacityTo: 0.1 } },
                    legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                    responsive: [{ breakpoint: 768, options: { chart: { height: 240 } } }]
                });
                sessionsChart.render();
            }

            // Load Session Breakdown via AJAX
            async function loadSessionBreakdown() {
                const container = document.getElementById('breakdownContent');
                container.innerHTML = '<div class="loading-spinner-small"></div>';

                try {
                    const response = await fetch('{{ route("specialist.earnings.session-breakdown") }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success) {
                        const breakdown = data.breakdown;
                        const isMobile = window.innerWidth <= 768;

                        if (isMobile) {
                            // Mobile card layout
                            container.innerHTML = `
                                        <div class="breakdown-cards">
                                            <div class="breakdown-card video">
                                                <div class="card-icon"><i class="fas fa-video"></i></div>
                                                <div class="card-info">
                                                    <h4>{{ __('Video Sessions') }}</h4>
                                                    <p class="stats">${breakdown.video.count} {{ __('sessions') }} • ${videoCount} {{ __('completed') }}</p>
                                                    <p class="fee">{{ __('100% of consultation fee') }}</p>
                                                </div>
                                                <div class="card-earnings">
                                                    <span class="amount">$${breakdown.video.earnings.toFixed(2)}</span>
                                                </div>
                                            </div>
                                            <div class="breakdown-card audio">
                                                <div class="card-icon"><i class="fas fa-phone-alt"></i></div>
                                                <div class="card-info">
                                                    <h4>{{ __('Audio Sessions') }}</h4>
                                                    <p class="stats">${breakdown.audio.count} {{ __('sessions') }}</p>
                                                    <p class="fee">{{ __('90% of consultation fee') }}</p>
                                                </div>
                                                <div class="card-earnings">
                                                    <span class="amount">$${breakdown.audio.earnings.toFixed(2)}</span>
                                                </div>
                                            </div>
                                            <div class="breakdown-card text">
                                                <div class="card-icon"><i class="fas fa-comment-dots"></i></div>
                                                <div class="card-info">
                                                    <h4>{{ __('Text Sessions') }}</h4>
                                                    <p class="stats">${breakdown.text.count} {{ __('sessions') }}</p>
                                                    <p class="fee">{{ __('80% of consultation fee') }}</p>
                                                </div>
                                                <div class="card-earnings">
                                                    <span class="amount">$${breakdown.text.earnings.toFixed(2)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                        } else {
                            // Desktop layout
                            container.innerHTML = `
                                        <div class="breakdown-item">
                                            <div class="breakdown-icon video"><i class="fas fa-video"></i></div>
                                            <div class="breakdown-info">
                                                <div class="breakdown-title">{{ __('Video Sessions') }}</div>
                                                <div class="breakdown-stats">${totalSessions} {{ __('sessions') }} • ${videoCount} {{ __('completed') }}</div>
                                            </div>
                                            <div class="breakdown-earnings">
                                                <span class="amount">$${breakdown.video.earnings.toFixed(2)}</span>
                                                <span class="count">${breakdown.video.count} {{ __('sessions') }}</span>
                                            </div>
                                        </div>
                                        <div class="breakdown-item">
                                            <div class="breakdown-icon audio"><i class="fas fa-phone-alt"></i></div>
                                            <div class="breakdown-info">
                                                <div class="breakdown-title">{{ __('Audio Sessions') }}</div>
                                                <div class="breakdown-stats">{{ __('90% of consultation fee') }}</div>
                                            </div>
                                            <div class="breakdown-earnings">
                                                <span class="amount">$${breakdown.audio.earnings.toFixed(2)}</span>
                                                <span class="count">${breakdown.audio.count} {{ __('sessions') }}</span>
                                            </div>
                                        </div>
                                        <div class="breakdown-item">
                                            <div class="breakdown-icon text"><i class="fas fa-comment-dots"></i></div>
                                            <div class="breakdown-info">
                                                <div class="breakdown-title">{{ __('Text Sessions') }}</div>
                                                <div class="breakdown-stats">{{ __('80% of consultation fee') }}</div>
                                            </div>
                                            <div class="breakdown-earnings">
                                                <span class="amount">$${breakdown.text.earnings.toFixed(2)}</span>
                                                <span class="count">${breakdown.text.count} {{ __('sessions') }}</span>
                                            </div>
                                        </div>
                                    `;
                        }
                    } else {
                        container.innerHTML = '<div class="empty-state"><i class="fas fa-chart-pie"></i><p>{{ __("No data available") }}</p></div>';
                    }
                } catch (error) {
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>{{ __("Error loading data") }}</p></div>';
                }
            }

            // Load Payment History via AJAX
            async function loadPaymentHistory() {
                const container = document.getElementById('paymentsList');
                container.innerHTML = '<div class="loading-spinner-small"></div>';

                try {
                    const response = await fetch('{{ route("specialist.earnings.payments") }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.success && data.payments.length > 0) {
                        container.innerHTML = data.payments.map(payment => `
                                    <div class="payment-item">
                                        <div class="payment-month">${payment.month_year}</div>
                                        <div class="payment-details">
                                            <div class="payment-amount">$${parseFloat(payment.final_amount).toFixed(2)}</div>
                                            <div class="payment-fee">{{ __("Platform fee") }}: $${parseFloat(payment.platform_fee).toFixed(2)}</div>
                                        </div>
                                        <div>
                                            <span class="payment-status ${payment.status}">${payment.status_text}</span>
                                        </div>
                                        <div>
                                            ${payment.status === 'paid' ?
                                `<button class="btn-invoice" onclick="downloadInvoice(${payment.id})">
                                                    <i class="fas fa-file-pdf"></i> {{ __('PDF') }}
                                                </button>` :
                                `<span class="text-muted" style="font-size: 0.7rem;">—</span>`
                            }
                                        </div>
                                    </div>
                                `).join('');
                    } else {
                        container.innerHTML = '<div class="empty-state"><i class="fas fa-history"></i><p>{{ __("No payment history found") }}</p></div>';
                    }
                } catch (error) {
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>{{ __("Error loading payments") }}</p></div>';
                }
            }

            // Download Invoice
            function downloadInvoice(paymentId) {
                const button = event.currentTarget;
                const originalHtml = button.innerHTML;

                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

                // Create a hidden iframe
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = `/specialist/earnings/invoice/${paymentId}`;
                document.body.appendChild(iframe);

                // Remove iframe after download starts
                setTimeout(() => {
                    document.body.removeChild(iframe);
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                }, 2000);
            }

            // Request Payout
            document.getElementById('requestPayoutBtn')?.addEventListener('click', async () => {
                const result = await Swal.fire({
                    title: '{{ __("Request Payout") }}',
                    text: '{{ __("Are you sure you want to request a payout?") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, Request Payout") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (!result.isConfirmed) return;

                const btn = document.getElementById('requestPayoutBtn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}';
                btn.disabled = true;

                try {
                    const response = await fetch('{{ route("specialist.earnings.request-payout") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: '{{ __("Request Submitted") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                        location.reload();
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: data.message,
                            confirmButtonColor: '#7c3aed'
                        });
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                } catch (error) {
                    await Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Network error. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            });

            // Refresh buttons
            document.getElementById('refreshBreakdownBtn')?.addEventListener('click', loadSessionBreakdown);
            document.getElementById('refreshPaymentsBtn')?.addEventListener('click', loadPaymentHistory);

            // Add resize listener to reload breakdown when screen size changes
            let breakdownResizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(breakdownResizeTimer);
                breakdownResizeTimer = setTimeout(() => {
                    loadSessionBreakdown();
                    renderEarningsChart();
                    renderSessionsChart();
                }, 300);
            });

            // Initialize all charts and data
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(() => {
                    renderEarningsChart();
                    renderSessionsChart();
                    loadSessionBreakdown();
                    loadPaymentHistory();
                }, 300);

                // Re-render charts when sidebar toggles
                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', () => {
                        setTimeout(() => {
                            renderEarningsChart();
                            renderSessionsChart();
                        }, 350);
                    });
                }

                // Re-render on window resize
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        renderEarningsChart();
                        renderSessionsChart();
                    }, 250);
                });
            });
        </script>
    @endpush

@endsection