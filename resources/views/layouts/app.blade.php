{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Tamman') . ' - ' . __('Dashboard'))</title>
    <meta name="description" content="@yield('description', __('Your personal dashboard for mental health support'))">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,100..900&family=Cairo:wght@200..1000&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fullcalendar/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/apexcharts/apexcharts.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

    </style>

    @stack('styles')
</head>

<body class="app-body">
    <div class="app-wrapper">
        <!-- Sidebar (Desktop) -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-header">
                <a href="{{ route('home') }}" class="sidebar-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Tamman') }}"
                        class="sidebar-logo">
                    <span>{{ __('Tamman') }}</span>
                </a>
                <button class="sidebar-toggle" id="sidebarToggle" data-bs-toggle="popover" data-bs-trigger="hover"
                    data-bs-content="{{ __('Collapse Sidebar') }}" data-bs-placement="left"
                    data-bs-delay='{"show":300,"hide":100}' aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="sidebar-user">
                @php
                    $profileImage = Auth::user()->getProfileImageUrl();
                @endphp

                @if($profileImage)
                    <img src="{{ $profileImage }}" alt="{{ Auth::user()->name }}" class="sidebar-user-avatar">
                @else
                    <div class="sidebar-user-avatar-placeholder">{{ mb_substr(Auth::user()->name, 0, 1, 'UTF-8') }}</div>
                @endif
                <div class="sidebar-user-info">
                    <h4>{{ Auth::user()->name }}</h4>
                    <span class="sidebar-user-role">
                        @if(Auth::user()->hasRole('admin'))
                            <i class="fas fa-shield-alt"></i> {{ __('Administrator') }}
                        @elseif(Auth::user()->hasRole('specialist'))
                            <i class="fas fa-user-md"></i> {{ __('Specialist') }}
                        @else
                            <i class="fas fa-user"></i> {{ __('Patient') }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Points Display for Patients -->
            @if(Auth::user()->hasRole('patient'))
                <div class="sidebar-points-card">
                    <div class="points-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="points-info">
                        <span class="points-label">{{ __('Tamman Points') }}</span>
                        <span class="points-value">{{ number_format(Auth::user()->total_points) }}</span>
                    </div>
                </div>
            @endif

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Dashboard') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-tachometer-alt"></i> <span>{{ __('Dashboard') }}</span>
                    </a>
                    <a href="{{ route('admin.users') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Users Management') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-users"></i> <span>{{ __('Users Management') }}</span>
                    </a>
                    <a href="{{ route('admin.specialists') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.specialists*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Specialists') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-user-md"></i> <span>{{ __('Specialists') }}</span>
                    </a>
                    <a href="{{ route('admin.approvals') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.approvals*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Verification Requests') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-clipboard-list"></i> <span>{{ __('Verification Requests') }}</span>
                        @php $pendingCount = App\Models\SpecialistProfile::where('is_verified', false)->count(); @endphp
                        @if($pendingCount > 0) <span class="badge">{{ $pendingCount }}</span> @endif
                    </a>
                    <a href="{{ route('admin.payments.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Payments') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-dollar-sign"></i> <span>{{ __('Payments') }}</span>
                    </a>
                    <a href="{{ route('admin.content') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.content*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Content') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-newspaper"></i> <span>{{ __('Content') }}</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Reports') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-chart-line"></i> <span>{{ __('Reports') }}</span>
                    </a>
                    <a href="{{ route('admin.analytics.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Analytics') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-chart-bar"></i> <span>{{ __('Analytics') }}</span>
                    </a>
                    <a href="{{ route('admin.logs') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.logs*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('System Logs') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-history"></i> <span>{{ __('System Logs') }}</span>
                    </a>
                    <a href="{{ route('settings') }}"
                        class="sidebar-nav-link {{ request()->routeIs('settings') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('App Settings') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-sliders-h"></i> <span>{{ __('App Settings') }}</span>
                    </a>

                @elseif(Auth::user()->hasRole('specialist'))
                    <a href="{{ route('specialist.dashboard') }}"
                        class="sidebar-nav-link {{ request()->routeIs('specialist.dashboard') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Dashboard') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-tachometer-alt"></i> <span>{{ __('Dashboard') }}</span>
                    </a>
                    <a href="{{ route('specialist.schedule') }}"
                        class="sidebar-nav-link {{ request()->routeIs('specialist.schedule*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Schedule') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-calendar-alt"></i> <span>{{ __('Schedule') }}</span>
                    </a>
                    <a href="{{ route('specialist.clients.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('specialist.clients*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('My Clients') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-users"></i> <span>{{ __('My Clients') }}</span>
                    </a>
                    <a href="{{ route('specialist.treatment-plans.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('specialist.treatment-plans*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Treatment Plans') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-tasks"></i> <span>{{ __('Treatment Plans') }}</span>
                    </a>
                    <a href="{{ route('chat.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('chat*') ? 'active' : '' }}" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-content="{{ __('Chat System') }}" data-bs-placement="right"
                        data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-envelope"></i> <span>{{ __('Chat System') }}</span>
                        <span class="badge unread-count" id="chatUnreadCount">0</span>
                    </a>
                    <a href="{{ route('specialist.session-notes.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('specialist.session-notes*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Session Notes') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-notes-medical"></i> <span>{{ __('Session Notes') }}</span>
                    </a>
                    <a href="{{ route('specialist.earnings') }}"
                        class="sidebar-nav-link {{ request()->routeIs('specialist.earnings*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Earnings') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-dollar-sign"></i> <span>{{ __('Earnings') }}</span>
                    </a>
                    <a href="{{ route('settings') }}"
                        class="sidebar-nav-link {{ request()->routeIs('settings') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('App Settings') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-sliders-h"></i> <span>{{ __('App Settings') }}</span>
                    </a>

                @else
                    <a href="{{ route('patient.dashboard') }}"
                        class="sidebar-nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Dashboard') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-tachometer-alt"></i> <span>{{ __('Dashboard') }}</span>
                    </a>
                    <a href="{{ route('patient.mood-tracker') }}"
                        class="sidebar-nav-link {{ request()->routeIs('patient.mood-tracker*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Mood Tracker') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-smile"></i> <span>{{ __('Mood Tracker') }}</span>
                    </a>
                    <a href="{{ route('patient.tests') }}"
                        class="sidebar-nav-link {{ request()->routeIs('patient.tests*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Self Assessments') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-clipboard-list"></i> <span>{{ __('Self Assessments') }}</span>
                    </a>
                    <a href="{{ route('patient.sessions') }}"
                        class="sidebar-nav-link {{ request()->routeIs('patient.sessions*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('My Sessions') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-calendar-check"></i> <span>{{ __('My Sessions') }}</span>
                    </a>
                    <a href="{{ route('patient.treatment-plan') }}"
                        class="sidebar-nav-link {{ request()->routeIs('patient.treatment-plan*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('My Treatment Plan') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-tasks"></i> <span>{{ __('My Treatment Plan') }}</span>
                    </a>
                    <a href="{{ route('chat.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('chat*') ? 'active' : '' }}" data-bs-toggle="popover"
                        data-bs-trigger="hover" data-bs-content="{{ __('Chat System') }}" data-bs-placement="right"
                        data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-comments"></i> <span>{{ __('Chat System') }}</span>
                        <span class="badge unread-count" id="chatUnreadCount">0</span>
                    </a>
                    <a href="{{ route('patient.rewards') }}"
                        class="sidebar-nav-link {{ request()->routeIs('patient.rewards*') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('Rewards') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-gift"></i> <span>{{ __('Rewards') }}</span>
                    </a>
                    <a href="{{ route('settings') }}"
                        class="sidebar-nav-link {{ request()->routeIs('settings') ? 'active' : '' }}"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="{{ __('App Settings') }}"
                        data-bs-placement="right" data-bs-delay='{"show":300,"hide":100}'>
                        <i class="fas fa-sliders-h"></i> <span>{{ __('App Settings') }}</span>
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="app-main">
            <!-- Top Navbar -->
            <nav class="app-topbar">
                <div class="topbar-left">
                    <button class="mobile-sidebar-toggle" id="mobileSidebarToggle" aria-label="Toggle mobile menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', __('Dashboard'))</h1>
                </div>

                <div class="topbar-right">
                    <!-- Role-based Action Buttons -->
                    <div class="topbar-actions">
                        @if(Auth::user()->hasRole('patient'))
                            <a href="{{ route('donate') }}" class="topbar-action-btn" data-bs-toggle="popover"
                                data-bs-trigger="hover" data-bs-content="{{ __('Become a Donor') }}"
                                data-bs-placement="bottom">
                                <i class="fas fa-hand-holding-heart"></i>
                                <span>{{ __('Become a Donor') }}</span>
                            </a>
                            <a href="{{ route('patient.add-credits') }}" class="topbar-action-btn" data-bs-toggle="popover"
                                data-bs-trigger="hover" data-bs-content="{{ __('Add Credits') }}"
                                data-bs-placement="bottom">
                                <i class="fas fa-plus-circle"></i>
                                <span>{{ __('Add Credits') }}</span>
                            </a>
                        @elseif(Auth::user()->hasRole('specialist'))
                            <a href="{{ route('donate') }}" class="topbar-action-btn" data-bs-toggle="popover"
                                data-bs-trigger="hover" data-bs-content="{{ __('Become a Donor') }}"
                                data-bs-placement="bottom">
                                <i class="fas fa-hand-holding-heart"></i>
                                <span>{{ __('Become a Donor') }}</span>
                            </a>
                        @endif
                    </div>

                    <!-- Impersonate Banner -->
                    <!-- Impersonate Banner -->
                    @if(session()->has('impersonate_admin'))
                        @php
                            $isRTL = app()->getLocale() === 'ar';
                            $bannerSide = $isRTL ? 'left' : 'right';
                        @endphp
                        <div id="impersonateBanner"
                            style="position: fixed; bottom: 20px; {{ $bannerSide }}: 20px; background: #1e1b4b; color: white; padding: 12px 20px; border-radius: 50px; display: flex; align-items: center; gap: 12px; z-index: 999999; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                            <i class="fas fa-user-secret"></i>
                            <span>{{ __('You are impersonating a user') }}</span>
                            <form method="POST" action="{{ route('admin.users.stop-impersonate') }}"
                                style="display: inline; margin: 0;">
                                @csrf
                                <button type="submit"
                                    style="background: rgba(255,255,255,0.2); color: white; padding: 6px 14px; border-radius: 40px; border: none; cursor: pointer; font-size: 0.75rem;">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('Stop') }}
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="topbar-notifications">
                        <button class="notifications-toggle" id="notificationsToggle" data-bs-toggle="popover"
                            data-bs-trigger="hover" data-bs-content="{{ __('Notifications') }}"
                            data-bs-placement="bottom" data-bs-delay='{"show":300,"hide":100}'
                            aria-label="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notifications-badge" id="notificationsCount" style="display: none;">0</span>
                        </button>
                        <div class="notifications-dropdown" id="notificationsDropdown">
                            <div class="notifications-header">
                                <h4>{{ __('Notifications') }}</h4>
                                <button class="mark-all-read"
                                    id="dropdownMarkAllRead">{{ __('Mark all as read') }}</button>
                            </div>
                            <div class="notifications-list" id="notificationsList">
                                <div class="notifications-empty">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>{{ __('Loading...') }}</p>
                                </div>
                            </div>
                            <div class="notifications-footer">
                                <a href="{{ route('notifications.index') }}">{{ __('View All Notifications') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="topbar-user">
                        <button class="user-menu-toggle" id="topbarUserMenuToggle" data-bs-toggle="popover"
                            data-bs-trigger="hover" data-bs-content="{{ __('User Menu') }}" data-bs-placement="bottom"
                            data-bs-delay='{"show":300,"hide":100}' aria-label="User menu">
                            @php
                                $topbarProfileImage = Auth::user()->getProfileImageUrl();
                            @endphp

                            @if($topbarProfileImage)
                                <img src="{{ $topbarProfileImage }}" alt="{{ Auth::user()->name }}" class="user-avatar-sm">
                            @else
                                <div class="user-avatar-placeholder-sm">{{ mb_substr(Auth::user()->name, 0, 1, 'UTF-8') }}
                                </div>
                            @endif
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown" id="topbarUserDropdown">
                            <div class="user-dropdown-header">
                                <strong>{{ Auth::user()->name }}</strong>
                                <small>{{ Auth::user()->email }}</small>
                            </div>
                            <div class="user-dropdown-divider"></div>
                            <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                                <i class="fas fa-user"></i> {{ __('My Profile') }}
                            </a>
                            <a href="{{ route('settings') }}" class="user-dropdown-item">
                                <i class="fas fa-sliders-h"></i> {{ __('App Settings') }}
                            </a>
                            @if(Auth::user()->hasRole('patient'))
                                <a href="{{ route('patient.rewards') }}" class="user-dropdown-item">
                                    <i class="fas fa-star"></i> {{ __('Points') }}
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
                </div>
            </nav>

            <div class="app-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info animate-fade-in-up">
                        <i class="fas fa-info-circle"></i> {{ session('info') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning animate-fade-in-up">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Vendor JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/fontawesome/js/all.min.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('vendor/chartjs/Chart.umd.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Bootstrap Popover Initialization & Sidebar Toggle -->
    <script>
        (function () {
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize all Bootstrap Popovers
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl, {
                        trigger: 'hover',
                        delay: { show: 300, hide: 100 }
                    });
                });

                // Update sidebar toggle popover text when sidebar collapses/expands
                var sidebar = document.getElementById('appSidebar');
                var toggleBtn = document.getElementById('sidebarToggle');

                if (sidebar && toggleBtn) {
                    var popover = bootstrap.Popover.getInstance(toggleBtn);

                    function updateTogglePopover() {
                        var isCollapsed = sidebar.classList.contains('collapsed');
                        var newContent = isCollapsed ? '{{ __("Expand Sidebar") }}' : '{{ __("Collapse Sidebar") }}';

                        if (popover) {
                            popover.setContent({ '.popover-body': newContent });
                        } else {
                            toggleBtn.setAttribute('data-bs-content', newContent);
                        }
                    }

                    var observer = new MutationObserver(function (mutations) {
                        mutations.forEach(function (mutation) {
                            if (mutation.attributeName === 'class') {
                                updateTogglePopover();
                            }
                        });
                    });
                    observer.observe(sidebar, { attributes: true });

                    function updatePopoverPlacement() {
                        var isRTL = document.documentElement.getAttribute('dir') === 'rtl';
                        if (popover) {
                            var newPlacement = isRTL ? 'right' : 'left';
                            popover.setOptions({ placement: newPlacement });
                        }
                    }

                    var rtlObserver = new MutationObserver(function (mutations) {
                        mutations.forEach(function (mutation) {
                            if (mutation.attributeName === 'dir') {
                                updatePopoverPlacement();
                            }
                        });
                    });
                    rtlObserver.observe(document.documentElement, { attributes: true });
                }

                var sidebarToggle = document.getElementById('sidebarToggle');
                var mobileToggle = document.getElementById('mobileSidebarToggle');
                var overlay = document.getElementById('sidebarOverlay');

                if (sidebarToggle && sidebar) {
                    var savedState = localStorage.getItem('sidebarCollapsed');
                    if (savedState === 'true') {
                        sidebar.classList.add('collapsed');
                    }

                    sidebarToggle.addEventListener('click', function (e) {
                        e.preventDefault();
                        sidebar.classList.toggle('collapsed');
                        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                        window.dispatchEvent(new Event('resize'));
                    });
                }

                if (mobileToggle && sidebar && overlay) {
                    mobileToggle.addEventListener('click', function () {
                        sidebar.classList.add('mobile-open');
                        overlay.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    });

                    overlay.addEventListener('click', function () {
                        sidebar.classList.remove('mobile-open');
                        overlay.classList.remove('active');
                        document.body.style.overflow = '';
                    });
                }
            });
        })();

        // ==================== NOTIFICATIONS SYSTEM ====================
        (function () {
            // Load notifications for dropdown
            async function loadDropdownNotifications() {
                const notificationsList = document.getElementById('notificationsList');
                if (!notificationsList) return;

                try {
                    const response = await fetch('{{ route("notifications.fetch") }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success && data.notifications && data.notifications.length > 0) {
                        notificationsList.innerHTML = data.notifications.map(notification => `
                    <div class="notification-item ${!notification.is_read ? 'unread' : ''}" data-id="${notification.id}">
                        <div class="notification-icon">
                            <i class="fas ${notification.icon}" style="color: ${notification.color}"></i>
                        </div>
                        <div class="notification-content">
                            <p>${escapeHtml(notification.message)}</p>
                            <small>${notification.time_ago}</small>
                        </div>
                    </div>
                `).join('');

                        // Add click handlers to mark as read
                        document.querySelectorAll('#notificationsList .notification-item').forEach(item => {
                            item.addEventListener('click', async () => {
                                const id = item.dataset.id;
                                if (!id) return;

                                try {
                                    const markResponse = await fetch(`/notifications/${id}/read`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Content-Type': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    });
                                    const markData = await markResponse.json();
                                    if (markData.success) {
                                        loadDropdownNotifications();
                                        updateNotificationBadge(markData.unread_count);
                                    }
                                } catch (error) {
                                    console.error('Error marking notification as read:', error);
                                }
                            });
                        });
                    } else if (data.success && (!data.notifications || data.notifications.length === 0)) {
                        notificationsList.innerHTML = `
                    <div class="notifications-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>{{ __('No new notifications') }}</p>
                    </div>
                `;
                    }

                    // Update badge count
                    if (data.unread_count !== undefined) {
                        updateNotificationBadge(data.unread_count);
                    }

                } catch (error) {
                    console.error('Error loading notifications:', error);
                    notificationsList.innerHTML = `
                <div class="notifications-empty">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>{{ __('Error loading notifications') }}</p>
                </div>
            `;
                }
            }

            function updateNotificationBadge(count) {
                const badge = document.getElementById('notificationsCount');
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }

            // Mark all as read from dropdown
            async function markAllAsRead() {
                try {
                    const response = await fetch('{{ route("notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        loadDropdownNotifications();
                        updateNotificationBadge(0);

                        // Show success toast
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success") }}',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, function (m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function () {
                // Load notifications if the dropdown exists
                if (document.getElementById('notificationsList')) {
                    loadDropdownNotifications();
                    // Refresh every 30 seconds
                    setInterval(loadDropdownNotifications, 30000);
                }

                // Mark all as read button
                const markAllBtn = document.getElementById('dropdownMarkAllRead');
                if (markAllBtn) {
                    markAllBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        markAllAsRead();
                    });
                }
            });
        })();

        // ==================== CHAT UNREAD COUNT ====================
        async function updateChatUnreadCount() {
            const chatUnreadSpan = document.getElementById('chatUnreadCount');
            if (!chatUnreadSpan) return;

            try {
                const response = await fetch('{{ route("chat.unread") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success && data.unread_count !== undefined) {
                    const unreadCount = data.unread_count;

                    if (unreadCount > 0) {
                        chatUnreadSpan.textContent = unreadCount > 99 ? '99+' : unreadCount;
                        chatUnreadSpan.style.display = 'inline-block';
                    } else {
                        chatUnreadSpan.style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Error fetching chat unread count:', error);
            }
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', function () {
            updateChatUnreadCount();
            // Update every 30 seconds
            setInterval(updateChatUnreadCount, 30000);
        });

    </script>
    @stack('scripts')
</body>

</html>