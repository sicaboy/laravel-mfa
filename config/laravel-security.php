<?php

return [

    'multi_factor_authentication' => [
        'enabled' => false,
        'email_notification' => [
            'enabled' => true,
            'mailable' => Sicaboy\LaravelSecurity\Mail\AuthenticationCodeMailable::class,
            'template' => 'laravel-security.emails.authentication-code',
            'subject' => 'Login authentication code',
        ],
    ],

    'password_policy' => [
        // Delete accounts with days of no activity
        'auto_delete_account' => [
            'enabled' => false,
            'days_after_last_login' => 365,
            'email_notification' => [
                'enabled' => true,
                'mailable' => Sicaboy\LaravelSecurity\Mail\AccountTerminatedMailable::class,
                'template' => 'laravel-security.emails.account-terminated',
                'subject' => 'Your account has been terminated',
            ],
        ],

        // Lock out accounts with days of no activity
        'auto_lock_account' => [
            'enabled' => false,
            'days_after_last_login' => 90,
            'email_notification' => [
                'enabled' => true,
                'mailable' => Sicaboy\LaravelSecurity\Mail\AccountLockedMailable::class,
                'template' => 'laravel-security.emails.account-locked',
                'subject' => 'Your account has been locked due to no activity',
            ],
        ],

        // Force change password every x days
        'force_change_password' => [
            'enabled' => false,
            'days_after_last_change' => 90,
        ],
    ],

    'database' => [
        'connection' => '',
        'user_security_table' => 'user_security',
        'password_history_table' => 'password_history',
        'password_history_model' => Sicaboy\LaravelSecurity\Model\PasswordHistory::class,
        'user_model' => 'App\User',
    ],
];
