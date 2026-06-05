@extends('emails.layouts.email')

@section('content')
<h2>{{ __('Dear') }} {{ $specialist->name }},</h2>

<div style="white-space: pre-line;">
    {!! nl2br(e($messageContent)) !!}
</div>

<div class="divider"></div>

<p>{{ __('Best regards,') }}<br>
<strong>{{ __('Tamman Team') }}</strong></p>
@endsection