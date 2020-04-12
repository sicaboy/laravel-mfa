# Laravel Security Components

[![Latest Stable Version](https://poser.pugx.org/sicaboy/laravel-security/v/stable.svg)](https://packagist.org/packages/sicaboy/laravel-security)
[![License](https://poser.pugx.org/sicaboy/laravel-security/license.svg)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/sicaboy/laravel-security.svg?style=flat-square)](https://scrutinizer-ci.com/g/sicaboy/laravel-security)
[![Total Downloads](https://img.shields.io/packagist/dt/sicaboy/laravel-security.svg?style=flat-square)](https://packagist.org/packages/sicaboy/laravel-security)

## Introduction

This package can be used to enhance the user security of Laravel projects.

- **Verify the user provided password is not one of the top 10,000 worst passwords** as analyzed by a respectable IT security analyst. Read about all 
[ here](https://xato.net/10-000-top-passwords-6d6380716fe0#.473dkcjfm),
[here(wired)](http://www.wired.com/2013/12/web-semantics-the-ten-thousand-worst-passwords/) or
[here(telegram)](http://www.telegraph.co.uk/technology/internet-security/10303159/Most-common-and-hackable-passwords-on-the-internet.html)


## Installation

Requirements:
- [PHP](https://php.net) 5.5+ 
- [Composer](https://getcomposer.org)

To get the latest version of Laravel Security, simply run:

```
composer require sicaboy/laravel-security
```

Then do vendor publish:

```
php artisan vendor:publish --provider="Sicaboy\LaravelSecurity\LaravelSecurityServiceProvider"
```

- If you're on Laravel < 5.5, you'll need to register the service provider. Open up `config/app.php` and add the following to the `providers` array:

```php
Siaboy\LaravelSecurity\LaravelSecurityServiceProvider::class,
```


# Features

## Disallow user to use a common password or a used password

#### Available validators rules

- [NotCommonPassword](src/Rules/NotCommonPassword.php) - Avoid user to use a common used password

- [NotAUsedPassword](src/Rules/NotAUsedPassword.php) - Avoid user to use a password which has been used before

```php
// Add rule instance to the field validation rules list
public function rules()
{
    return [
        'password_field' => [
            'required',
            'confirmed',
            'min:8',
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
            //...
            new \Sicaboy\LaravelSecurity\Rules\NotCommonPassword(),
            new \Sicaboy\LaravelSecurity\Rules\NotAUsedPassword(),
            // or only check used password for a specific user (e.g. on user password change):
            // new \Sicaboy\LaravelSecurity\Rules\NotAUsedPassword($userId),
            // Also you need to call event, examples in the next section
        ],
    ];
}
```

#### Caution: extra event you need to call 

Login and reigster events are automatically traced.
While there is an extra event you should add to call explicitly. 

```php
// Call on user password change
event(new \Illuminate\Auth\Events\PasswordReset($user));
```

## Password Policies

#### Available policies

- Delete accounts with days of no activity
- Lock out accounts with days of no activity
- Force change password

1. Enable function needed by setting config `enabled` to `true` in `config/laravel-security.php`

```php
'password_policy' => [
    // Delete accounts with days of no activity
    'auto_delete_inactive_accounts' => [
        'enabled' => true,
        ...
    ],

    // Lock out accounts with days of no activity
    'auto_lockout_inactive_accounts' => [
        'enabled' => true,
        ...
    ],

    // Force change password every x days
    'force_change_password' => [
        'enabled' => true,
        ...
    ],
]
```

2. If you force user change password every x days, you will need to use this middleware

```php
Route::middleware(['security'])->group(function () {
    ...
});
```


3. Add the following commands to `app/Console/Kernel.php` of your application

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command(\Sicaboy\LaravelSecurity\Console\Commands\DeleteInactiveAccounts::class)
             ->hourly();
    $schedule->command(\Sicaboy\LaravelSecurity\Console\Commands\LockoutInactiveAccounts::class)
             ->hourly();
    ...
}
```
3. Make sure you add the [Laravel scheduler](https://laravel.com/docs/7.x/scheduling#introduction) in your crontab 

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```  

## Multi-factor Authentication

1. Enable function needed by setting config `enabled` to `true` in `config/laravel-security.php`

```php
'multi_factor_authentication' => [
    'enabled' => false,
    ...
]
```

2. Attach the middleware to your routes to protect your pages.

```php

Route::middleware(['mfa'])->group(function () {
    ...
});
```


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
