<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Donations Report') }} - {{ config('app.name') }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }

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
            border-bottom: 2px solid #ec4899;
        }

        .header h1 {
            color: #ec4899;
            font-size: 22px;
            margin: 0;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-table th {
            background: #ec4899;
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

        .text-success {
            color: #10b981;
        }

        .font-bold {
            font-weight: 700;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>{{ config('app.name', 'Tamman') }}</h1>
        <p>{{ __('Donations Report') }}</p>
    </div>

    <table class="payments-table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Donor') }}</th>
                <th>{{ __('Recipient') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $donation)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('Y-m-d') }}</td>
                    <td>{{ $donation->donor_name }}</td>
                    <td>{{ $donation->recipient_name ?? '—' }}</td>
                    <td class="font-bold">${{ number_format($donation->amount, 2) }}</td>
                    <td>{{ __(ucfirst($donation->status)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">{{ __('No donations found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{ __('Generated') }}: {{ now()->format('Y-m-d H:i:s') }}
    </div>

</body>

</html>