<?php

return [
    'template' => 'laravel-mfa::mfa.form',
    'code_expire_after_minutes' => 10,
    'login_route' => 'login', // Route name
    'auth_user_closure' => function() {
        return \Illuminate\Support\Facades\Auth::user();
    },
    'email' => [
        //'mailable' => Sicaboy\LaravelMFA\Mail\AuthenticationCodeMailable::class,
        'template' => 'laravel-mfa::emails.authentication-code',
        'subject' => 'Login authentication code',
    ],
    // @todo
    // 'sms' => []
];
