{{-- resources/views/admin/reports/exports/sessions.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Sessions Report') }} - {{ config('app.name') }}</title>
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

        /* Table */
        .sessions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .sessions-table th {
            background: #7c3aed;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
        }

        .sessions-table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8px;
            text-align: center;
        }

        .sessions-table th:first-child,
        .sessions-table td:first-child {
            text-align: left;
        }

        /* Badges */
        .badge-completed {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .badge-scheduled {
            background: #ede9fe;
            color: #7c3aed;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .badge-no_show {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .payment-credit {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .payment-cash {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
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

        body.rtl .sessions-table th:first-child,
        body.rtl .sessions-table td:first-child {
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Sessions Report') }}</p>
        </div>
    </htmlpageheader>

    <!-- Main Content -->
    <div class="content">

        <!-- 4 Stats Cards in One Row -->
        <div class="stats-row">
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_sessions'] ?? 0) }}</span>
                <span class="label">{{ __('Total Sessions') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['completed_sessions'] ?? 0) }}</span>
                <span class="label">{{ __('Completed') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['cancelled_sessions'] ?? 0) }}</span>
                <span class="label">{{ __('Cancelled') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</span>
                <span class="label">{{ __('Total Revenue') }}</span>
            </div>
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
                if (!empty($stats['session_type']) && $stats['session_type'] !== 'all') {
                    $filterText[] = __('Type') . ': ' . __(ucfirst($stats['session_type']));
                }
                if (!empty($stats['status']) && $stats['status'] !== 'all') {
                    $filterText[] = __('Status') . ': ' . __(ucfirst($stats['status']));
                }
                echo empty($filterText) ? __('All Sessions') : implode(' | ', $filterText);
            @endphp
        </div>

        <!-- Sessions Table -->
        <table class="sessions-table" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Date & Time') }}</th>
                    <th>{{ __('Patient') }}</th>
                    <th>{{ __('Specialist') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Duration') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Payment') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $index => $session)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($session->session_datetime)->format('Y-m-d') }}<br>
                            <small
                                style="color:#9ca3af;">{{ \Carbon\Carbon::parse($session->session_datetime)->format('H:i') }}</small>
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
                        <td>{{ $session->duration_minutes ?? 60 }} {{ __('min') }}</td>
                        <td><strong>${{ number_format($session->amount ?? 0, 2) }}</strong></td>
                        <td>
                            @if($session->is_paid_by_credit)
                                <span class="payment-credit">{{ __('Credit') }}</span>
                            @else
                                <span class="payment-cash">{{ __('Cash') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($session->status === 'completed')
                                <span class="badge-completed">{{ __('Completed') }}</span>
                            @elseif($session->status === 'scheduled')
                                <span class="badge-scheduled">{{ __('Scheduled') }}</span>
                            @elseif($session->status === 'cancelled')
                                <span class="badge-cancelled">{{ __('Cancelled') }}</span>
                            @else
                                <span class="badge-no_show">{{ __('No Show') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 30px;">
                            {{ __('No sessions found') }}
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