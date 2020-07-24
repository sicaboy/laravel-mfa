<?php

namespace Sicaboy\LaravelMFA\Http\Middleware;

use Closure;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;

class MFA
{

    protected $generator;
    protected $helper;

    public function __construct(UrlGenerator $generator, MFAHelper $helper)
    {
        $this->generator = $generator;
        $this->helper = $helper;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $group
     * @return mixed
     */
    public function handle($request, Closure $next, $group = 'default')
    {
        if ($this->helper->getConfigByGroup('enabled', $group) == false) {
            return $next($request);
        }
        if (!$this->helper->getUserModel($group)) {
            // No Auth::user returned. Not login yet
            return $request->wantsJson()
                ? response()->json([
                    'error' => 'Login required',
                    'url' => $this->helper->getConfigByGroup('login_route', $group, 'login')
                ], 403)
                : redirect()->route(
                    $this->helper->getConfigByGroup('login_route', $group, 'login')
                );
        }

        if (!$this->helper->isVerificationCompleted($group)) {
            // User hasn't completed MFA verification
            return $request->wantsJson()
                ? response()->json([
                    'error' => 'MFA Required',
                    'url' => route('mfa.mfa', [
                        'group' => $group,
                        'referer' => $this->generator->previous()
                    ])
                ], 423)
                : redirect()->route('mfa.mfa', [
                    'group' => $group,
                    'referer' => $this->generator->previous()
                ]);
        }

        return $next($request);
    }

}
