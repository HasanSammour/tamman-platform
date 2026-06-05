<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Credit Requests Report') }} - {{ config('app.name') }}</title>
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
            border-bottom: 2px solid #10b981;
        }

        .header h1 {
            color: #10b981;
            font-size: 22px;
            margin: 0;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-table th {
            background: #10b981;
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

        .badge-pending {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 8px;
        }

        .badge-allocated {
            background: #d1fae5;
            color: #065f46;
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
    </style>
</head>

<body>

    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Credit Requests Report') }}</p>
        </div>
    </htmlpageheader>

    <div class="content">
        <table class="payments-table" cellspacing="0">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Description') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($request->created_at)->format('Y-m-d') }}
                        </td>
                        <td>{{ $request->user_name }}<br><small>{{ $request->user_email }}</small></td>
                        <td class="font-bold">${{ number_format($request->amount, 2) }}</td>
                        <td>
                            <span class="{{ $request->status === 'pending' ? 'badge-pending' : 'badge-allocated' }}">
                                {{ $request->status === 'pending' ? __('Pending') : __('Allocated') }}
                            </span>
                        </td>
                        <td>{{ $request->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">{{ __('No credit requests found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <htmlpagefooter name="page-footer">
        <div class="footer">
            {{ __('Page') }} {PAGENO} / {nbpg} | &copy; {{ date('Y') }} {{ config('app.name', 'Tamman') }}
        </div>
    </htmlpagefooter>

</body>

</html>