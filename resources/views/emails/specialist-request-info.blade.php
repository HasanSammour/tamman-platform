@extends('emails.layouts.email')

@section('content')
    <h2 style="color: #f59e0b; text-align: center;">{{ __('Additional Information Required') }}</h2>

    <p>{{ __('Dear') }} <strong>{{ $specialist->name }}</strong>,</p>

    <p>{{ __('We are reviewing your application to become a specialist on :app_name.', ['app_name' => config('app.name')]) }}
    </p>

    <p>{{ __('We need some additional information to continue with the review process:') }}</p>

    <div class="info-box" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
        <h3 style="color: #92400e;">{{ __('Request Details') }}:</h3>
        <p style="white-space: pre-line;">{{ $messageContent }}</p>
    </div>

    <div class="info-box" style="background: #ede9fe; border-left: 4px solid #7c3aed;">
        <h3 style="color: #4c1d95;">{{ __('How to respond') }}</h3>
        <ul>
            <li>{{ __('Reply directly to this email with the requested information') }}</li>
            <li>{{ __('Upload the required documents to your application') }}</li>
            <li>{{ __('Contact our support team if you need clarification') }}</li>
        </ul>
    </div>

    <div style="background: #f3f4f6; border-radius: 12px; padding: 15px; text-align: center;">
        <p style="margin: 0;">{{ __('If you have any questions, please contact us at') }} <a href="mailto:support@tamman.ps"
                style="color: #7c3aed;">support@tamman.ps</a></p>
    </div>

    <div class="divider"></div>

    <p>{{ __('Thank you for your cooperation.') }}</p>
    <p style="margin-top: 20px;">
        {{ __('Best regards,') }}<br>
        <strong>{{ __('Tamman Team') }}</strong>
    </p>
@endsection