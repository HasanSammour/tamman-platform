@extends('emails.layouts.email')

@section('content')
    <h2 style="color: #1f2937; text-align: center; margin-bottom: 16px;">{{ __('Booking Confirmed!') }}</h2>
    <p style="color: #6b7280; text-align: center; margin-bottom: 24px;">
        {{ __('Your session has been successfully booked') }}</p>

    <p style="color: #4b5563; margin-bottom: 20px;">
        {{ __('Dear') }} <strong>{{ $user->name }}</strong>,
    </p>

    <p style="color: #4b5563; margin-bottom: 20px;">
        {{ __('Your session has been confirmed. Here are the details:') }}
    </p>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">{{ __('Specialist') }}: </td>
                <td class="info-value">{{ $session->specialist->name }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Specialization') }}: </td>
                <td class="info-value">{{ $session->specialist->specialistProfile->specialization ?? __('Not specified') }}
                </td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Date') }}: </td>
                <td class="info-value">
                    {{ \Carbon\Carbon::parse($session->session_datetime)->translatedFormat('l, F d, Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Time') }}: </td>
                <td class="info-value">{{ \Carbon\Carbon::parse($session->session_datetime)->format('h:i A') }} -
                    {{ \Carbon\Carbon::parse($session->session_datetime)->addMinutes($session->duration_minutes)->format('h:i A') }}
                </td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Session Type') }}: </td>
                <td class="info-value">
                    @if($session->session_type == 'video')
                        <span
                            style="background: #ede9fe; color: #7c3aed; padding: 4px 12px; border-radius: 20px; font-size: 12px;">{{ __('Video Session') }}</span>
                    @elseif($session->session_type == 'audio')
                        <span
                            style="background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 20px; font-size: 12px;">{{ __('Audio Session') }}</span>
                    @else
                        <span
                            style="background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 20px; font-size: 12px;">{{ __('Text Chat Session') }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Duration') }}: </td>
                <td class="info-value">{{ $session->duration_minutes }} {{ __('minutes') }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Payment Method') }}: </td>
                <td class="info-value">{{ $paymentMethod }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Amount Paid') }}: </td>
                <td class="info-value"><strong>${{ number_format($amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    @if($session->meeting_link)
        <div class="meeting-box">
            <h3 style="color: #1f2937; margin-bottom: 8px;">{{ __('Meeting Link') }}</h3>
            <p style="color: #6b7280; font-size: 13px;">
                {{ __('Join your session using the link below at the scheduled time.') }}</p>
            <div class="meeting-link">
                <a href="{{ $session->meeting_link }}">{{ $session->meeting_link }}</a>
            </div>
            <a href="{{ $session->meeting_link }}" class="btn-primary">{{ __('Join Session') }}</a>
        </div>
    @endif

    <div class="tips-box">
        <div class="tips-title">
            📋 {{ __('Important Tips') }}
        </div>
        <ul class="tips-list">
            <li>{{ __('Join 5 minutes before the scheduled time') }}</li>
            <li>{{ __('Test your microphone and camera before joining') }}</li>
            <li>{{ __('Ensure you have a stable internet connection') }}</li>
            <li>{{ __('Find a quiet and private space for your session') }}</li>
        </ul>
    </div>

    <div class="btn-group">
        <a href="{{ route('patient.sessions.show', $session->id) }}"
            class="btn-primary">{{ __('View Session Details') }}</a>
        <a href="{{ route('patient.dashboard') }}" class="btn-secondary">{{ __('Go to Dashboard') }}</a>
    </div>

    <p style="color: #6b7280; font-size: 13px; text-align: center; margin-top: 24px;">
        {{ __('Thank you for choosing Tamman. We wish you a beneficial session!') }}
    </p>
@endsection