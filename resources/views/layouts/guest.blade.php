{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Tamman') . ' - Digital Mental Health Platform')</title>
    <meta name="description"
        content="@yield('description', 'Tamman is a secure digital mental health platform providing psychological support services online. Get help from licensed specialists in a private, stigma-free environment.')">
    <meta name="keywords"
        content="mental health, psychology, therapy, counseling, Gaza, psychological support, online therapy">
    <meta name="author" content="Tamman Team">

    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="@yield('og-title', config('app.name', 'Tamman') . ' - Mental Health Platform')">
    <meta property="og:description"
        content="@yield('og-description', 'Access professional mental health support online. Private, secure, and stigma-free.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Preconnect for external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,100..900&family=Cairo:wght@200..1000&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/fonts/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/apexcharts/apexcharts.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* Verification warning styles */
        .verification-warning {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #fef3c7;
            border-radius: 40px;
            font-size: 0.75rem;
            color: #d97706;
            margin-right: 10px;
        }

        .verification-warning i {
            font-size: 0.7rem;
        }

        .verification-warning .verification-text {
            font-weight: 500;
        }

        .inline-logout {
            display: inline-block;
        }

        body.rtl .verification-warning {
            margin-right: 0;
            margin-left: 10px;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="skip-to-main"
        style="position: absolute; top: -40px; left: 0; background: #8b5cf6; color: white; padding: 8px 16px; z-index: 9999; text-decoration: none;">
        {{ __('Skip to main content') }}
    </a>

    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="navbar-container container">
            <!-- Logo / Brand -->
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Tamman') }}" class="navbar-logo"
                    height="40">
                <span>{{ __('Tamman') }}</span>
            </a>

            <!-- Desktop Navigation Menu -->
            <ul class="navbar-menu">
                <li><a href="{{ route('home') }}"
                        class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('Home') }}</a></li>
                <li><a href="{{ route('how-it-works') }}"
                        class="{{ request()->routeIs('how-it-works') ? 'active' : '' }}">{{ __('How It Works') }}</a>
                </li>
                <li><a href="{{ route('specialists.index') }}"
                        class="{{ request()->routeIs('specialists.*') ? 'active' : '' }}">{{ __('Find a Specialist') }}</a>
                </li>
                <li><a href="{{ route('resources.index') }}"
                        class="{{ request()->routeIs('resources.*') ? 'active' : '' }}">{{ __('Resources') }}</a></li>
                <li><a href="{{ route('about') }}"
                        class="{{ request()->routeIs('about') ? 'active' : '' }}">{{ __('About Us') }}</a></li>
            </ul>

            <!-- Auth Buttons + Language Switcher -->
            <div class="navbar-actions">
                <!-- Language Switcher Button -->
                <div style="position: relative; display: inline-block;">
                    <button id="langToggleBtn"
                        style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); border-radius: 40px; padding: 8px 16px; color: white; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; transition: all 0.3s ease;">
                        <i class="fas fa-globe" style="color: #c4b5fd;"></i>
                        <span id="currentLang"
                            style="color: black;">{{ app()->getLocale() === 'ar' ? 'AR' : 'EN' }}</span>
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem; transition: transform 0.3s;"></i>
                    </button>
                    <div id="langDropdownMenu"
                        style="position: absolute; top: 100%; right: 0; margin-top: 10px; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); min-width: 120px; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; z-index: 1000;">
                        <a href="{{ route('lang.switch', 'ar') }}" class="lang-option" data-lang="ar"
                            style="display: flex; align-items: center; gap: 10px; padding: 10px 15px; text-decoration: none; color: #374151; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                            <span style="font-size: 1rem;">🇸🇦</span>
                            <span>العربية</span>
                        </a>
                        <a href="{{ route('lang.switch', 'en') }}" class="lang-option" data-lang="en"
                            style="display: flex; align-items: center; gap: 10px; padding: 10px 15px; text-decoration: none; color: #374151; transition: background 0.2s;">
                            <span style="font-size: 1rem;">🇬🇧</span>
                            <span>English</span>
                        </a>
                    </div>
                </div>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-content="{{ __('Login to your account') }}"
                        data-bs-placement="bottom" data-bs-delay='{"show":300,"hide":100}'>
                        {{ __('Login') }}
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-content="{{ __('Create a new account') }}"
                        data-bs-placement="bottom" data-bs-delay='{"show":300,"hide":100}'>
                        {{ __('Get Started') }}
                    </a>
                @else
                    @php $authUser = Auth::user(); @endphp
                    @if($authUser->hasVerifiedEmail())
                        <!-- FULL AUTHENTICATED HEADER FOR VERIFIED USERS -->
                        <div class="user-menu">
                            <button class="user-menu-toggle" id="userMenuToggle" data-bs-toggle="popover"
                                data-bs-trigger="hover" data-bs-content="{{ __('User Menu') }}" data-bs-placement="bottom"
                                data-bs-delay='{"show":300,"hide":100}'>
                                @php
                                    $profileImageUrl = $authUser->getProfileImageUrl();
                                @endphp

                                @if($profileImageUrl)
                                    <img src="{{ $profileImageUrl }}" alt="{{ $authUser->name }}" class="user-avatar">
                                @else
                                    <div class="user-avatar-placeholder">
                                        {{ mb_substr($authUser->name, 0, 1, 'UTF-8') }}
                                    </div>
                                @endif
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="user-dropdown" id="userDropdown">
                                <div class="user-dropdown-header">
                                    <strong>{{ $authUser->name }}</strong>
                                    <small>{{ $authUser->email }}</small>
                                </div>
                                <div class="user-dropdown-divider"></div>
                                <a href="{{ route('dashboard') }}" class="user-dropdown-item">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
                                </a>
                                <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                                    <i class="fas fa-user"></i> {{ __('Profile') }}
                                </a>
                                <a href="{{ route('settings') }}" class="user-dropdown-item">
                                    <i class="fas fa-sliders-h"></i> {{ __('App Settings') }}
                                </a>
                                @if($authUser->hasRole('patient'))
                                    <a href="{{ route('patient.rewards') }}" class="user-dropdown-item">
                                        <i class="fas fa-star"></i> {{ __('Points') }}
                                        <span class="badge">{{ $authUser->total_points }}</span>
                                    </a>
                                @endif
                                <div class="user-dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="user-dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- LIMITED HEADER FOR UNVERIFIED USERS (only verification warning + logout) -->
                        <div class="verification-warning">
                            <i class="fas fa-envelope"></i>
                            <span class="verification-text">{{ __('Verify your email') }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline-logout">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm">
                                <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                            </button>
                        </form>
                    @endif
                @endguest
            </div>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggle" aria-label="Toggle navigation" data-bs-toggle="popover"
                data-bs-trigger="hover" data-bs-content="{{ __('Open menu') }}" data-bs-placement="bottom"
                data-bs-delay='{"show":300,"hide":100}'>
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Navigation Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <a class="mobile-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Tamman') }}" class="mobile-logo"
                    height="30">
                <span>{{ __('Tamman') }}</span>
            </a>
            <button class="mobile-menu-close" id="mobileMenuClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="mobile-menu-nav">
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> {{ __('Home') }}</a></li>
            <li><a href="{{ route('how-it-works') }}"><i class="fas fa-info-circle"></i> {{ __('How It Works') }}</a>
            </li>
            <li><a href="{{ route('specialists.index') }}"><i class="fas fa-user-md"></i>
                    {{ __('Find a Specialist') }}</a></li>
            <li><a href="{{ route('resources.index') }}"><i class="fas fa-newspaper"></i> {{ __('Resources') }}</a></li>
            <li><a href="{{ route('about') }}"><i class="fas fa-heart"></i> {{ __('About Us') }}</a></li>

            <!-- Mobile Language Switcher - Improved Styling -->
            <li class="mobile-divider"></li>
            <li class="mobile-language-section">
                <div class="mobile-language-title">
                    <i class="fas fa-globe"></i> <span>{{ __('Language') }} / {{ __('اللغة') }}</span>
                </div>
                <div class="mobile-language-buttons">
                    <a href="{{ route('lang.switch', 'ar') }}"
                        class="mobile-lang-btn {{ app()->getLocale() === 'ar' ? 'active' : '' }}">
                        <span class="lang-flag">🇸🇦</span>
                        <span class="lang-name">العربية</span>
                    </a>
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="mobile-lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                        <span class="lang-flag">🇬🇧</span>
                        <span class="lang-name">English</span>
                    </a>
                </div>
            </li>

            @auth
                @php $mobileUser = Auth::user(); @endphp
                @if($mobileUser->hasVerifiedEmail())
                    <!-- FULL MOBILE MENU FOR VERIFIED USERS -->
                    <li class="mobile-divider"></li>
                    <li><a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}</a></li>
                    <li><a href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> {{ __('Profile') }}</a></li>
                    <li><a href="{{ route('settings') }}"><i class="fas fa-sliders-h"></i> {{ __('App Settings') }}</a></li>

                    @if($mobileUser->hasRole('patient'))
                        <li><a href="{{ route('patient.rewards') }}"><i class="fas fa-star"></i> {{ __('Points') }}
                                <span class="badge">{{ $mobileUser->total_points }}</span>
                            </a></li>
                    @endif
                @else
                    <!-- LIMITED MOBILE MENU FOR UNVERIFIED USERS -->
                    <li class="mobile-divider"></li>
                    <li class="mobile-verify-warning">
                        <div class="verify-warning-mobile">
                            <i class="fas fa-envelope"></i>
                            <span>{{ __('Please verify your email address') }}</span>
                        </div>
                    </li>
                @endif

                <li class="mobile-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="mobileLogoutForm">
                        @csrf
                        <button type="submit" class="mobile-logout-btn">
                            <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                        </button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('specialist.apply') }}"><i class="fas fa-briefcase"></i>
                        {{ __('Become a Specialist') }}</a></li>
            @endauth
        </ul>

        @guest
            <div class="mobile-menu-auth">
                <a href="{{ route('login') }}" class="btn btn-outline btn-block">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-block">{{ __('Sign Up') }}</a>
            </div>
        @endguest
    </div>

    <!-- Overlay background -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Main Content -->
    <main id="main-content" class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Tamman') }}" height="40">
                        <h4>{{ __('Tamman') }}</h4>
                    </div>
                    <p>{{ __('A secure digital mental health platform providing psychological support services online, with complete privacy and no social stigma.') }}
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook" data-bs-toggle="popover" data-bs-trigger="hover"
                            data-bs-content="{{ __('Follow us on Facebook') }}" data-bs-placement="top"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter" data-bs-toggle="popover" data-bs-trigger="hover"
                            data-bs-content="{{ __('Follow us on Twitter') }}" data-bs-placement="top"><i
                                class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram" data-bs-toggle="popover" data-bs-trigger="hover"
                            data-bs-content="{{ __('Follow us on Instagram') }}" data-bs-placement="top"><i
                                class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn" data-bs-toggle="popover" data-bs-trigger="hover"
                            data-bs-content="{{ __('Follow us on LinkedIn') }}" data-bs-placement="top"><i
                                class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>{{ __('Quick Links') }}</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('how-it-works') }}">{{ __('How It Works') }}</a></li>
                        <li><a href="{{ route('specialists.index') }}">{{ __('Find a Specialist') }}</a></li>
                        <li><a href="{{ route('resources.index') }}">{{ __('Resources') }}</a></li>
                        <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>{{ __('Support') }}</h4>
                    <ul>
                        <li><a href="{{ route('help-center') }}">{{ __('Help Center') }}</a></li>
                        <li><a href="{{ route('privacy') }}">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('terms') }}">{{ __('Terms of Service') }}</a></li>
                        <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
                        <li><a href="{{ route('specialist.apply') }}">{{ __('Become a Specialist') }}</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>{{ __('Contact Info') }}</h4>
                    <ul class="footer-contact">
                        <li><i class="fas fa-envelope"></i> <span>support@tamman.ps</span></li>
                        <li><i class="fas fa-phone-alt"></i> <span>+970 8 123 4567</span></li>
                        <li><i class="fas fa-map-marker-alt"></i> <span>{{ __('Gaza, Palestine') }}</span></li>
                    </ul>
                    <div class="emergency-badge" data-bs-toggle="popover" data-bs-trigger="hover"
                        data-bs-content="{{ __('24/7 mental health crisis support available') }}"
                        data-bs-placement="top">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ __('24/7 Emergency Helpline: 101') }}</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ __('Tamman') }}. {{ __('All rights reserved.') }}</p>
                <p class="footer-tagline">{{ __('Together for better mental health') }}</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" aria-label="Back to top" data-bs-toggle="popover" data-bs-trigger="hover"
        data-bs-content="{{ __('Back to top') }}" data-bs-placement="left" data-bs-delay='{"show":300,"hide":100}'>
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Vendor JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/fontawesome/js/all.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Bootstrap Popover Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    trigger: 'hover',
                    delay: { show: 300, hide: 100 }
                });
            });
        });
    </script>

    <!-- Mobile Menu Script -->
    <script>
        (function () {
            document.addEventListener('DOMContentLoaded', function () {
                const toggleBtn = document.querySelector('.navbar-toggle');
                const mobileMenu = document.getElementById('mobileMenu');
                const mobileOverlay = document.getElementById('mobileOverlay');
                const closeBtn = document.getElementById('mobileMenuClose');

                if (toggleBtn && mobileMenu && mobileOverlay) {
                    toggleBtn.addEventListener('click', function () {
                        mobileMenu.classList.add('active');
                        mobileOverlay.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    });

                    if (closeBtn) {
                        closeBtn.addEventListener('click', function () {
                            mobileMenu.classList.remove('active');
                            mobileOverlay.classList.remove('active');
                            document.body.style.overflow = '';
                        });
                    }

                    mobileOverlay.addEventListener('click', function () {
                        mobileMenu.classList.remove('active');
                        mobileOverlay.classList.remove('active');
                        document.body.style.overflow = '';
                    });

                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                            mobileMenu.classList.remove('active');
                            mobileOverlay.classList.remove('active');
                            document.body.style.overflow = '';
                        }
                    });
                }
            });
        })();
    </script>

    <!-- User Dropdown Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userMenuToggle = document.querySelector('#userMenuToggle');
            const userDropdown = document.querySelector('#userDropdown');

            if (userMenuToggle && userDropdown) {
                userMenuToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    userDropdown.classList.toggle('show');
                });

                document.addEventListener('click', function (e) {
                    if (!userMenuToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                        userDropdown.classList.remove('show');
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        userDropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>

    <!-- Language Switcher Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const langBtn = document.getElementById('langToggleBtn');
            const langMenu = document.getElementById('langDropdownMenu');

            if (langBtn && langMenu) {
                langBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isOpen = langMenu.style.opacity === '1';
                    langMenu.style.opacity = isOpen ? '0' : '1';
                    langMenu.style.visibility = isOpen ? 'hidden' : 'visible';
                    langMenu.style.transform = isOpen ? 'translateY(-10px)' : 'translateY(0)';
                    const chevron = langBtn.querySelector('.fa-chevron-down');
                    if (chevron) chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                });

                document.addEventListener('click', function (e) {
                    if (!langBtn.contains(e.target) && !langMenu.contains(e.target)) {
                        langMenu.style.opacity = '0';
                        langMenu.style.visibility = 'hidden';
                        langMenu.style.transform = 'translateY(-10px)';
                        const chevron = langBtn.querySelector('.fa-chevron-down');
                        if (chevron) chevron.style.transform = 'rotate(0deg)';
                    }
                });
            }
        });
    </script>

    <!-- Mobile Menu Additional Styles -->
    <style>
        /* Mobile menu container - improved scrolling */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -320px;
            width: 320px;
            max-width: 85%;
            height: 100%;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            z-index: 1002;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: -2px 0 20px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            overflow-x: hidden;
        }

        body.rtl .mobile-menu {
            right: auto;
            left: -320px;
            transition: left 0.3s ease;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
        }

        .mobile-menu.active {
            right: 0;
        }

        body.rtl .mobile-menu.active {
            right: auto;
            left: 0;
        }

        /* Mobile menu header */
        .mobile-menu-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-brand {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .mobile-brand span {
            background: linear-gradient(135deg, #a78bfa, #ec4899);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .mobile-menu-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 1.1rem;
            cursor: pointer;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        .mobile-menu-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Mobile menu navigation */
        .mobile-menu-nav {
            flex: 1;
            padding: 20px;
            list-style: none;
            margin: 0;
        }

        .mobile-menu-nav li {
            margin-bottom: 16px;
        }

        .mobile-menu-nav li a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            text-decoration: none;
            padding: 8px 0;
            transition: color 0.3s ease;
        }

        .mobile-menu-nav li a:hover {
            color: #a78bfa;
        }

        .mobile-menu-nav li a i {
            width: 24px;
            color: #a78bfa;
        }

        /* Mobile divider */
        .mobile-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 16px 0;
        }

        /* Mobile verify warning */
        .mobile-verify-warning {
            margin: 8px 0;
        }

        .verify-warning-mobile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: rgba(254, 243, 199, 0.15);
            border-radius: 12px;
            color: #fcd34d;
            font-size: 0.8rem;
        }

        .verify-warning-mobile i {
            color: #fcd34d;
        }

        /* Mobile Language Section - Improved */
        .mobile-language-section {
            margin: 8px 0;
        }

        .mobile-language-title {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            margin-bottom: 12px;
        }

        .mobile-language-title i {
            color: #a78bfa;
        }

        .mobile-language-buttons {
            display: flex;
            gap: 12px;
        }

        .mobile-lang-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-lang-btn .lang-flag {
            font-size: 1.1rem;
        }

        .mobile-lang-btn .lang-name {
            font-weight: 500;
        }

        .mobile-lang-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .mobile-lang-btn.active {
            background: #7c3aed;
            border-color: #7c3aed;
        }

        /* Badge in mobile menu */
        .mobile-menu-nav li .badge {
            background: #7c3aed;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            margin-left: 8px;
        }

        /* Mobile logout button */
        .mobile-logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            background: none;
            border: none;
            padding: 8px 0;
            color: #f87171;
            font-size: 1rem;
            cursor: pointer;
            transition: color 0.3s ease;
            text-align: left;
        }

        .mobile-logout-btn i {
            width: 24px;
            color: #f87171;
        }

        .mobile-logout-btn:hover {
            color: #ef4444;
        }

        /* Mobile auth buttons */
        .mobile-menu-auth {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mobile-menu-auth .btn {
            width: 100%;
            text-align: center;
            justify-content: center;
        }

        /* Mobile overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .mobile-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        /* RTL support for mobile menu */
        body.rtl .mobile-logout-btn {
            text-align: right;
            flex-direction: row-reverse;
        }

        body.rtl .mobile-menu-nav li .badge {
            margin-left: 0;
            margin-right: 8px;
        }

        body.rtl .mobile-language-buttons {
            flex-direction: row;
        }

        /* Ensure mobile menu works properly */
        @media (max-width: 768px) {
            .navbar-menu {
                display: none;
            }

            .navbar-toggle {
                display: block;
            }
        }
    </style>

    @stack('scripts')
</body>

</html>