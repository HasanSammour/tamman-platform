{{-- resources/views/patient/tests/results.blade.php --}}
@extends('layouts.app')

@section('title', __(':test Results', ['test' => $testInfo['name']]) . ' - ' . __('Tamman'))

@section('page-title', __('Test Results'))

@section('content')
    <div class="results-container">
        <!-- Result Card -->
        <div class="result-card" style="border-top: 4px solid {{ $testInfo['color'] }}">
            <div class="result-header">
                <div class="test-icon" style="background: {{ $testInfo['bg'] }}; color: {{ $testInfo['color'] }}">
                    <i class="{{ $testInfo['icon'] }}"></i>
                </div>
                <div class="test-info">
                    <h1>{{ $testInfo['name'] }}</h1>
                    <p>{{ $testResult->test_date->translatedFormat('l, M d, Y') }}</p>
                </div>
            </div>

            <div class="score-section">
                <div class="score-circle">
                    <div class="score-number">{{ $testResult->score }}</div>
                    <div class="score-max">/ {{ end($ranges)['max'] }}</div>
                </div>
                <div class="score-level {{ $testResult->result_level }}">
                    {{ $testResult->getResultLevelArAttribute() }}
                </div>
            </div>

            <div class="interpretation-section">
                <h3><i class="fas fa-chart-line"></i> {{ __('Interpretation') }}</h3>
                <p>{{ app()->getLocale() === 'ar' ? $interpretation['ar'] : $interpretation['en'] }}</p>
            </div>

            <div class="recommendations-section">
                <h3><i class="fas fa-lightbulb"></i> {{ __('Recommendations') }}</h3>
                <ul>
                    <li>{{ __('Share these results with your mental health professional') }}</li>
                    <li>{{ __('Take this test again next month to track your progress') }}</li>
                    <li>{{ __('Continue practicing self-care and healthy habits') }}</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="{{ route('patient.tests') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Tests') }}
                </a>
                <a href="{{ route('patient.tests.history') }}" class="btn-history">
                    <i class="fas fa-history"></i> {{ __('View History') }}
                </a>
            </div>
        </div>

        <!-- Previous Results Chart with ApexCharts -->
        @if($previousResults->count() > 0)
            <div class="trend-card">
                <h3><i class="fas fa-chart-line"></i> {{ __('Your Progress Over Time') }}</h3>
                <div class="trend-chart-container">
                    <div id="trendChart" class="apex-chart"></div>
                </div>
            </div>
        @endif

        <!-- Share Results Section -->
        <div class="share-card">
            <i class="fas fa-share-alt"></i>
            <div class="share-content">
                <h4>{{ __('Share with your specialist') }}</h4>
                <p>{{ __('You can share these results with your mental health professional to help them better understand your situation.') }}
                </p>
            </div>
            <button class="btn-share" id="shareResultsBtn">
                <i class="fas fa-envelope"></i> {{ __('Share with Specialist') }}
            </button>
        </div>
    </div>

    @push('styles')
        <style>
            .results-container {
                max-width: 800px;
                margin: 0 auto;
            }

            .result-card {
                background: white;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
            }

            .result-header {
                padding: 30px;
                display: flex;
                align-items: center;
                gap: 20px;
                border-bottom: 1px solid #f3f4f6;
            }

            .test-icon {
                width: 60px;
                height: 60px;
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .test-icon i {
                font-size: 1.6rem;
            }

            .test-info h1 {
                font-size: 1.3rem;
                margin-bottom: 5px;
                color: #1f2937;
            }

            .test-info p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            .score-section {
                padding: 30px;
                text-align: center;
                border-bottom: 1px solid #f3f4f6;
            }

            .score-circle {
                display: inline-flex;
                align-items: baseline;
                gap: 5px;
                margin-bottom: 15px;
            }

            .score-number {
                font-size: 3rem;
                font-weight: 800;
                color: #1f2937;
            }

            .score-max {
                font-size: 1.2rem;
                color: #6b7280;
            }

            .score-level {
                display: inline-block;
                padding: 8px 20px;
                border-radius: 40px;
                font-size: 0.9rem;
                font-weight: 600;
            }

            .score-level.minimal {
                background: #d1fae5;
                color: #065f46;
            }

            .score-level.mild {
                background: #fef3c7;
                color: #92400e;
            }

            .score-level.moderate {
                background: #fed7aa;
                color: #9a3412;
            }

            .score-level.moderately_severe {
                background: #fed7aa;
                color: #9a3412;
            }

            .score-level.severe {
                background: #fee2e2;
                color: #991b1b;
            }

            .score-level.none {
                background: #d1fae5;
                color: #065f46;
            }

            .score-level.subthreshold {
                background: #fef3c7;
                color: #92400e;
            }

            .score-level.low {
                background: #d1fae5;
                color: #065f46;
            }

            .score-level.high {
                background: #fee2e2;
                color: #991b1b;
            }

            .interpretation-section,
            .recommendations-section {
                padding: 25px 30px;
                border-bottom: 1px solid #f3f4f6;
            }

            .interpretation-section h3,
            .recommendations-section h3 {
                font-size: 1rem;
                margin-bottom: 12px;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .interpretation-section p {
                font-size: 0.9rem;
                color: #4b5563;
                line-height: 1.6;
                margin: 0;
            }

            .recommendations-section ul {
                margin: 0;
                padding-left: 20px;
            }

            .recommendations-section li {
                font-size: 0.85rem;
                color: #4b5563;
                margin-bottom: 8px;
            }

            .action-buttons {
                padding: 25px 30px;
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .btn-back,
            .btn-history {
                padding: 10px 24px;
                border-radius: 40px;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-back {
                background: #f3f4f6;
                color: #4b5563;
            }

            .btn-back:hover {
                background: #e5e7eb;
                transform: translateX(-2px);
                color: #4b5563;
            }

            .btn-history {
                background: #7c3aed;
                color: white;
            }

            .btn-history:hover {
                background: #6d28d9;
                transform: translateX(2px);
                color: white;
            }

            .trend-card {
                background: white;
                border-radius: 24px;
                padding: 25px;
                margin-bottom: 25px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .trend-card h3 {
                font-size: 1rem;
                margin-bottom: 20px;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .trend-chart-container {
                position: relative;
                width: 100%;
                min-height: 320px;
            }

            .apex-chart {
                width: 100%;
                min-height: 300px;
            }

            .share-card {
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                border-radius: 20px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 20px;
                flex-wrap: wrap;
            }

            .share-card i {
                font-size: 2rem;
                color: #7c3aed;
            }

            .share-content {
                flex: 1;
            }

            .share-content h4 {
                font-size: 0.9rem;
                margin-bottom: 5px;
                color: #1f2937;
            }

            .share-content p {
                font-size: 0.75rem;
                color: #6b7280;
                margin: 0;
            }

            .btn-share {
                background: #f59e0b;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 40px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .btn-share:hover {
                background: #d97706;
                transform: translateY(-2px);
                color: white;
            }

            @media (max-width: 768px) {
                .result-header {
                    flex-direction: column;
                    text-align: center;
                }

                .action-buttons {
                    flex-direction: column;
                }

                .btn-back,
                .btn-history {
                    justify-content: center;
                }

                .share-card {
                    flex-direction: column;
                    text-align: center;
                }
            }

            body.rtl .recommendations-section ul {
                padding-right: 20px;
                padding-left: 0;
            }

            body.rtl .btn-back i,
            body.rtl .btn-history i {
                transform: rotate(180deg);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const currentLocale = '{{ app()->getLocale() }}';
            let trendChart = null;
            let chartInitTimeout = null;

            @if($previousResults->count() > 0)
                // Prepare chart data
                const dates = {!! json_encode($previousResults->reverse()->pluck('test_date')->map(function ($date) {
                    return $date->translatedFormat('M d, Y');
                })) !!};
                const scores = {!! json_encode($previousResults->reverse()->pluck('score')) !!};

                // Add current result
                dates.push('{{ $testResult->test_date->translatedFormat("M d, Y") }}');
                scores.push({{ $testResult->score }});

                const maxScore = {{ end($ranges)['max'] }};

                function renderTrendChart() {
                    if (trendChart) {
                        trendChart.destroy();
                        trendChart = null;
                    }

                    const options = {
                        series: [{
                            name: currentLocale === 'ar' ? 'الدرجة' : 'Score',
                            data: scores
                        }],
                        chart: {
                            type: 'line',
                            height: 320,
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
                            size: 6,
                            hover: { size: 10 },
                            colors: ['#7c3aed'],
                            strokeColors: '#ffffff',
                            strokeWidth: 2,
                            radius: 6
                        },
                        tooltip: {
                            enabled: true,
                            shared: true,
                            intersect: false,
                            theme: 'dark',
                            style: { fontSize: '12px' },
                            y: {
                                formatter: (value) => value + ' / ' + maxScore,
                                title: { formatter: () => currentLocale === 'ar' ? 'الدرجة: ' : 'Score: ' }
                            }
                        },
                        grid: {
                            borderColor: '#e5e7eb',
                            strokeDashArray: 4
                        },
                        xaxis: {
                            categories: dates,
                            title: { text: currentLocale === 'ar' ? 'التاريخ' : 'Date', style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' } },
                            labels: { rotate: -35, style: { fontSize: '10px' } }
                        },
                        yaxis: {
                            min: 0,
                            max: maxScore,
                            title: { text: currentLocale === 'ar' ? 'الدرجة' : 'Score', style: { fontSize: '12px', fontWeight: 500, color: '#6b7280' } },
                            labels: { formatter: (value) => Math.round(value) }
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 0.3,
                                opacityFrom: 0.4,
                                opacityTo: 0.1,
                                stops: [0, 100]
                            }
                        },
                        legend: { show: true, position: 'top', labels: { colors: '#374151' } },
                        responsive: [{ breakpoint: 768, options: { chart: { height: 280 }, markers: { size: 4 } } }]
                    };

                    const element = document.querySelector("#trendChart");
                    if (element && typeof ApexCharts !== 'undefined') {
                        trendChart = new ApexCharts(element, options);
                        trendChart.render();
                    }
                }

                // Render chart after layout loads
                function initTrendChart() {
                    if (chartInitTimeout) {
                        clearTimeout(chartInitTimeout);
                    }
                    chartInitTimeout = setTimeout(renderTrendChart, 500);
                }

                window.addEventListener('load', initTrendChart);
                document.addEventListener('DOMContentLoaded', initTrendChart);

                // Handle sidebar toggle
                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', function () {
                        setTimeout(() => { if (trendChart) trendChart.resize(); }, 400);
                    });
                }

                const mobileToggle = document.getElementById('mobileSidebarToggle');
                if (mobileToggle) {
                    mobileToggle.addEventListener('click', function () {
                        setTimeout(() => { if (trendChart) trendChart.resize(); }, 450);
                    });
                }

                let resizeTimer;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => { if (trendChart) trendChart.resize(); }, 250);
                });
            @endif

            // Share results button
            document.getElementById('shareResultsBtn')?.addEventListener('click', async () => {
                const result = await Swal.fire({
                    title: '{{ __("Share Results") }}',
                    text: '{{ __("Do you want to share these results with your specialist?") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __("Yes, share") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                });

                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Shared!") }}',
                        text: '{{ __("Your results have been shared with your specialist.") }}',
                        timer: 2000,
                        showConfirmButton: false,
                        confirmButtonColor: '#7c3aed'
                    });
                }
            });
        </script>
    @endpush

@endsection