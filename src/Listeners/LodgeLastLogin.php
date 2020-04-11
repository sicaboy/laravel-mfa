<?php

namespace Sicaboy\LaravelSecurity\Listeners;

use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LodgeLastLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $modelClassName = config('laravel-security.database.user_security_model');
        return $modelClassName::updateOrCreate(
            [
                'user_id' => $event->user->id,
            ],
            [
                'last_loggein_at' => Carbon::now()
            ]
        );
    }
}
