{{-- resources/views/errors/429.blade.php --}}
@extends('errors.layout')

@section('title', __('Too Many Requests') . ' - ' . __('Tamman'))

@section('content')
<div class="error-container animate-scale-in">
    <div class="error-card">
        <div class="error-icon animate-float">
            <div class="icon-bg" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.3);">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <div class="icon-small" style="background: #f97316;">
                <i class="fas fa-exclamation"></i>
            </div>
        </div>
        
        <div class="error-code">429</div>
        <h1 class="error-title">{{ __('Too Many Requests') }}</h1>
        <p class="error-description">{{ __('You have made too many requests recently.') }}<br>{{ __('Please wait a moment before trying again.') }}</p>
        
        <div class="error-actions">
            <button onclick="location.reload()" class="btn-retry" id="retryBtn" style="background: #ef4444; color: white; border: none; padding: 10px 24px; border-radius: 40px; cursor: pointer;" disabled>
                <i class="fas fa-sync-alt"></i> <span id="retryText">{{ __('Try Again') }}</span>
                <span id="countdownText" style="margin-left: 5px;">(60)</span>
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
        background: linear-gradient(90deg, #ef4444, #f97316, #ef4444);
    }
    
    .btn-retry:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .btn-retry:hover:not(:disabled) {
        background: #dc2626 !important;
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
    let seconds = 60;
    const retryBtn = document.getElementById('retryBtn');
    const countdownText = document.getElementById('countdownText');
    const retryText = document.getElementById('retryText');
    
    const countdown = setInterval(() => {
        seconds--;
        if (countdownText) countdownText.textContent = `(${seconds})`;
        
        if (seconds <= 0) {
            clearInterval(countdown);
            if (retryBtn) {
                retryBtn.disabled = false;
                if (countdownText) countdownText.style.display = 'none';
                if (retryText) retryText.textContent = '{{ __("Try Again") }}';
            }
        }
    }, 1000);
</script>
@endpush