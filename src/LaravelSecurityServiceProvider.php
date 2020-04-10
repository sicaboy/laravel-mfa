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

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Validator;

class LaravelSecurityServiceProvider extends ServiceProvider
{

    /**
     * Publishes all the config file this package needs to function.
     */
    public function boot()
    {
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
                __DIR__.'/../config' => config_path(),
                __DIR__.'/../database/migrations' => database_path('migrations'),
                __DIR__.'/../resources/lang' => resource_path('lang'),
            ], 'laravel-security');

            $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'security');
        }
    }

}
