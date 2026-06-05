{{-- resources/views/admin/reports/exports/points.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Points Report') }} - {{ config('app.name') }}</title>
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
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
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

        .points-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .points-table th {
            background: #7c3aed;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
        }

        .points-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8px;
            text-align: center;
        }

        .points-table th:first-child,
        .points-table td:first-child {
            text-align: left;
        }

        .points-positive {
            color: #10b981;
            font-weight: bold;
        }

        .points-negative {
            color: #ef4444;
            font-weight: bold;
        }

        .badge-earned {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .badge-redeemed {
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

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .small-text {
            font-size: 6px;
            color: #9ca3af;
        }

        body.rtl {
            direction: rtl;
        }

        body.rtl .points-table th:first-child,
        body.rtl .points-table td:first-child {
            text-align: right;
        }

        body.rtl .text-left {
            text-align: right;
        }

        body.rtl .text-right {
            text-align: left;
        }
    </style>
</head>

<body>

    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Points Report') }}</p>
        </div>
    </htmlpageheader>

    <div class="content">

        <div class="stats-row">
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_earned'] ?? 0) }}</span>
                <span class="label">{{ __('Total Earned') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_redeemed'] ?? 0) }}</span>
                <span class="label">{{ __('Total Redeemed') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['net_points'] ?? 0) }}</span>
                <span class="label">{{ __('Net Points') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_transactions'] ?? 0) }}</span>
                <span class="label">{{ __('Transactions') }}</span>
            </div>
        </div>

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

        <table class="points-table" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Points') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Source') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $transaction)
                    <tr>
                        <td class="text-left">{{ $loop->iteration }}</td>
                        <td class="text-left">
                            <strong>{{ $transaction->user_name ?? '-' }}</strong>
                            <br>
                            <span class="small-text">{{ $transaction->user_email ?? '' }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $points = intval($transaction->points);
                                $pointsClass = $points > 0 ? 'points-positive' : 'points-negative';
                                $pointsSign = $points > 0 ? '+' : '';
                            @endphp
                            <span class="{{ $pointsClass }}">{{ $pointsSign }}{{ number_format($points) }}</span>
                        </td>
                        <td class="text-center">
                            @if($transaction->type === 'earned')
                                <span class="badge-earned">{{ __('Earned') }}</span>
                            @else
                                <span class="badge-redeemed">{{ __('Redeemed') }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ ucfirst(str_replace('_', ' ', $transaction->source ?? '-')) }}</td>
                        <td class="text-left">{{ $transaction->description ?? '-' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">
                            {{ __('No transactions found') }}
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