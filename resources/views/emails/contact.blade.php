@extends('emails.layouts.email')

@section('content')
    <h2 style="color: #1f2937; text-align: center; margin-bottom: 16px;">{{ __('New Contact Message') }}</h2>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">{{ __('Name') }}: </td>
                <td class="info-value">{{ $data['name'] }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Email') }}: </td>
                <td class="info-value">{{ $data['email'] }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('Subject') }}: </td>
                <td class="info-value">{{ $data['subject'] }}</td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <p><strong>{{ __('Message') }}:</strong></p>
        <p style="color: #4b5563; line-height: 1.6;">{{ $data['message'] }}</p>
    </div>

    <p style="color: #6b7280; font-size: 13px; text-align: center; margin-top: 20px;">
        {{ __('This message was sent from the contact form on the website.') }}
    </p>
@endsection