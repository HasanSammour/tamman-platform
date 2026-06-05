@extends('emails.layouts.email')

@section('content')
    <h2>{{ __('Treatment Plan Completed!') }} 🏆</h2>
    <p>{{ __('Dear') }} {{ $user->name }},</p>
    <p>{{ __('Congratulations! You have completed the following treatment plan:') }}</p>

    <div class="info-box">
        <p><strong>{{ __('Treatment Plan') }}:</strong> {{ $plan->title }}</p>
        <p><strong>{{ __('Completed Tasks') }}:</strong>
            {{ $plan->tasks->where('is_completed', true)->count() }}/{{ $plan->tasks->count() }}</p>
        <p><strong>{{ __('Bonus Points') }}:</strong> +{{ $bonusPoints }} {{ __('points') }}</p>
    </div>

    <p>{{ __('You are on your way to a healthier life. Keep up the great work!') }}</p>

    <div style="text-align: center;">
        <a href="{{ route('patient.treatment-plan') }}" class="button">{{ __('View My Treatment Plans') }}</a>
    </div>
@endsection