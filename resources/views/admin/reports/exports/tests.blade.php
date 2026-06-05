{{-- resources/views/admin/reports/exports/tests.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Tests Report') }} - {{ config('app.name') }}</title>
    <style>
        @page {
            margin: 1.5cm;
            header: page-header;
            footer: page-footer;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #7c3aed;
        }

        .header h1 {
            color: #7c3aed;
            font-size: 20px;
            margin: 0;
        }

        .header p {
            color: #666;
            font-size: 9px;
            margin-top: 3px;
        }

        .report-info {
            margin-bottom: 15px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 8px;
        }

        .report-info strong {
            color: #333;
        }

        .stats-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 12px;
            margin-bottom: 20px;
            width: 100%;
        }

        .stat-card {
            flex: 1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .stat-card .number {
            font-size: 18px;
            font-weight: bold;
            color: #7c3aed;
            display: block;
        }

        .stat-card .label {
            font-size: 8px;
            color: #666;
            margin-top: 4px;
        }

        /* Test Types Summary - Using Table instead of Flex */
        .test-types-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .test-types-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            background: #f8f9fa;
            width: 33.33%;
        }

        .test-type-card {
            text-align: center;
        }

        .test-type-card.phq9 .test-border {
            border-top: 3px solid #7c3aed;
            padding-top: 8px;
        }

        .test-type-card.gad7 .test-border {
            border-top: 3px solid #10b981;
            padding-top: 8px;
        }

        .test-type-card.pcl5 .test-border {
            border-top: 3px solid #f59e0b;
            padding-top: 8px;
        }

        .test-type-card.isi .test-border {
            border-top: 3px solid #ef4444;
            padding-top: 8px;
        }

        .test-type-card.pss .test-border {
            border-top: 3px solid #ec4899;
            padding-top: 8px;
        }

        .test-type-card.cis .test-border {
            border-top: 3px solid #06b6d4;
            padding-top: 8px;
        }

        .test-type-card .title {
            font-size: 11px;
            font-weight: bold;
        }

        .test-type-card .subtitle {
            font-size: 7px;
            color: #666;
        }

        .test-type-card .count {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            display: block;
            margin-top: 5px;
        }

        .test-type-card .avg {
            font-size: 8px;
            color: #9ca3af;
        }

        .tests-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tests-table th {
            background: #7c3aed;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
        }

        .tests-table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8px;
            text-align: center;
        }

        .tests-table th:first-child,
        .tests-table td:first-child {
            text-align: left;
        }

        .test-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 7px;
        }

        .test-phq9 {
            background: rgba(124, 58, 237, 0.12);
            color: #7c3aed;
        }

        .test-gad7 {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
        }

        .test-pcl5 {
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
        }

        .test-isi {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
        }

        .test-pss {
            background: rgba(236, 72, 153, 0.12);
            color: #ec4899;
        }

        .test-cis {
            background: rgba(6, 182, 212, 0.12);
            color: #06b6d4;
        }

        .level-minimal {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .level-mild {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .level-moderate {
            background: #fed7aa;
            color: #9a3412;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .level-severe {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 7px;
            color: #999;
        }

        body.rtl {
            direction: rtl;
        }

        body.rtl .tests-table th:first-child,
        body.rtl .tests-table td:first-child {
            text-align: right;
        }
    </style>
</head>

<body>

    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Tests Report') }}</p>
        </div>
    </htmlpageheader>

    <div class="content">

        <!-- 3 Stats Cards in One Row -->
        <div class="stats-row">
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_tests'] ?? 0) }}</span>
                <span class="label">{{ __('Total Tests') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['unique_users'] ?? 0) }}</span>
                <span class="label">{{ __('Unique Users') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['avg_score'] ?? 0, 1) }}</span>
                <span class="label">{{ __('Average Score') }}</span>
            </div>
        </div>

        <!-- Test Types Summary - Using HTML Table instead of Flex -->
        <table class="test-types-table" cellspacing="0">
            <tbody>
                <tr>
                    <td>
                        <div class="test-type-card phq9">
                            <div class="test-border">
                                <div class="title">PHQ-9</div>
                                <div class="subtitle">{{ __('Depression') }}</div>
                                <span class="count">{{ number_format($stats['phq9_count'] ?? 0) }}</span>
                                <div class="avg">{{ __('Avg Score') }}: {{ number_format($stats['phq9_avg'] ?? 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="test-type-card gad7">
                            <div class="test-border">
                                <div class="title">GAD-7</div>
                                <div class="subtitle">{{ __('Anxiety') }}</div>
                                <span class="count">{{ number_format($stats['gad7_count'] ?? 0) }}</span>
                                <div class="avg">{{ __('Avg Score') }}: {{ number_format($stats['gad7_avg'] ?? 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="test-type-card pcl5">
                            <div class="test-border">
                                <div class="title">PCL-5</div>
                                <div class="subtitle">{{ __('PTSD') }}</div>
                                <span class="count">{{ number_format($stats['pcl5_count'] ?? 0) }}</span>
                                <div class="avg">{{ __('Avg Score') }}: {{ number_format($stats['pcl5_avg'] ?? 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="test-type-card isi">
                            <div class="test-border">
                                <div class="title">ISI</div>
                                <div class="subtitle">{{ __('Insomnia') }}</div>
                                <span class="count">{{ number_format($stats['isi_count'] ?? 0) }}</span>
                                <div class="avg">{{ __('Avg Score') }}: {{ number_format($stats['isi_avg'] ?? 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="test-type-card pss">
                            <div class="test-border">
                                <div class="title">PSS</div>
                                <div class="subtitle">{{ __('Stress') }}</div>
                                <span class="count">{{ number_format($stats['pss_count'] ?? 0) }}</span>
                                <div class="avg">{{ __('Avg Score') }}: {{ number_format($stats['pss_avg'] ?? 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="test-type-card cis">
                            <div class="test-border">
                                <div class="title">CIS</div>
                                <div class="subtitle">{{ __('Functioning') }}</div>
                                <span class="count">{{ number_format($stats['cis_count'] ?? 0) }}</span>
                                <div class="avg">{{ __('Avg Score') }}: {{ number_format($stats['cis_avg'] ?? 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Filters Info -->
        <div class="report-info">
            <strong>{{ __('Report Info') }}:</strong>
            {{ $stats['generated_at'] ?? now()->format('Y-m-d H:i:s') }} |
            {{ __('By') }}: {{ $stats['generated_by'] ?? 'System' }}
            <br>
            <strong>{{ __('Filters') }}:</strong>
            @php
                $filterText = [];
                if (!empty($stats['date_from'])) {
                    $filterText[] = __('From') . ': ' . $stats['date_from'];
                }
                if (!empty($stats['date_to'])) {
                    $filterText[] = __('To') . ': ' . $stats['date_to'];
                }
                echo empty($filterText) ? __('All Tests') : implode(' | ', $filterText);
            @endphp
        </div>

        <!-- Tests Table -->
        <table class="tests-table" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Test Type') }}</th>
                    <th>{{ __('Score') }}</th>
                    <th>{{ __('Result Level') }}</th>
                    <th>{{ __('Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tests as $index => $test)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align: left;">
                            <strong>{{ $test->user_name ?? '-' }}</strong>
                            <div style="font-size: 6px; color: #9ca3af;">{{ $test->user_email ?? '' }}</div>
                        </td>
                        <td>
                            @php
                                $testType = $test->test_type;
                                $testClass = match ($testType) {
                                    'phq9' => 'test-phq9',
                                    'gad7' => 'test-gad7',
                                    'pcl5' => 'test-pcl5',
                                    'isi' => 'test-isi',
                                    'pss' => 'test-pss',
                                    'cis' => 'test-cis',
                                    default => ''
                                };
                                $testLabel = strtoupper($testType);
                            @endphp
                            <span class="test-badge {{ $testClass }}">{{ $testLabel }}</span>
                        </td>
                        <td><strong>{{ intval($test->score) }}</strong></td>
                        <td>
                            @php
                                $level = $test->result_level;
                                $levelClass = match ($level) {
                                    'minimal' => 'level-minimal',
                                    'mild' => 'level-mild',
                                    'moderate' => 'level-moderate',
                                    'severe' => 'level-severe',
                                    default => ''
                                };
                                $levelText = match ($level) {
                                    'minimal' => __('Minimal'),
                                    'mild' => __('Mild'),
                                    'moderate' => __('Moderate'),
                                    'severe' => __('Severe'),
                                    default => $level ?? '-'
                                };
                            @endphp
                            <span class="{{ $levelClass }}">{{ $levelText }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($test->test_date)->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">
                            {{ __('No test results found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <htmlpagefooter name="page-footer">
        <div class="footer">
            {{ __('Page') }} {PAGENO} / {nbpg} |
            &copy; {{ date('Y') }} {{ config('app.name', 'Tamman') }} |
            {{ __('Generated') }}: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </htmlpagefooter>

</body>

</html>