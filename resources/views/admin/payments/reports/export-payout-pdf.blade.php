<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Payout Report') }} - {{ config('app.name') }}</title>
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
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f59e0b;
        }

        .header h1 {
            color: #f59e0b;
            font-size: 22px;
            margin: 0;
        }

        .header p {
            color: #666;
            font-size: 10px;
            margin-top: 3px;
        }

        .stats-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 15px;
            margin-bottom: 25px;
            width: 100%;
        }

        .stat-card {
            flex: 1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .stat-card .number {
            font-size: 22px;
            font-weight: bold;
            color: #f59e0b;
            display: block;
        }

        .stat-card .label {
            font-size: 9px;
            color: #666;
            margin-top: 5px;
        }

        .filters-box {
            background: #f5f5f5;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 20px;
            font-size: 9px;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-table th {
            background: #f59e0b;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
        }

        .payments-table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
            text-align: center;
        }

        .payments-table th:first-child,
        .payments-table td:first-child {
            text-align: left;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #999;
        }

        .summary-row {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
            text-align: right;
            font-size: 10px;
        }

        .text-success {
            color: #10b981;
        }

        .font-bold {
            font-weight: 700;
        }
    </style>
</head>

<body>

    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Specialist Payout Report') }} - {{ $stats['month_name'] }}</p>
        </div>
    </htmlpageheader>

    <div class="content">
        <div class="stats-row">
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_earnings'], 2) }}</span>
                <span class="label">{{ __('Total Earnings') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_platform_fees'], 2) }}</span>
                <span class="label">{{ __('Platform Fee') }} ({{ $stats['platform_percent'] }}%)</span>
            </div>
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_final_amount'], 2) }}</span>
                <span class="label">{{ __('Final Amount') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_specialists']) }}</span>
                <span class="label">{{ __('Specialists') }}</span>
            </div>
        </div>

        <div class="filters-box">
            <strong>{{ __('Report Info') }}:</strong>
            {{ $stats['generated_at'] }} |
            {{ __('By') }}: {{ $stats['generated_by'] }} |
            {{ __('Month') }}: {{ $stats['month_name'] }}
        </div>

        <table class="payments-table" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Specialist') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Video') }}</th>
                    <th>{{ __('Audio') }}</th>
                    <th>{{ __('Text') }}</th>
                    <th>{{ __('Total Sessions') }}</th>
                    <th>{{ __('Fee/Session') }}</th>
                    <th>{{ __('Earnings') }}</th>
                    <th>{{ __('Platform Fee') }}</th>
                    <th>{{ __('Final Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $index => $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['specialist_name'] }}</td>
                        <td>{{ $item['specialist_email'] }}</td>
                        <td>{{ number_format($item['video_sessions']) }}</td>
                        <td>{{ number_format($item['audio_sessions']) }}</td>
                        <td>{{ number_format($item['text_sessions']) }}</td>
                        <td class="font-bold">{{ number_format($item['total_sessions']) }}</td>
                        <td>${{ number_format($item['consultation_fee'], 2) }}</td>
                        <td>${{ number_format($item['earnings'], 2) }}</td>
                        <td>${{ number_format($item['platform_fee'], 2) }}</td>
                        <td class="font-bold text-success">${{ number_format($item['final_amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 30px;">
                            {{ __('No data found for this month') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary-row">
            <strong>{{ __('Total Summary') }}:</strong>
            {{ __('Total Earnings') }}: ${{ number_format($stats['total_earnings'], 2) }} |
            {{ __('Total Platform Fees') }}: ${{ number_format($stats['total_platform_fees'], 2) }} |
            {{ __('Total Final Amount') }}: ${{ number_format($stats['total_final_amount'], 2) }}
        </div>

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