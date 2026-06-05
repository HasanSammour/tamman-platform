@extends('emails.layouts.email')

@section('content')
<h2>{{ __('Congratulations! 🎉') }}</h2>

<p>{{ __('Dear') }} {{ $user->name }},</p>

<p>{{ __('You have successfully redeemed a free :session_type session!', ['session_type' => __(ucfirst($sessionType))]) }}</p>

<div class="info-box">
    <p><strong>{{ __('Reward') }}:</strong> {{ $rewardName }}</p>
    <p><strong>{{ __('Points Spent') }}:</strong> {{ number_format($pointsSpent) }} {{ __('points') }}</p>
    <p><strong>{{ __('Session Type') }}:</strong> {{ __(ucfirst($sessionType)) }}</p>
</div>

<p>{{ __('To book your free session:') }}</p>
<ol>
    <li>{{ __('Go to the specialist page') }}</li>
    <li>{{ __('Select your preferred date and time') }}</li>
    <li>{{ __('The session will be automatically marked as free') }}</li>
</ol>

<div style="text-align: center;">
    <a href="{{ route('specialists.index') }}" class="button">{{ __('Book Your Free Session') }}</a>
</div>

<p>{{ __('Note: This free session does not expire, but we encourage you to book it soon!') }}</p>
@endsection