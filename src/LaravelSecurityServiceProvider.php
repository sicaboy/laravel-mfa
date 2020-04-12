<?php

/*
 * This file is part of the Laravel Security package.
 *
 * (c) David Shen <slj@slj.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sicaboy\LaravelSecurity;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Sicaboy\LaravelSecurity\Providers\EventServiceProvider;
use Validator;

class LaravelSecurityServiceProvider extends ServiceProvider
{

    /**
     * Publishes all the config file this package needs to function.
     */
    public function boot()
    {
        $this->app->register(EventServiceProvider::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-security');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'laravel-security');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->registerRoutes();

        $this->registerPublishing();

    }


    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Sicaboy\LaravelSecurity\Console\Commands\DeleteInactiveAccounts::class,
                \Sicaboy\LaravelSecurity\Console\Commands\LockoutInactiveAccounts::class,
            ]);
            $this->publishes([
                __DIR__.'/../config' => config_path(),
//                __DIR__.'/../database/migrations' => database_path('migrations'),
                __DIR__.'/../resources/lang' => resource_path('lang'),
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-security'),
            ], 'laravel-security');
        }
    }


    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
//            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Get the Nova route group configuration array.
     *
     * @return array
     */
    protected function routeConfiguration()
    {
        return [
            'namespace' => 'Sicaboy\LaravelSecurity\Http\Controllers',
            'prefix' => 'security',
            'as' => 'security.',
            'middleware' => 'web',
        ];
    }

}
