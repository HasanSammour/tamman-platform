{{-- resources/views/admin/reports/exports/users.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Users Report') }} - {{ config('app.name') }}</title>
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

        /* Filters Section */
        .filters-box {
            background: #f5f5f5;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 8px;
        }

        .filters-box strong {
            color: #333;
        }

        /* Table */
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .users-table th {
            background: #7c3aed;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
        }

        .users-table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8px;
            text-align: center;
        }

        .users-table th:first-child,
        .users-table td:first-child {
            text-align: left;
        }

        /* Badges */
        .badge-active {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .badge-donor {
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

        body.rtl .users-table th:first-child,
        body.rtl .users-table td:first-child {
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Users Report') }}</p>
        </div>
    </htmlpageheader>

    <!-- Main Content -->
    <div class="content">

        <!-- 4 Stats Cards in One Row -->
        <div class="stats-row">
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total'] ?? 0) }}</span>
                <span class="label">{{ __('Total Users') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['active'] ?? 0) }}</span>
                <span class="label">{{ __('Active Users') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['inactive'] ?? 0) }}</span>
                <span class="label">{{ __('Inactive / Suspended') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['donors'] ?? 0) }}</span>
                <span class="label">{{ __('Donors') }}</span>
            </div>
        </div>

        <!-- Filters Info -->
        <div class="filters-box">
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
                echo empty($filterText) ? __('All Users') : implode(' | ', $filterText);
            @endphp
        </div>

        <!-- Users Table -->
        <table class="users-table" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Gender') }}</th>
                    <th>{{ __('Registered') }}</th>
                    <th>{{ __('Sessions') }}</th>
                    <th>{{ __('Points') }}</th>
                    <th>{{ __('Credit') }}</th>
                    <th>{{ __('Donor') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align: left;">
                            <strong>{{ $user->name }}</strong>
                            <div style="font-size: 6px; color: #9ca3af;">ID: {{ $user->id }}</div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '—' }}</td>
                        <td>
                            @if($user->gender === 'male')
                                {{ __('Male') }}
                            @elseif($user->gender === 'female')
                                {{ __('Female') }}
                            @elseif($user->gender === 'other')
                                {{ __('Other') }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : '—' }}</td>
                        <td>{{ number_format($user->total_sessions ?? 0) }}</td>
                        <td>{{ number_format($user->total_points ?? 0) }}</td>
                        <td>${{ number_format($user->credit_balance ?? 0, 2) }}</td>
                        <td>
                            @if($user->is_donor)
                                <span class="badge-donor">{{ __('Donor') }}</span>
                            @else
                                {{ __('No') }}
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge-active">{{ __('Active') }}</span>
                            @else
                                <span class="badge-inactive">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 30px;">
                            {{ __('No users found') }}
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