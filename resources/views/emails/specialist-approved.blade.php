@extends('emails.layouts.email')

@section('content')
<h2 style="color: #7c3aed; text-align: center;">{{ __('Congratulations! 🎉') }}</h2>

<p>{{ __('Dear') }} <strong style="color: #7c3aed;">{{ $specialist->name }}</strong>,</p>

<p>{{ __('We are pleased to inform you that your application to become a specialist on :app_name has been approved!', ['app_name' => config('app.name')]) }}</p>

<div class="info-box" style="background: #f0fdf4; border-left: 4px solid #10b981;">
    <h3 style="color: #065f46;">✅ {{ __('Your account has been activated') }}</h3>
    <p><strong>{{ __('Email') }}:</strong> {{ $specialist->email }}</p>
    <p><strong>{{ __('Login URL') }}:</strong> <a href="{{ route('login') }}" style="color: #7c3aed;">{{ route('login') }}</a></p>
</div>

<div class="info-box" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
    <h3 style="color: #92400e;">📋 {{ __('Next Steps') }}</h3>
    <ol>
        <li>{{ __('Log in to your account') }}</li>
        <li>{{ __('Complete your profile information') }}</li>
        <li>{{ __('Set your availability schedule') }}</li>
        <li>{{ __('Start accepting session requests') }}</li>
    </ol>
</div>

<div class="info-box" style="background: #ede9fe; border-left: 4px solid #7c3aed;">
    <h3 style="color: #4c1d95;">💡 {{ __('Tips for Success') }}</h3>
    <ul>
        <li>{{ __('Keep your availability updated regularly') }}</li>
        <li>{{ __('Respond to patient messages within 24 hours') }}</li>
        <li>{{ __('Complete session notes after each session') }}</li>
        <li>{{ __('Encourage patients to leave reviews') }}</li>
    </ul>
</div>

<div style="background: #f3f4f6; border-radius: 12px; padding: 15px; text-align: center;">
    <p style="margin: 0;">{{ __('If you have any questions, please contact us at') }} <a href="mailto:support@tamman.ps" style="color: #7c3aed;">support@tamman.ps</a></p>
</div>

<div class="divider"></div>

<p>{{ __('Welcome to the team!') }}</p>
<p style="margin-top: 20px;">
    {{ __('Best regards,') }}<br>
    <strong>{{ __('Tamman Team') }}</strong>
</p>
@endsection