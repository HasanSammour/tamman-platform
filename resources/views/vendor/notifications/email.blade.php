<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

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
            background-color: #f6f8fc;
            padding: 40px 20px;
        }

        .container {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            padding: 32px 32px 0 32px;
            text-align: center;
        }

        .logo {
            max-width: 120px;
            margin: 0 auto;
        }

        .logo img {
            width: 100%;
            height: auto;
        }

        .content {
            padding: 24px 32px 32px 32px;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 12px;
            text-align: center;
        }

        .greeting {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 20px;
            text-align: center;
        }

        .message {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 28px;
            text-align: center;
        }

        .button {
            display: inline-block;
            background-color: #7c3aed;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            text-align: center;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #6d28d9;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .expiry-note {
            font-size: 12px;
            color: #a0aec0;
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .fallback-link {
            font-size: 12px;
            color: #718096;
            text-align: center;
            word-break: break-all;
            background: #f7fafc;
            padding: 12px;
            border-radius: 8px;
            margin-top: 16px;
        }

        .footer {
            background: #f7fafc;
            padding: 20px 32px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
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
            font-size: 11px;
            color: #a0aec0;
        }

        @media (max-width: 600px) {
            body {
                padding: 20px;
            }

            .header {
                padding: 24px 24px 0 24px;
            }

            .content {
                padding: 20px 24px 24px 24px;
            }

            h1 {
                font-size: 20px;
            }
        }

        /* RTL Support */
        body.rtl .button-container,
        body.rtl .message,
        body.rtl .greeting {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            <div class="logo">
                <img src="{{ config('app.url') }}/images/logo.png" alt="{{ config('app.name') }}" style="max-width: 120px; height: auto;">   
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h1>{{ __('Verify Your Email Address') }}</h1>

            @if (!empty($greeting))
                <div class="greeting">{{ $greeting }}</div>
            @else
                <div class="greeting">@lang('Hello!')</div>
            @endif

            @foreach ($introLines as $line)
                <div class="message">{{ $line }}</div>
            @endforeach

            @isset($actionText)
                <div class="button-container">
                    <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
                </div>
            @endisset

            @foreach ($outroLines as $line)
                <div class="message">{{ $line }}</div>
            @endforeach

            <div class="expiry-note">
                🔒 {{ __('This link will expire in 60 minutes.') }}
            </div>

            <div class="fallback-link">
                @lang("If you're having trouble clicking the button, copy and paste the URL below into your browser:")
                <br>
                <a href="{{ $actionUrl }}" style="color: #7c3aed; word-break: break-all;">{{ $actionUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
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
        </div>
    </div>
</body>

</html>