<?php

namespace Sicaboy\LaravelMFA\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use Sicaboy\LaravelMFA\LaravelMFAServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        
        // Add login route for tests
        \Illuminate\Support\Facades\Route::get('/login', function () {
            return 'Login Page';
        })->name('login');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelMFAServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        config()->set('mail.default', 'array');
        config()->set('cache.default', 'array');
        config()->set('session.driver', 'array');

        // Set up basic MFA config
        config()->set('laravel-mfa.default', [
            'enabled' => true,
            'template' => 'laravel-mfa::mfa.form',
            'code_expire_after_minutes' => 10,
            'login_route' => 'login',
            'auth_user_closure' => function () {
                return auth()->user();
            },
            'email' => [
                'queue' => false,
                'template' => 'laravel-mfa::emails.authentication-code',
                'subject' => 'Login authentication code',
            ],
        ]);
    }
}
