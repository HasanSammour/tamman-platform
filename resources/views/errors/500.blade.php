{{-- resources/views/errors/500.blade.php --}}
@extends('errors.layout')

@section('title', __('Server Error') . ' - ' . __('Tamman'))

@section('content')
<div class="error-container animate-scale-in">
    <div class="error-card">
        <div class="error-icon animate-float">
            <div class="icon-bg" style="background: linear-gradient(135deg, #6b7280, #4b5563); box-shadow: 0 10px 25px -5px rgba(107, 114, 128, 0.3);">
                <i class="fas fa-server"></i>
            </div>
            <div class="icon-small" style="background: #f59e0b;">
                <i class="fas fa-tools"></i>
            </div>
        </div>
        
        <div class="error-code">500</div>
        <h1 class="error-title">{{ __('Server Error') }}</h1>
        <p class="error-description">{{ __('Something went wrong on our end.') }}<br>{{ __('Our team has been notified and is working to fix the issue.') }}</p>
        
        <div class="error-actions">
            <button onclick="location.reload()" class="btn-retry" style="background: #7c3aed; color: white; border: none; padding: 10px 24px; border-radius: 40px; cursor: pointer;">
                <i class="fas fa-sync-alt"></i> {{ __('Try Again') }}
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
        background: linear-gradient(90deg, #6b7280, #9ca3af, #6b7280);
    }
    
    .btn-retry:hover {
        background: #6d28d9 !important;
        transform: translateY(-2px);
    }
</style>
@endpush