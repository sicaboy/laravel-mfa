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
        \Illuminate\Auth\Events\Registered::class => [
            \Sicaboy\LaravelSecurity\Listeners\InsertUsedPassword::class,
            \Sicaboy\LaravelSecurity\Listeners\LodgeLastLogin::class,
            \Sicaboy\LaravelSecurity\Listeners\LodgeLastPasswordUpdated::class,
        ],
        \Illuminate\Auth\Events\Login::class => [
            \Sicaboy\LaravelSecurity\Listeners\LodgeLastLogin::class,
        ],
        \Illuminate\Auth\Events\PasswordReset::class => [
            \Sicaboy\LaravelSecurity\Listeners\InsertUsedPassword::class,
            \Sicaboy\LaravelSecurity\Listeners\LodgeLastLogin::class,
            \Sicaboy\LaravelSecurity\Listeners\LodgeLastPasswordUpdated::class,
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
