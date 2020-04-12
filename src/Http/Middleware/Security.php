<?php

namespace Sicaboy\LaravelSecurity\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Security
{

    protected $generator;

    public function __construct(UrlGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('laravel-security.password_policy.force_change_password.enabled') !== true) {
            return $next($request);
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (Session::has('force_change_password_completed')) {
            return $next($request);
        }

        $days = config('laravel-security.password_policy.force_change_password.days_after_last_change', 90);

        $modelClassName = config('laravel-security.database.user_security_model');
        $passwordRecentlyUpdated = $modelClassName::where('user_id', $user->id)
            ->whereDate('last_password_updated_at', '>', Carbon::now()->subDays($days))
            ->exists();

        if ($passwordRecentlyUpdated) {
            Session::put('force_change_password_completed', true);
            return $next($request);
        }

        $url = config('laravel-security.password_policy.force_change_password.change_password_url');

        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= 'referer=' . urlencode($this->generator->previous());

        return redirect()->to($url);
    }
}
