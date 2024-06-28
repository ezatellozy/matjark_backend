@component('mail::message')

<h1 style="text-align: left !important">
    We have received your request to verify your email
</h1>

<p style="text-align: left !important">
    You can use the following code to verify your account:
</p>

@component('mail::panel')
{{ $code }}
@endcomponent

<p style="text-align: left !important">
    The allowed duration of the code is one hour from the time the message was sent
</p>

@endcomponent
