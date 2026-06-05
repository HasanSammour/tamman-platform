{{-- resources/views/admin/reports/exports/financial.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Financial Report') }} - {{ config('app.name') }}</title>
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

        /* Header */
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

        /* Report Info */
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

        /* Stats Cards - 4 in one row */
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

        /* Breakdown Cards - 3 in one row */
        .breakdown-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 12px;
            margin-bottom: 20px;
            width: 100%;
        }

        .breakdown-card {
            flex: 1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .breakdown-card.video {
            border-left: 4px solid #7c3aed;
        }

        .breakdown-card.audio {
            border-left: 4px solid #10b981;
        }

        .breakdown-card.text {
            border-left: 4px solid #f59e0b;
        }

        .breakdown-card .title {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .breakdown-card .count {
            font-size: 8px;
            color: #666;
        }

        .breakdown-card .revenue {
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 4px;
        }

        /* Fee Summary */
        .fee-summary {
            background: #f5f3ff;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c4b5fd;
        }

        .fee-summary .fee-label {
            font-size: 8px;
            color: #666;
        }

        .fee-summary .fee-value {
            font-size: 12px;
            font-weight: bold;
            color: #7c3aed;
        }

        /* Table */
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .transactions-table th {
            background: #7c3aed;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
        }

        .transactions-table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8px;
            text-align: center;
        }

        .transactions-table th:first-child,
        .transactions-table td:first-child {
            text-align: left;
        }

        /* Payment Badges */
        .payment-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 7px;
        }

        .payment-credit {
            background: #d1fae5;
            color: #065f46;
        }

        .payment-cash {
            background: #fef3c7;
            color: #d97706;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 7px;
            color: #999;
        }

        /* RTL Specific */
        body.rtl {
            direction: rtl;
        }

        body.rtl .breakdown-card {
            border-left: none;
            border-right: 4px solid;
        }

        body.rtl .breakdown-card.video {
            border-right-color: #7c3aed;
        }

        body.rtl .breakdown-card.audio {
            border-right-color: #10b981;
        }

        body.rtl .breakdown-card.text {
            border-right-color: #f59e0b;
        }

        body.rtl .transactions-table th:first-child,
        body.rtl .transactions-table td:first-child {
            text-align: right;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Financial Report') }}</p>
        </div>
    </htmlpageheader>

    <!-- Main Content -->
    <div class="content">

        <!-- 4 Stats Cards in One Row -->
        <div class="stats-row">
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</span>
                <span class="label">{{ __('Total Revenue') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_platform_fee'] ?? 0, 2) }}</span>
                <span class="label">{{ __('Platform Fee') }}</span>
                <span class="label">{{ $stats['platform_percent'] ?? 10 }}%</span>
            </div>
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_specialist_earnings'] ?? 0, 2) }}</span>
                <span class="label">{{ __('Specialist Earnings') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_sessions'] ?? 0) }}</span>
                <span class="label">{{ __('Total Sessions') }}</span>
            </div>
        </div>

        <!-- Revenue Breakdown by Session Type -->
        <div class="breakdown-row">
            <div class="breakdown-card video">
                <div class="title">{{ __('Video Sessions') }}</div>
                <div class="count">{{ number_format($stats['video_count'] ?? 0) }} {{ __('sessions') }}</div>
                <div class="revenue">${{ number_format($stats['video_revenue'] ?? 0, 2) }}</div>
            </div>
            <div class="breakdown-card audio">
                <div class="title">{{ __('Audio Sessions') }}</div>
                <div class="count">{{ number_format($stats['audio_count'] ?? 0) }} {{ __('sessions') }}</div>
                <div class="revenue">${{ number_format($stats['audio_revenue'] ?? 0, 2) }}</div>
            </div>
            <div class="breakdown-card text">
                <div class="title">{{ __('Text Sessions') }}</div>
                <div class="count">{{ number_format($stats['text_count'] ?? 0) }} {{ __('sessions') }}</div>
                <div class="revenue">${{ number_format($stats['text_revenue'] ?? 0, 2) }}</div>
            </div>
        </div>

        <!-- Platform Fee Summary -->
        <div class="fee-summary">
            <span class="fee-label">{{ __('Platform Fee Percentage') }}:</span>
            <span class="fee-value">{{ $stats['platform_percent'] ?? 10 }}%</span>
            <span class="fee-label" style="margin-left: 15px;">{{ __('Platform Fee Applied') }}:</span>
            <span class="fee-value">${{ number_format($stats['total_platform_fee'] ?? 0, 2) }}</span>
            <span class="fee-label" style="margin-left: 15px;">{{ __('Total Paid to Specialists') }}:</span>
            <span class="fee-value">${{ number_format($stats['total_specialist_earnings'] ?? 0, 2) }}</span>
        </div>

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
                echo empty($filterText) ? __('All Transactions') : implode(' | ', $filterText);
            @endphp
        </div>

        <!-- Transactions Table -->
        <table class="transactions-table" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Patient') }}</th>
                    <th>{{ __('Specialist') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Payment') }}</th>
                    <th>{{ __('Platform Fee') }}</th>
                    <th>{{ __('Specialist Earning') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $index => $session)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($session->session_datetime)->format('Y-m-d') }}<br>
                        <small style="color:#9ca3af;">{{ \Carbon\Carbon::parse($session->session_datetime)->format('H:i') }}</small>
                    </td>
                    <td style="text-align: left;">
                        <strong>{{ $session->patient_name ?? __('Deleted User') }}</strong>
                        <div style="font-size: 6px; color: #9ca3af;">{{ $session->patient_email ?? '' }}</div>
                    </td>
                    <td style="text-align: left;">
                        <strong>{{ $session->specialist_name ?? __('Deleted User') }}</strong>
                        <div style="font-size: 6px; color: #9ca3af;">{{ $session->specialist_email ?? '' }}</div>
                    </td>
                    <td>
                        @if($session->session_type === 'video')
                            <span style="color:#7c3aed;">{{ __('Video') }}</span>
                        @elseif($session->session_type === 'audio')
                            <span style="color:#10b981;">{{ __('Audio') }}</span>
                        @else
                            <span style="color:#f59e0b;">{{ __('Text') }}</span>
                        @endif
                    </td>
                    <td><strong>${{ number_format($session->amount ?? 0, 2) }}</strong></td>
                    <td>
                        <span class="payment-badge {{ $session->is_paid_by_credit ? 'payment-credit' : 'payment-cash' }}">
                            {{ $session->is_paid_by_credit ? __('Credit') : __('Cash') }}
                        </span>
                    </td>
                    <td>${{ number_format($session->platform_fee ?? 0, 2) }}</td>
                    <td><strong>${{ number_format($session->specialist_earning ?? 0, 2) }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 30px;">
                        {{ __('No transactions found') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <!-- Footer -->
    <htmlpagefooter name="page-footer">
        <div class="footer">
            {{ __('Page') }} {PAGENO} / {nbpg} |
            &copy; {{ date('Y') }} {{ config('app.name', 'Tamman') }} |
            {{ __('Generated') }}: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </htmlpagefooter>

</body>
</html>