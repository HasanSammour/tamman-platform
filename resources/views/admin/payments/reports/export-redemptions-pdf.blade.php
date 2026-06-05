<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Redemptions Report') }} - {{ config('app.name') }}</title>
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #06b6d4;
        }
        .header h1 {
            color: #06b6d4;
            font-size: 22px;
            margin: 0;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payments-table th {
            background: #06b6d4;
            color: white;
            padding: 8px 6px;
            font-size: 9px;
        }
        .payments-table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('app.name', 'Tamman') }}</h1>
        <p>{{ __('Points Redemptions Report') }}</p>
    </div>

    <table class="payments-table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Reward') }}</th>
                <th>{{ __('Points Spent') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($redemptions as $redemption)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($redemption->created_at)->format('Y-m-d') }}</td>
                    <td>{{ $redemption->user_name }}</td>
                    <td>{{ $redemption->reward_name }}</td>
                    <td>{{ number_format($redemption->points_spent) }} {{ __('points') }}</td>
                    <td>{{ $redemption->status_text }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;">{{ __('No redemptions found') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{ __('Generated') }}: {{ now()->format('Y-m-d H:i:s') }}
    </div>

</body>
</html>