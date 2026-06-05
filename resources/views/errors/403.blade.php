{{-- resources/views/errors/403.blade.php --}}
@extends('errors.layout')

@section('title', __('Access Denied') . ' - ' . __('Tamman'))

@section('content')
<div class="error-container animate-scale-in">
    <div class="error-card">
        <div class="error-icon animate-float">
            <div class="icon-bg" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.3);">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="icon-small" style="background: #1f2937;">
                <i class="fas fa-lock" style="color: #fbbf24;"></i>
            </div>
        </div>
        
        <div class="error-code">403</div>
        <h1 class="error-title">{{ __('Access Denied') }}</h1>
        <p class="error-description">{{ __('You do not have permission to access this page.') }}<br>{{ __('This area is restricted to authorized personnel only.') }}</p>
        
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
        background: linear-gradient(90deg, #ef4444, #f59e0b, #ef4444);
    }
    
    .error-description {
        color: #ef4444;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }
</style>
@endpush