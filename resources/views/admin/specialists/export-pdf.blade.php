{{-- resources/views/admin/specialists/export-pdf.blade.php --}}
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

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #7c3aed;
        }

        .header h1 {
            color: #7c3aed;
            font-size: 22px;
            margin: 0;
        }

        .header p {
            color: #666;
            font-size: 10px;
            margin-top: 3px;
        }

        /* Stats Cards */
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
            color: #7c3aed;
            display: block;
        }

        .stat-card .label {
            font-size: 9px;
            color: #666;
            margin-top: 5px;
        }

        /* Filters Section */
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

        /* Table */
        .specialists-table {
            width: 100%;
            border-collapse: collapse;
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
            font-size: 9px;
            text-align: center;
        }

        .specialists-table th:first-child,
        .specialists-table td:first-child {
            text-align: left;
        }

        /* Badges */
        .badge-active {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 8px;
        }

        .badge-suspended {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 8px;
        }

        .badge-verified {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 8px;
        }

        .badge-unverified {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            font-size: 8px;
        }

        .rating-stars {
            color: #fbbf24;
            font-size: 8px;
            letter-spacing: 1px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #999;
        }

        /* RTL */
        body.rtl {
            direction: rtl;
        }

        body.rtl .specialists-table th {
            text-align: center;
        }

        body.rtl .specialists-table th:first-child,
        body.rtl .specialists-table td:first-child {
            text-align: right;
        }

        /* User name column */
        .user-name {
            font-weight: 600;
            color: #1f2937;
        }

        .user-id {
            font-size: 7px;
            color: #9ca3af;
        }

        .user-avatar-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .avatar-img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .avatar-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>{{ __('Specialists Report') }}</p>
        </div>
    </htmlpageheader>

    <!-- Main Content -->
    <div class="content">

        <!-- 4 Stats Cards in One Row -->
        <div class="stats-row">
            <div class="stat-card">
                <span class="number">{{ number_format($stats['total']) }}</span>
                <span class="label">{{ __('Total Specialists') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['active']) }}</span>
                <span class="label">{{ __('Active Specialists') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['suspended']) }}</span>
                <span class="label">{{ __('Suspended Specialists') }}</span>
            </div>
            <div class="stat-card">
                <span class="number">{{ number_format($stats['pending']) ?? 0 }}</span>
                <span class="label">{{ __('Pending Approval') }}</span>
            </div>
        </div>

        <!-- Filters Info -->
        <div class="filters-box">
            <strong>{{ __('Report Info') }}:</strong>
            {{ $stats['generated_at'] }} |
            {{ __('By') }}: {{ $stats['generated_by'] }}
            <br>
            <strong>{{ __('Filters') }}:</strong>
            @php
                $filterText = [];
                if (request('status') && request('status') !== 'all') {
                    $filterText[] = request('status') === 'active' ? 'Active Specialists' : 'Suspended Specialists';
                }
                if (request('search')) {
                    $filterText[] = 'Search: ' . request('search');
                }
                echo empty($filterText) ? 'All Specialists' : implode(' | ', $filterText);
            @endphp
        </div>

        <!-- Specialists Table -->
        <table class="specialists-table" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Specialist') }}</th>
                    <th>{{ __('Specialization') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Sessions') }}</th>
                    <th>{{ __('Fee') }}</th>
                    <th>{{ __('Rating') }}</th>
                    <th>{{ __('Verified') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Registered') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($specialists as $index => $specialist)
                    @php
                        $rating = $specialist->rating_avg ?? 0;
                        $fullStars = floor($rating);
                        $hasHalfStar = $rating - $fullStars >= 0.5;
                        $starsHtml = '';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $fullStars)
                                $starsHtml .= '★';
                            elseif ($i === $fullStars + 1 && $hasHalfStar)
                                $starsHtml .= '½';
                            else
                                $starsHtml .= '☆';
                        }
                        $profileImage = $specialist->profile_image_url;
                        $initial = mb_substr($specialist->name, 0, 1, 'UTF-8');
                    @endphp
                    <tr>
                        <td style="text-align: left;">#{{ $specialist->id }}</td>
                        <td style="text-align: left;">
                            <div class="user-avatar-cell">
                                <div class="avatar-img">
                                    @if($profileImage)
                                        <img src="{{ $profileImage }}" alt="{{ $specialist->name }}">
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                                <div>
                                    <div class="user-name">{{ $specialist->name }}</div>
                                    <div class="user-id">ID: {{ $specialist->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $specialist->specialization ?? '—' }}</td>
                        <td>{{ $specialist->email }}</td>
                        <td>{{ number_format($specialist->total_sessions ?? 0) }}</td>
                        <td>${{ number_format($specialist->consultation_fee ?? 0, 2) }}</td>
                        <td>
                            <div class="rating-stars">{{ $starsHtml }}</div>
                            <span style="font-size:7px;color:#666;">({{ number_format($rating, 1) }})</span>
                        </td>
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
                                <span class="badge-suspended">{{ __('Suspended') }}</span>
                            @endif
                        </td>
                        <td>{{ $specialist->created_at ? $specialist->created_at->format('Y-m-d') : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 30px;">
                            {{ __('No specialists found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <!-- Footer -->
    <htmlpagefooter name="page-footer">
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Tamman') }} |
            {{ __('Generated') }}: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </htmlpagefooter>

</body>

</html>