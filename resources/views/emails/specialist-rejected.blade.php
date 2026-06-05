@extends('emails.layouts.email')

@section('content')
<h2 style="color: #ef4444; text-align: center;">{{ __('Application Update') }}</h2>

<p>{{ __('Dear') }} <strong>{{ $specialist->name }}</strong>,</p>

<p>{{ __('Thank you for your interest in becoming a specialist on :app_name.', ['app_name' => config('app.name')]) }}</p>

<p>{{ __('After careful review, we regret to inform you that your application has not been approved at this time.') }}</p>

@if($reason)
<div class="info-box" style="background: #fee2e2; border-left: 4px solid #ef4444;">
    <h3 style="color: #991b1b;">{{ __('Reason') }}:</h3>
    <p>{{ $reason }}</p>
</div>
@endif

<div class="info-box" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
    <h3 style="color: #92400e;">{{ __('What can you do?') }}</h3>
    <ul>
        <li>{{ __('Review the requirements and reapply') }}</li>
        <li>{{ __('Contact our support team for more details') }}</li>
        <li>{{ __('Update your documents and qualifications') }}</li>
    </ul>
</div>

<div style="background: #f3f4f6; border-radius: 12px; padding: 15px; text-align: center;">
    <p style="margin: 0;">{{ __('If you have any questions, please contact us at') }} <a href="mailto:support@tamman.ps" style="color: #7c3aed;">support@tamman.ps</a></p>
</div>

<div class="divider"></div>

<p>{{ __('Thank you for your understanding.') }}</p>
<p style="margin-top: 20px;">
    {{ __('Best regards,') }}<br>
    <strong>{{ __('Tamman Team') }}</strong>
</p>
@endsection