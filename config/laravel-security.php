<?php

return [
    'database' => [
        'connection' => '',
        'password_history_table' => 'password_history',
        'password_history_model' => Sicaboy\LaravelSecurity\Model\PasswordHistory::class,
        'user_model' => 'App\User',
    ],
];
