# Laravel Multi-factor Authentication (MFA) / Two-factor Authentication (2FA)

[![Latest Stable Version](https://poser.pugx.org/sicaboy/laravel-mfa/v/stable.svg)](https://packagist.org/packages/sicaboy/laravel-mfa)
[![License](https://poser.pugx.org/sicaboy/laravel-mfa/license.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/sicaboy/laravel-mfa.svg?style=flat-square)](https://packagist.org/packages/sicaboy/laravel-mfa)

## Introduction

This package was a part of [sicaboy/laravel-security](https://github.com/sicaboy/laravel-security). Later moved to this separated repository.

This package provides a Middleware to protect pages with MFA in your Laravel projects.


## Installation

Requirements:
- [PHP](https://php.net) 5.5+ 
- [Composer](https://getcomposer.org)

To get the latest version of Laravel MFA, simply run:

```
composer require sicaboy/laravel-mfa
```

Then do vendor publish:

```
php artisan vendor:publish --provider="Sicaboy\LaravelMFA\LaravelMFAServiceProvider"
```

After publish, you are able to modify templates and config in:

```
app/config/laravel-mfa.php
resources/views/vendor/laravel-mfa/
```

If you're on Laravel < 5.5, you'll need to register the service provider. Open up `config/app.php` and add the following to the `providers` array:

```php
Siaboy\LaravelMFA\LaravelMFAServiceProvider::class,
```

# Usage

1. Enable function needed by setting config `enabled` to `true` in `config/laravel-mfa.php`

```php
'multi_factor_authentication' => [
    'enabled' => true,
    ...
]
```

2. Attach the middleware to your routes to protect your pages.

```php
Route::middleware(['mfa'])->group(function () {
    ...
});
```

## TODO

- Queue email sending

- Switch on MFA on specific users (db field based)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
