<?php

namespace Sicaboy\LaravelSecurity\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Sicaboy\LaravelSecurity\Events\UserRegistered::class => [
            \Sicaboy\LaravelSecurity\Listeners\InsertUsedPassword::class,
        ],
        \Sicaboy\LaravelSecurity\Events\UserLoggedIn::class => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
