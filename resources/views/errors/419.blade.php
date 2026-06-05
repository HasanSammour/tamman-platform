{{-- resources/views/errors/419.blade.php --}}
@extends('errors.layout')

@section('title', __('Session Expired') . ' - ' . __('Tamman'))

@section('content')
<div class="error-container animate-scale-in">
    <div class="error-card">
        <div class="error-icon animate-float">
            <div class="icon-bg" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.3);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="icon-small" style="background: #7c3aed;">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
        
        <div class="error-code">419</div>
        <h1 class="error-title">{{ __('Session Expired') }}</h1>
        <p class="error-description">{{ __('Your session has expired due to inactivity.') }}<br>{{ __('Please refresh the page and try again.') }}</p>
        
        <div class="error-actions">
            <button onclick="location.reload()" class="btn-refresh" style="background: #f59e0b; color: white; border: none; padding: 10px 24px; border-radius: 40px; cursor: pointer;">
                <i class="fas fa-sync-alt"></i> {{ __('Refresh Page') }}
            </button>
            <a href="{{ route('home') }}" class="btn-home">
                <i class="fas fa-home"></i> {{ __('Home') }}
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-dashboard">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
                </a>
            @endauth
        </div>
        
        <div class="emergency-note">
            <i class="fas fa-heartbeat"></i>
            <span>{{ __('Need immediate help?') }} <a href="tel:101">{{ __('Call 101') }}</a> {{ __('24/7 Crisis Support') }}</span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .error-card::before {
        background: linear-gradient(90deg, #f59e0b, #fbbf24, #f59e0b);
    }
    
    .btn-refresh:hover {
        background: #d97706 !important;
        transform: translateY(-2px);
    }
</style>
@endpush