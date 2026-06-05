{{-- resources/views/specialist/earnings/invoice.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice - {{ $month_name }} - {{ config('app.name') }}</title>
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
            line-height: 1.5;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #7c3aed;
        }

        .header h1 {
            color: #7c3aed;
            font-size: 24px;
            margin: 0 0 5px;
        }

        .header p {
            color: #666;
            font-size: 10px;
        }

        /* Invoice Title */
        .invoice-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-title h2 {
            color: #1f2937;
            font-size: 18px;
            margin: 0;
        }

        .invoice-title .month {
            color: #7c3aed;
            margin-top: 5px;
            font-size: 14px;
        }

        /* Info Boxes */
        .info-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 20px;
            margin-bottom: 25px;
            width: 100%;
        }

        .info-box {
            flex: 1;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e5e7eb;
        }

        .info-box .label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 5px;
            display: block;
        }

        .info-box .value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }

        .info-box .value.small {
            font-size: 11px;
        }

        /* Payment Summary Box */
        .payment-summary {
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            text-align: center;
        }

        .payment-summary .total-label {
            font-size: 10px;
            color: #6d28d9;
            display: block;
            margin-bottom: 5px;
        }

        .payment-summary .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #7c3aed;
        }

        /* Table */
        .sessions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
            font-size: 9px;
            text-align: center;
        }

        /* Summary Table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .summary-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-table td:first-child {
            font-weight: 600;
            color: #374151;
        }

        .summary-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .total-row {
            background: #f5f3ff;
            font-weight: bold;
        }

        .total-row td {
            border-bottom: none;
            padding: 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #999;
        }

        .notes {
            margin-top: 20px;
            padding: 12px;
            background: #fef3c7;
            border-radius: 8px;
            font-size: 8px;
            color: #d97706;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <htmlpageheader name="page-header">
        <div class="header">
            <h1>{{ config('app.name', 'Tamman') }}</h1>
            <p>Digital Mental Health Support Platform</p>
        </div>
    </htmlpageheader>

    <!-- Main Content -->
    <div class="content">

        <!-- Invoice Title -->
        <div class="invoice-title">
            <h2>Payment Invoice</h2>
            <div class="month">{{ $month_name }}</div>
        </div>

        <!-- Info Row -->
        <div class="info-row">
            <div class="info-box">
                <span class="label">Specialist Name</span>
                <span class="value">{{ $specialist->name }}</span>
            </div>
            <div class="info-box">
                <span class="label">Specialist ID</span>
                <span class="value">#{{ $specialist->id }}</span>
            </div>
            <div class="info-box">
                <span class="label">Payment Date</span>
                <span class="value small">{{ $generated_at->format('F d, Y') }}</span>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="payment-summary">
            <span class="total-label">Total Payment Amount</span>
            <div class="total-amount">${{ number_format($payment->final_amount, 2) }}</div>
            <span style="font-size: 9px; color: #6d28d9;">Payment for {{ $month_name }}</span>
        </div>

        <!-- Sessions Table -->
        <h3 style="font-size: 11px; margin-bottom: 8px; color: #1f2937;">
            Session Details
        </h3>
        <table class="sessions-table" cellspacing="0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Session Type</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                    <tr>
                        <td>{{ $session['date'] }}</td>
                        <td>{{ $session['time'] }}</td>
                        <td>{{ $session['patient_name'] }}</td>
                        <td>
                            @if($session['session_type'] == 'video')
                                Video Session
                            @elseif($session['session_type'] == 'audio')
                                Audio Session
                            @else
                                Text Session
                            @endif
                        </td>
                        <td>${{ number_format($session['earning'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">
                            No sessions found for this period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary Table -->
        <h3 style="font-size: 11px; margin-bottom: 8px; color: #1f2937;">
            Payment Summary
        </h3>
        <table class="summary-table" cellspacing="0">
            <tr>
                <td width="70%">Total Sessions</td>
                <td width="30%">{{ count($sessions) }} sessions</td>
            </tr>
            <tr>
                <td>Total Earnings</td>
                <td>${{ number_format($total_earnings, 2) }}</td>
            </tr>
            <tr>
                <td>Platform Fee
                    ({{ $payment->platform_fee > 0 ? number_format(($payment->platform_fee / $payment->amount) * 100, 1) : '0' }}%)
                </td>
                <td>${{ number_format($payment->platform_fee, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Net Payment</strong></td>
                <td><strong>${{ number_format($payment->final_amount, 2) }}</strong></td>
            </tr>
        </table>

        <!-- Payment Status -->
        <div class="notes">
            <strong>Payment Status:</strong>
            @if($payment->status == 'paid')
                <span style="color: #10b981;">✓ Paid</span>
                <br><strong>Payment Date:</strong> {{ $payment->paid_at ? $payment->paid_at->format('F d, Y') : '-' }}
            @elseif($payment->status == 'pending')
                <span style="color: #f59e0b;">⏳ Pending</span>
            @else
                <span style="color: #ef4444;">✗ Failed</span>
            @endif
        </div>

        <!-- Notes -->
        <div class="notes">
            <strong>Notes:</strong><br>
            • This invoice is generated automatically by Tamman Platform.<br>
            • For any questions regarding this payment, please contact support@tamman.ps<br>
            • Session fees are calculated as: Video (100%), Audio (90%), Text (80%) of consultation fee.
        </div>

    </div>

    <!-- Footer -->
    <htmlpagefooter name="page-footer">
        <div class="footer">
            Invoice generated on {{ $generated_at->format('F d, Y \a\t h:i A') }} |
            &copy; {{ date('Y') }} {{ config('app.name', 'Tamman') }} |
            All rights reserved
        </div>
    </htmlpagefooter>

</body>

</html>