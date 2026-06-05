{{-- resources/views/admin/reports/exports/specialists.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Specialists Report') }} - {{ config('app.name') }}</title>
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

        .specialists-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .specialists-table th {
            background: #7c3aed;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
        }

        .specialists-table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8px;
            text-align: center;
        }

        .specialists-table th:first-child,
        .specialists-table td:first-child {
            text-align: left;
        }

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

        .badge-verified {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .badge-unverified {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 7px;
        }

        .rating-stars {
            color: #fbbf24;
            font-size: 7px;
            letter-spacing: 1px;
            white-space: nowrap;
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

        body.rtl .specialists-table th:first-child,
        body.rtl .specialists-table td:first-child {
            text-align: right;
        }
    </style>
</head>

<body>

    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Specialists Report') }}</p>
        </div>
    </htmlpageheader>

    <div class="content">

        <div class="stats-row">
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total_specialists'] ?? 0) }}</span>
                <span class="label">{{ __('Total Specialists') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['verified_specialists'] ?? 0) }}</span>
                <span class="label">{{ __('Verified') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['avg_rating'] ?? 0, 1) }}</span>
                <span class="label">{{ __('Average Rating') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">${{ number_format($stats['total_earnings'] ?? 0, 2) }}</span>
                <span class="label">{{ __('Total Earnings') }}</span>
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
                echo empty($filterText) ? __('All Specialists') : implode(' | ', $filterText);
            @endphp
        </div>

        <table class="specialists-table" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Specialist') }}</th>
                    <th>{{ __('Specialization') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Sessions') }}</th>
                    <th>{{ __('Earnings') }}</th>
                    <th>{{ __('Rating') }}</th>
                    <th>{{ __('Fee') }}</th>
                    <th>{{ __('Verified') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($specialists as $index => $specialist)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align: left;">
                            <strong>{{ $specialist->name }}</strong>
                            <br><span style="font-size: 6px; color: #9ca3af;">ID: {{ $specialist->id }}</span>
                        </td>
                        <td>{{ $specialist->specialization ?? '-' }}</td>
                        <td>{{ $specialist->email }}</td>
                        <td>{{ $specialist->phone ?? '—' }}</td>
                        <td>{{ number_format($specialist->total_sessions ?? 0) }}</td>
                        <td><strong>${{ number_format($specialist->total_earnings ?? 0, 2) }}</strong></td>
                        <td>
                            @php
                                $rating = floatval($specialist->rating_avg ?? 0);
                                $fullStars = floor($rating);
                                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                $starsHtml = '';
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $fullStars) {
                                        $starsHtml .= '★';
                                    } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                                        $starsHtml .= '½';
                                    } else {
                                        $starsHtml .= '☆';
                                    }
                                }
                            @endphp
                            <div class="rating-stars">{{ $starsHtml }}</div>
                            <span style="font-size: 7px;">({{ number_format($rating, 1) }})</span>
                        </td>
                        <td>${{ number_format($specialist->consultation_fee ?? 0, 2) }}</td>
                        <td>
                            @if($specialist->is_verified)
                                <span class="badge-verified">{{ __('Verified') }}</span>
                            @else
                                <span class="badge-unverified">{{ __('Pending') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($specialist->is_active)
                                <span class="badge-active">{{ __('Active') }}</span>
                            @else
                                <span class="badge-inactive">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 30px;">
                            {{ __('No specialists found') }}
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