{{-- resources/views/auth/confirm-password.blade.php --}}
@extends('layouts.auth')

@section('title', __('Confirm Password') . ' - ' . __('Tamman'))

@section('auth-content')
<div class="auth-header">
    <div class="auth-icon">
        <i class="fas fa-shield-alt"></i>
    </div>
    <h2>{{ __('Confirm Password') }}</h2>
    <p>{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
    @csrf

    <!-- Password -->
    <div class="form-group">
        <label for="password">{{ __('Password') }} <span class="required">*</span></label>
        <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autofocus placeholder="{{ __('Enter your password') }}">
            <i class="fas fa-eye-slash password-toggle" style="cursor: pointer;" onclick="togglePassword(this)"></i>
        </div>
        @error('password')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn-auth">
        <i class="fas fa-check-circle"></i> {{ __('Confirm') }}
    </button>
</form>

<script>
    function togglePassword(element) {
        var wrapper = element.parentElement;
        var input = wrapper.querySelector('input');
        
        if (input) {
            if (input.type === 'password') {
                input.type = 'text';
                element.classList.remove('fa-eye-slash');
                element.classList.add('fa-eye');
            } else {
                input.type = 'password';
                element.classList.remove('fa-eye');
                element.classList.add('fa-eye-slash');
            }
        }
    }
</script>

@push('styles')
<style>
    /* No additional styles needed - uses auth.css */
</style>
@endpush
@endsection