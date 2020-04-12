<?php

namespace Sicaboy\LaravelSecurity\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Security
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        if (config('laravel-security.multi_factor_authentication.enabled') !== true) {

        }
    }
}
