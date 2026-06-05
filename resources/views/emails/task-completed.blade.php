@extends('emails.layouts.email')

@section('content')
    <h2>{{ __('Task Completed!') }} 🎯</h2>
    <p>{{ __('Dear') }} {{ $user->name }},</p>
    <p>{{ __('You have completed the following task in your treatment plan:') }}</p>

    <div class="info-box">
        <p><strong>{{ __('Task') }}:</strong> {{ $task->title }}</p>
        <p><strong>{{ __('Treatment Plan') }}:</strong> {{ $task->plan->title }}</p>
        <p><strong>{{ __('Points Earned') }}:</strong> +{{ $points }} {{ __('points') }}</p>
    </div>

    <p>{{ __('Keep completing your tasks to earn more points!') }}</p>

    <div style="text-align: center;">
        <a href="{{ route('patient.treatment-plan.show', $task->plan_id) }}"
            class="button">{{ __('View My Treatment Plan') }}</a>
    </div>
@endsection