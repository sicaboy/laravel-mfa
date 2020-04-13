<?php

namespace Sicaboy\LaravelMFA\Http\Middleware;

use Closure;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MFA
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
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!Session::has('mfa_completed')) {
            return redirect()->route('mfa.mfa', [
                'referer' => $this->generator->previous()
            ]);
        }
        return $next($request);
    }
}
