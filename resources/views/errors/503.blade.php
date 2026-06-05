{{-- resources/views/errors/503.blade.php --}}
@extends('errors.layout')

@section('title', __('Under Maintenance') . ' - ' . __('Tamman'))

@section('content')
<div class="error-container animate-scale-in">
    <div class="error-card">
        <div class="error-icon animate-float">
            <div class="icon-bg" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.3);">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="icon-small" style="background: #f59e0b;">
                <i class="fas fa-hard-hat"></i>
            </div>
        </div>
        
        <div class="error-code">503</div>
        <h1 class="error-title">{{ __('Under Maintenance') }}</h1>
        <p class="error-description">{{ __('We are currently improving the platform.') }}<br>{{ __('The site will be back online shortly.') }}</p>
        
        <div class="error-actions">
            <button onclick="location.reload()" class="btn-check" style="background: #7c3aed; color: white; border: none; padding: 10px 24px; border-radius: 40px; cursor: pointer;">
                <i class="fas fa-sync-alt"></i> {{ __('Check Status') }}
            </button>
            <a href="mailto:support@tamman.ps" class="btn-support" style="background: #f3f4f6; color: #374151; text-decoration: none; padding: 10px 24px; border-radius: 40px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-envelope"></i> {{ __('Contact Support') }}
            </a>
        </div>
        
        <div class="emergency-note">
            <i class="fas fa-heartbeat"></i>
            <span>{{ __('Crisis Support Available 24/7:') }} <a href="tel:101">{{ __('Call 101') }}</a></span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .error-card::before {
        background: linear-gradient(90deg, #7c3aed, #a78bfa, #7c3aed);
    }
    
    .btn-check:hover {
        background: #6d28d9 !important;
        transform: translateY(-2px);
    }
    
    .btn-support:hover {
        background: #e5e7eb !important;
        transform: translateY(-2px);
    }
</style>
@endpush