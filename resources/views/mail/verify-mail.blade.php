<x-mail::message>
# Introduction

Please refer to the following link:

<x-mail::button :url="{{ route( 'register.verify', ['token' => $user->verify_token] ) }}">
Verify Email
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
