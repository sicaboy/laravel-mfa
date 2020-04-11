<?php

namespace Sicaboy\LaravelSecurity\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InsertUsedPassword
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
        $userId = $event->user->id;
        $password = $event->plainPassword;
        $modelClassName = config('laravel-security.database.password_history_model');
        return $modelClassName::create([
            'user_id' => $userId,
            'password' => bcrypt($password)
        ]);
    }
}
