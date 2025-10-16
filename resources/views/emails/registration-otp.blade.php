@component('mail::message')
# {{ __('Your verification code') }}

{{ __('Use the following one-time password to complete your K-Wave registration:') }}

@component('mail::panel')
**{{ $code }}**
@endcomponent

{{ __('This code will expire in 10 minutes. If you did not request it, you can safely ignore this email.') }}

{{ __('See you on K-Wave!') }}<br>
{{ config('app.name') }}
@endcomponent
