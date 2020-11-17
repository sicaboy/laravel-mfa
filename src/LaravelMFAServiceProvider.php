<?php

/*
 * This file is part of the Laravel Security package.
 *
 * (c) David Shen <slj@slj.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sicaboy\LaravelMFA;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Validator;

/**
 * Class LaravelMFAServiceProvider
 * @package Sicaboy\LaravelMFA
 */
class LaravelMFAServiceProvider extends ServiceProvider
{

    /**
     * Publishes all the config file this package needs to function.
     */
    public function boot()
    {
        $this->app->register(\Sicaboy\LaravelMFA\Providers\EventServiceProvider::class);
        $this->app['router']->aliasMiddleware('mfa', \Sicaboy\LaravelMFA\Http\Middleware\MFA::class);
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-mfa');
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
            $this->publishes([
                __DIR__ . '/../config' => config_path(),
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-mfa'),
            ], 'laravel-mfa');
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
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
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
            'namespace' => 'Sicaboy\LaravelMFA\Http\Controllers',
            'prefix' => 'mfa',
            'as' => 'mfa.',
            'middleware' => 'web',
        ];
    }
}
