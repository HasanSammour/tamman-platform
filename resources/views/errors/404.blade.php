{{-- resources/views/errors/404.blade.php --}}
@extends('errors.layout')

@section('title', __('Page Not Found') . ' - ' . __('Tamman'))

@section('content')
<div class="error-container animate-scale-in">
    <div class="error-card">
        <div class="error-icon animate-float">
            <div class="icon-bg" style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);">
                <i class="fas fa-search"></i>
            </div>
            <div class="icon-small" style="background: #f59e0b;">
                <i class="fas fa-question"></i>
            </div>
        </div>
        
        <div class="error-code">404</div>
        <h1 class="error-title">{{ __('Page Not Found') }}</h1>
        <p class="error-description">{{ __('Oops! The page you are looking for does not exist.') }}<br>{{ __('It might have been moved, deleted, or you may have typed the address incorrectly.') }}</p>
        
        <div class="error-actions">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Go Back') }}
            </a>
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
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #3b82f6);
    }
    
    .error-description {
        color: #6b7280;
    }
</style>
@endpush