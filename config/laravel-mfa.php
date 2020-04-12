<?php

return [

    'multi_factor_authentication' => [
        'enabled' => false,
        'template' => 'laravel-mfa::mfa.form',
        'code_expire_after_minutes' => 10,
        'email' => [
            //'mailable' => Sicaboy\LaravelMFA\Mail\AuthenticationCodeMailable::class,
            'template' => 'laravel-mfa::emails.authentication-code',
            'subject' => 'Login authentication code',
        ],
        // @todo
        // 'sms' => []
    ],
];
