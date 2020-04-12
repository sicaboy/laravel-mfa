<?php

return [

    'multi_factor_authentication' => [
        'enabled' => false,
        'template' => 'laravel-security.mfa.form',
        'code_expire_after_minutes' => 10,
        'email' => [
            //'mailable' => Sicaboy\LaravelSecurity\Mail\AuthenticationCodeMailable::class,
            'template' => 'laravel-security::emails.authentication-code',
            'subject' => 'Login authentication code',
        ],
        // @todo
        // 'sms' => []
    ],

    'password_policy' => [
        // Delete accounts with days of no activity
        'auto_delete_inactive_accounts' => [
            'enabled' => false,
            'days_after_last_login' => 365,
            'email_notification' => [
                'enabled' => true,
                //'mailable' => Sicaboy\LaravelSecurity\Mail\AccountTerminatedMailable::class,
                'template' => 'laravel-security::emails.account-terminated',
                'subject' => 'Your account has been terminated',
            ],
        ],

        // Lock out accounts with days of no activity
        'auto_lockout_inactive_accounts' => [
            'enabled' => false,
            'days_after_last_login' => 90,
            'email_notification' => [
                'enabled' => true,
                //'mailable' => Sicaboy\LaravelSecurity\Mail\AccountLockedMailable::class,
                'template' => 'laravel-security::emails.account-locked',
                'subject' => 'Your account has been locked due to no activity',
            ],
        ],

        // Force change password every x days
        'force_change_password' => [
            'enabled' => false,
            'days_after_last_change' => 90,
            'change_password_url' => '/your_path_to/user/change-password',
        ],
    ],

    'database' => [
        'connection' => '', // Database connection for running database migration.
        'user_security_table' => 'user_extend_security',
        'user_security_model' => Sicaboy\LaravelSecurity\Model\UserExtendSecurity::class,
        'password_history_table' => 'password_history',
        'password_history_model' => Sicaboy\LaravelSecurity\Model\PasswordHistory::class,
        'user_model' => 'App\User',
    ],
];
