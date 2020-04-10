<?php


namespace Sicaboy\LaravelSecurity\Handlers;

use Hash;

class NotUsedPasswordHandler
{
    public static function lodgePassword($userId, $password)
    {
        $modelClassName = config('laravel-security.database.password_history_model');
        return $modelClassName::create([
            'user_id' => $userId,
            'password' => bcrypt($password)
        ]);
    }
}
