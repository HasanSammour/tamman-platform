<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Payouts History Report') }} - {{ config('app.name') }}</title>
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
            border-bottom: 2px solid #8b5cf6;
        }

        .header h1 {
            color: #8b5cf6;
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
            color: #8b5cf6;
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

        .filters-box strong {
            color: #333;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-table th {
            background: #8b5cf6;
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

        .badge-paid {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 8px;
        }

        .badge-pending {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 8px;
        }

        .badge-failed {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 8px;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #999;
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
            <p>{{ __('Payouts History Report') }}</p>
        </div>
    </htmlpageheader>

    <div class="content">
        <div class="stats-row">
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_amount'], 2) }}</span>
                <span class="label">{{ __('Total Amount Paid') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_payouts']) }}</span>
                <span class="label">{{ __('Total Payouts') }}</span>
            </div>
        </div>

        <div class="filters-box">
            <strong>{{ __('Report Info') }}:</strong>
            {{ $stats['generated_at'] }} |
            {{ __('By') }}: {{ $stats['generated_by'] }}
            @if(request('status') && request('status') !== 'all')
                <br>
                <strong>{{ __('Status Filter') }}:</strong> {{ __(ucfirst(request('status'))) }}
            @endif
        </div>

        <table class="payments-table" cellspacing="0">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Specialist') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Month') }}</th>
                    <th>{{ __('Total Earnings') }}</th>
                    <th>{{ __('Platform Fee') }}</th>
                    <th>{{ __('Final Amount') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                    <tr>
                        <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($payout->created_at)->format('Y-m-d') }}
                        </td>
                        <td style="text-align: left;">{{ $payout->specialist_name }}</td>
                        <td>{{ $payout->specialist_email }}</td>
                        <td>{{ $payout->month_year }}</td>
                        <td>${{ number_format($payout->amount, 2) }}</td>
                        <td>${{ number_format($payout->platform_fee, 2) }}</td>
                        <td class="font-bold text-success">${{ number_format($payout->final_amount, 2) }}</td>
                        <td>
                            <span class="badge-{{ $payout->status }}">
                                {{ $payout->status === 'paid' ? __('Paid') : ($payout->status === 'pending' ? __('Pending') : __('Failed')) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px;">
                            {{ __('No payouts found') }}
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