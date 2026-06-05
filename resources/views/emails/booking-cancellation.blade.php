@extends('emails.layouts.email')

@section('content')
    <h2 style="color: #1f2937; text-align: center; margin-bottom: 16px;">{{ __('Session Cancelled') }}</h2>
    <p style="color: #6b7280; text-align: center; margin-bottom: 24px;">
        {{ __('Your session has been cancelled successfully') }}</p>

    <p style="color: #4b5563; margin-bottom: 20px;">
        {{ __('Dear') }} <strong>{{ $user->name }}</strong>,
    </p>

    <p style="color: #4b5563; margin-bottom: 20px;">
        {{ __('Your session has been cancelled. Here are the details of the cancelled session:') }}
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
                <td class="info-value">{{ \Carbon\Carbon::parse($session->session_datetime)->format('h:i A') }}</td>
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
        </table>
    </div>

    @if($refundAmount > 0)
        <div class="refund-box">
            <div class="refund-title">💰 {{ __('Refund Processed') }}</div>
            <p style="color: #065f46; margin: 0;">
                {{ __('An amount of') }} <strong>${{ number_format($refundAmount, 2) }}</strong>
                {{ __('has been credited back to your account balance.') }}
            </p>
        </div>
    @endif

    <div class="tips-box">
        <div class="tips-title">
            ℹ️ {{ __('What would you like to do next?') }}
        </div>
        <p style="color: #92400e; margin-bottom: 16px;">
            {{ __('You can book another session with the same specialist or browse other specialists.') }}</p>
    </div>

    <div class="btn-group">
        <a href="{{ route('patient.book', $session->specialist_id) }}" class="btn-primary">{{ __('Book Again') }}</a>
        <a href="{{ route('specialists.index') }}" class="btn-secondary">{{ __('Find Other Specialists') }}</a>
    </div>

    <p style="color: #6b7280; font-size: 13px; text-align: center; margin-top: 24px;">
        {{ __('We look forward to serving you in future sessions.') }}
    </p>
@endsection