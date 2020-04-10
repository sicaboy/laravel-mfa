# Laravel Security Components

[![Latest Stable Version](https://poser.pugx.org/sicaboy/laravel-security/v/stable.svg)](https://packagist.org/packages/sicaboy/laravel-security)
[![License](https://poser.pugx.org/sicaboy/laravel-security/license.svg)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/sicaboy/laravel-security.svg?style=flat-square)](https://scrutinizer-ci.com/g/sicaboy/laravel-security)
[![Total Downloads](https://img.shields.io/packagist/dt/sicaboy/laravel-security.svg?style=flat-square)](https://packagist.org/packages/sicaboy/laravel-security)

### Introduction

This package can be used to enhance the user security of Laravel projects.

- **Verify the user provided password is not one of the top 10,000 worst passwords** as analyzed by a respectable IT security analyst. Read about all 
[ here](https://xato.net/10-000-top-passwords-6d6380716fe0#.473dkcjfm),
[here(wired)](http://www.wired.com/2013/12/web-semantics-the-ten-thousand-worst-passwords/) or
[here(telegram)](http://www.telegraph.co.uk/technology/internet-security/10303159/Most-common-and-hackable-passwords-on-the-internet.html)


## Installation

[PHP](https://php.net) 5.5+ or [HHVM](http://hhvm.com) 3.3+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Security, simply add the following line to the require block of your `composer.json` file.

```
"sicaboy/laravel-security": "1.0.*"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

- If you're on Laravel 5.5 or above, that's all you need to do! Check out the usage examples below.
- If you're on Laravel < 5.5, you'll need to register the service provider. Open up `config/app.php` and add the following to the `providers` array:

```php
Siaboy\LaravelSecurity\LaravelSecurityServiceProvider::class
```

## Available Rules

- [NotCommonPassword](src/RulesNotCommonPassword.php)
- 

## Usage

```php

// In a `FormRequest`
// Add new NotCommonPassword() to the rule list

use Sicaboy\LaravelSecurity\Rules\NotCommonPassword;

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
            new NotCommonPassword(),
        ],
    ];
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
