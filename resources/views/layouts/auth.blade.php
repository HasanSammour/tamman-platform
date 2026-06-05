{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Tamman'))</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    
    @stack('styles')
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-container">
            <!-- Logo -->
            <div class="auth-logo">
                <a href="{{ route('home') }}" 
                    data-bs-toggle="popover" data-bs-trigger="hover"
                    data-bs-content="{{ __('Go back to home page') }}" data-bs-placement="bottom"
                    data-bs-delay='{"show":300,"hide":100}'>
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Tamman') }}" height="50">
                    <span>{{ __('Tamman') }}</span>
                </a>
            </div>
            
            <!-- Auth Card -->
            <div class="auth-card">
                @yield('auth-content')
            </div>
            
            <!-- Footer Links -->
            <div class="auth-footer-links">
                <a href="{{ route('home') }}" 
                    data-bs-toggle="popover" data-bs-trigger="hover"
                    data-bs-content="{{ __('Return to homepage') }}" data-bs-placement="top"
                    data-bs-delay='{"show":300,"hide":100}'>
                    {{ __('Back to Home') }}
                </a>
                <span class="separator">|</span>
                <a href="{{ route('terms') }}"
                    data-bs-toggle="popover" data-bs-trigger="hover"
                    data-bs-content="{{ __('Read our privacy policy') }}" data-bs-placement="top"
                    data-bs-delay='{"show":300,"hide":100}'>
                    {{ __('Privacy Policy') }}
                </a>
                <span class="separator">|</span>
                <a href="{{ route('privacy') }}"
                    data-bs-toggle="popover" data-bs-trigger="hover"
                    data-bs-content="{{ __('Read our terms of service') }}" data-bs-placement="top"
                    data-bs-delay='{"show":300,"hide":100}'>
                    {{ __('Terms of Service') }}
                </a>
            </div>
        </div>
    </div>
    
    <!-- Vendor JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/fontawesome/js/all.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Bootstrap Popover Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all Bootstrap Popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    trigger: 'hover',
                    delay: { show: 300, hide: 100 }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>