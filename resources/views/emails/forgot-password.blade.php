@component('mail::message')
  # Hello

  Please click the button below to reset your password.

  @component('mail::button', ['url' => $url])
    Reset Password
  @endcomponent

  Thanks,<br>
  {{ config('app.name') }}

  @component('mail::subcopy')
    If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
    <a href="{{ $url }}">{{ $url }}</a>
  @endcomponent
@endcomponent
