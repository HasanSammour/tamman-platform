@extends('emails.layouts.email')

@section('content')
    <h2>{{ $data['reward_type'] === 'donate' ? 'Thank You for Your Donation! 💝' : 'Reward Redeemed Successfully! 🎉' }}
    </h2>

    <p>{{ __('Dear') }} {{ $data['user']->name }},</p>

    <p>{{ __('You have successfully redeemed your reward:') }}</p>

    <div class="info-box">
        <p><strong>{{ __('Reward') }}:</strong> {{ $data['reward_name'] }}</p>
        <p><strong>{{ __('Points Spent') }}:</strong> {{ number_format($data['points_spent']) }} {{ __('points') }}</p>
        <p><strong>{{ __('Remaining Points') }}:</strong> {{ number_format($data['remaining_points']) }} {{ __('points') }}
        </p>
        <p><strong>{{ __('Redeemed On') }}:</strong> {{ $data['redeemed_at']->translatedFormat('l, F d, Y h:i A') }}</p>
    </div>

    @if($data['reward_type'] === 'credit')
        <div class="info-box">
            <p class="success"><strong>${{ number_format($data['credit_amount'], 2) }}
                    {{ __('Added to Your Credit Balance') }}</strong></p>
            <p>{{ __('The amount has been added to your account balance. You can use it to book sessions.') }}</p>
        </div>
    @endif

    @if($data['reward_type'] === 'free_session')
        <div class="info-box">
            <p class="success"><strong>{{ __('Free Session Unlocked!') }}</strong></p>
            <p>{{ __('You can now book a free :session_type session. When booking, your session will be automatically marked as free.', ['session_type' => __(ucfirst($data['session_type']))]) }}
            </p>
        </div>
    @endif

    @if($data['reward_type'] === 'donate')
        <div class="info-box">
            <p class="success"><strong>{{ __('Your Generosity Makes a Difference!') }}</strong></p>
            <p>{{ __('Your donated points will help patients in need receive mental health support. Thank you for being part of our community!') }}
            </p>
        </div>
    @endif

    <p>{{ __('Thank you for being an active member of our community!') }}</p>

    <div style="text-align: center;">
        <a href="{{ route('patient.rewards') }}" class="button">{{ __('View Rewards') }}</a>
    </div>
@endsection