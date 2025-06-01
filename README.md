# Laravel Multi-factor Authentication (MFA) / Two-factor Authentication (2FA)

[![Latest Stable Version](https://poser.pugx.org/sicaboy/laravel-mfa/v/stable.svg)](https://packagist.org/packages/sicaboy/laravel-mfa)
[![Total Downloads](https://img.shields.io/packagist/dt/sicaboy/laravel-mfa.svg?style=flat-square)](https://packagist.org/packages/sicaboy/laravel-mfa)
[![License](https://poser.pugx.org/sicaboy/laravel-mfa/license.svg)](LICENSE.md)
[![Tests](https://github.com/sicaboy/laravel-mfa/workflows/Tests/badge.svg)](https://github.com/sicaboy/laravel-mfa/actions)
[![PHP Version Require](http://poser.pugx.org/sicaboy/laravel-mfa/require/php)](https://packagist.org/packages/sicaboy/laravel-mfa)
[![Packagist](https://img.shields.io/packagist/v/sicaboy/laravel-mfa.svg)](https://packagist.org/packages/sicaboy/laravel-mfa)
[![GitHub issues](https://img.shields.io/github/issues/sicaboy/laravel-mfa.svg)](https://github.com/sicaboy/laravel-mfa/issues)
[![GitHub stars](https://img.shields.io/github/stars/sicaboy/laravel-mfa.svg)](https://github.com/sicaboy/laravel-mfa/stargazers)

## Introduction

A powerful and flexible Laravel package that provides Multi-factor Authentication (MFA) / Two-factor Authentication (2FA) middleware to secure your Laravel applications. This package was originally part of [sicaboy/laravel-security](https://github.com/sicaboy/laravel-security) and has been moved to this dedicated repository.

## Features

- ✅ **Easy Integration** - Simple middleware-based implementation
- ✅ **Email-based MFA** - Secure code delivery via email
- ✅ **Multiple Auth Guards** - Support for different authentication contexts (user, admin, etc.)
- ✅ **Configurable** - Flexible configuration options
- ✅ **Queue Support** - Background email sending with Laravel queues
- ✅ **Cache-based** - Efficient code storage and verification tracking
- ✅ **Customizable Views** - Override templates to match your design
- ✅ **Laravel 5.7+ Support** - Compatible with modern Laravel versions

---

## 🚀 Don't Want to Build Authentication From Scratch?

**Save weeks of development time with [Users.au](https://www.users.au) - a complete authentication solution for Laravel!**

![Users.au MFA Screenshot](https://www.users.au/screenshots/mfa.png)

### Why Choose Users.au?

- 🎯 **Ready-to-use Authentication** - Complete user management system
- 🔐 **Built-in MFA/2FA** - No need for additional packages
- ⚡ **Laravel Integration** - Seamless setup with your existing Laravel app
- 🆓 **Free to Start** - Get started without any upfront costs
- 🛠️ **Developer-friendly** - Multiple integration options

### Get Started in Minutes:

#### Option 1: Laravel Starter Kit (Fastest)
```bash
git clone https://github.com/Users-au/laravel-starter-kit.git
cd laravel-starter-kit
composer install
```

#### Option 2: Add to Existing Laravel App
```bash
composer require users-au/laravel-client
```

#### Option 3: Socialite Integration
```bash
composer require users-au/socialite-provider
```

### Resources:
- 🌐 **Website**: [https://www.users.au](https://www.users.au)
- 📦 **Laravel Starter Kit**: [https://github.com/Users-au/laravel-starter-kit](https://github.com/Users-au/laravel-starter-kit)
- 🔧 **Laravel Package**: [https://github.com/Users-au/laravel-client](https://github.com/Users-au/laravel-client)
- 🔑 **Socialite Provider**: [https://github.com/Users-au/socialite-provider](https://github.com/Users-au/socialite-provider)

*Skip the complexity of building authentication from scratch and focus on what makes your app unique!*

---

## Installation

### Requirements
- PHP 7.1+ or 8.0+
- Laravel 5.7+
- [Composer](https://getcomposer.org)

### Install via Composer

```bash
composer require sicaboy/laravel-mfa
```

### Publish Configuration and Views

```bash
php artisan vendor:publish --provider="Sicaboy\LaravelMFA\LaravelMFAServiceProvider"
```

This will publish:
- Configuration file: `config/laravel-mfa.php`
- View templates: `resources/views/vendor/laravel-mfa/`

### Service Provider Registration (Laravel < 5.5)

If you're using Laravel < 5.5, manually register the service provider in `config/app.php`:

```php
'providers' => [
    // ...
    Sicaboy\LaravelMFA\LaravelMFAServiceProvider::class,
],
```

# Usage

## Basic Usage

Protect your routes by applying the `mfa` middleware:

```php
// Protect individual routes
Route::get('/dashboard', 'DashboardController@index')->middleware('mfa');

// Protect route groups
Route::middleware(['mfa'])->group(function () {
    Route::get('/admin', 'AdminController@index');
    Route::get('/profile', 'ProfileController@show');
});
```

## Multiple Authentication Guards

If you use multiple authentication guards (e.g., separate user and admin authentication), specify the guard group:

```php
// For admin routes
Route::middleware(['mfa:admin'])->group(function () {
    Route::get('/admin/dashboard', 'Admin\DashboardController@index');
});
```

Configure the corresponding group in `config/laravel-mfa.php`:

```php
return [
    'default' => [
        // Default configuration...
    ],
    'group' => [
        'admin' => [ // Example, when using middleware 'mfa:admin'. Attributes not mentioned will be inherit from `default` above
            'login_route' => 'admin.login',
            'auth_user_closure' => function() {
                return \Encore\Admin\Facades\Admin::user();
            },
        ],
        'other_name' => [ // Middleware 'mfa:other_name'
            ...
        ]
    ],
];
```

## Configuration Options

### Email Configuration

Configure email settings in `config/laravel-mfa.php`:

```php
'email' => [
    'queue' => true, // Enable queue for background sending
    'template' => 'laravel-mfa::emails.authentication-code',
    'subject' => 'Your Authentication Code',
],
```

### Code Expiration

Set how long verification codes remain valid:

```php
'code_expire_after_minutes' => 10, // Default: 10 minutes
```

## Queue Configuration

For applications with queue workers running, enable background email sending:

```php
return [
    'default' => [
        'email' => [
            'queue' => true, // Enable queue processing
        ]
    ]
];
```

Make sure your queue worker is running:

```bash
php artisan queue:work
```

## API Responses

The middleware provides JSON responses for API requests:

- **403** - User not authenticated
- **423** - MFA verification required

```json
{
    "error": "MFA Required",
    "url": "/mfa/generate?group=default"
}
```

## Testing

Run the test suite:

```bash
composer test
```

Or run PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## Security Considerations

- Codes expire after the configured time limit (default: 10 minutes)
- Verification status is cached to prevent replay attacks
- Email delivery can be queued for better performance
- Multiple authentication contexts are supported

## Roadmap

- ✅ Email-based MFA
- 🔄 SMS-based MFA
- 🔄 TOTP/Authenticator app support
- 🔄 User-specific MFA settings
- 🔄 Backup codes

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

1. Clone the repository:
```bash
git clone https://github.com/sicaboy/laravel-mfa.git
cd laravel-mfa
```

2. Install dependencies:
```bash
composer install
```

3. Run tests:
```bash
composer test
```

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
./vendor/bin/phpunit --coverage-html build/coverage

# Run specific test file
./vendor/bin/phpunit tests/Unit/MFAHelperTest.php

# Run specific test method
./vendor/bin/phpunit --filter testGetConfigByGroupReturnsGroupConfig
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support

- **Issues**: [GitHub Issues](https://github.com/sicaboy/laravel-mfa/issues)
- **Documentation**: This README and inline code documentation
- **Email**: [slj@slj.me](mailto:slj@slj.me)

## Credits

- [David Shen](https://github.com/sicaboy)
- [All Contributors](../../contributors)
