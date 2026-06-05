<!DOCTYPE html>
<html lang="ar" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Tamman') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            background-color: #f5f3ff;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            padding: 32px 24px;
            text-align: center;
        }

        .logo {
            max-width: 80px;
            margin: 0 auto;
        }

        .logo img {
            width: 100%;
            height: auto;
        }

        .logo-text {
            color: white;
            font-size: 22px;
            font-weight: 700;
            margin-top: 12px;
            letter-spacing: -0.5px;
        }

        .logo-sub {
            color: rgba(255, 255, 255, 0.85);
            font-size: 12px;
            margin-top: 4px;
        }

        .content {
            padding: 32px 32px 24px 32px;
            background: white;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 24px 0;
        }

        .info-box {
            background: #f9fafb;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #7c3aed;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 10px 0;
        }

        .info-label {
            color: #6b7280;
            width: 40%;
            font-size: 14px;
        }

        .info-value {
            color: #1f2937;
            font-weight: 500;
            font-size: 14px;
        }

        .meeting-box {
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .meeting-link {
            background: white;
            border-radius: 12px;
            padding: 12px;
            margin: 15px 0;
            word-break: break-all;
        }

        .meeting-link a {
            color: #7c3aed;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }

        .btn-secondary {
            display: inline-block;
            background: #f3f4f6;
            color: #374151 !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 14px;
            margin-left: 12px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .btn-group {
            text-align: center;
            margin: 24px 0;
        }

        .tips-box {
            background: #fef3c7;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
        }

        .tips-title {
            color: #d97706;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .tips-list {
            margin: 0;
            padding-left: 20px;
            color: #92400e;
            font-size: 13px;
        }

        .tips-list li {
            margin-bottom: 6px;
        }

        .refund-box {
            background: #d1fae5;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
            text-align: center;
        }

        .refund-title {
            color: #065f46;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .footer {
            background: #f9fafb;
            padding: 20px 32px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer-links {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #7c3aed;
            text-decoration: none;
            font-size: 12px;
            margin: 0 8px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .copyright {
            color: #9ca3af;
            font-size: 11px;
            margin-top: 12px;
        }

        .tagline {
            color: #6b7280;
            font-size: 12px;
            margin-top: 8px;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 24px 16px;
            }

            .content {
                padding: 24px 20px;
            }

            .footer {
                padding: 16px 20px;
            }

            .info-table td {
                display: block;
                padding: 4px 0;
            }

            .info-label {
                width: 100%;
            }

            .btn-primary,
            .btn-secondary {
                display: block;
                margin: 10px 0;
            }

            .btn-secondary {
                margin-left: 0;
            }

            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ url('/images/logo.png') }}" alt="{{ config('app.name', 'Tamman') }}">
            </div>
            <div class="logo-text">{{ config('app.name', 'Tamman') }}</div>
            <div class="logo-sub">{{ __('منصة الدعم الرقمي للصحة النفسية') }}</div>
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            <div class="footer-links">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>
                <span>•</span>
                <a href="{{ route('terms') }}">{{ __('Terms') }}</a>
                <span>•</span>
                <a href="{{ route('privacy') }}">{{ __('Privacy') }}</a>
                <span>•</span>
                <a href="{{ route('contact') }}">{{ __('Contact') }}</a>
            </div>
            <div class="copyright">
                &copy; {{ date('Y') }} {{ config('app.name', 'Tamman') }}. {{ __('All rights reserved.') }}
            </div>
            <div class="tagline">
                {{ __('Together for better mental health') }}
            </div>
        </div>
    </div>
</body>

</html>