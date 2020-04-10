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
    /*
    * Indicates if loading of the provider is deferred.
    *
    * @var bool
    */
    protected $defer = false;

    /**
     * Default error message.
     *
     * @var string
     */
    protected $message = [
        'not_common_password' => 'This password is too common used. Please try another.',
        'not_used_password' => 'This password has been used before. Please try another.',
    ];

    /**
     * Publishes all the config file this package needs to function.
     */
    public function boot()
    {

        $this->registerPublishing();

        Validator::extend('not_common_password', function ($attribute, $value, $parameters, $validator) {
            $path = realpath(__DIR__ . '/../resources/config/not_common_password_list.txt');
            $cache_key = md5_file($path);
            $data = Cache::rememberForever('not_common_password_list_' . $cache_key, function () use ($path) {
                return collect(explode("\n", file_get_contents($path)));
            });
            return !$data->contains($value);
        }, $this->message['not_common_password']);

        Validator::extend('not_used_password', function ($attribute, $value, $parameters, $validator) {

//            return !$data->contains($value);
        }, $this->message['not_used_password']);

    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-security'];
    }


    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'laravel-security');
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'laravel-security');
        }
    }

    /**
     * Register paths to be published by the publish command.
     *
     * @param  array  $paths
     * @param  string  $group
     * @return void
     */
    protected function publishes(array $paths, $group = null)
    {
        $this->ensurePublishArrayInitialized($class = static::class);

        static::$publishes[$class] = array_merge(static::$publishes[$class], $paths);

        if ($group) {
            $this->addPublishGroup($group, $paths);
        }
    }

}
