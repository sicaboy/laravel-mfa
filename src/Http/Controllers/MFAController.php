<?php

namespace Sicaboy\LaravelMFA\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;
use Sicaboy\LaravelMFA\Mail\SendMFAMail;

/**
 * Class MFAController
 * @package Sicaboy\LaravelMFA\Http\Controllers
 */
class MFAController extends Controller
{

    public const MFA_CODE_KEY = 'mfa_code';

    /**
     * @var Authenticatable|null
     */
    protected $authUser;

    /**
     * @var MFAHelper
     */
    protected $helper;

    /**
     * @var string|null
     */
    protected $configGroup;

    public function __construct(MFAHelper $helper, Request $request)
    {
        $this->helper = $helper;
        $this->configGroup = $request->get('group');
    }

    /**
     * @return Authenticatable|mixed|null
     */
    protected function getAuthUser()
    {
        if ($this->authUser) {
            return $this->authUser;
        }
        $this->authUser = $this->helper->getUserModel($this->configGroup);
        return $this->authUser;
    }

    /**
     * @return string
     */
    protected function getCodeCacheKey()
    {
        return self::MFA_CODE_KEY . '-' . $this->configGroup . '-' . $this->getAuthUser()->id;
    }

    /**
     * @return false|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function userNotLoggedInRedirect()
    {
        if (!$this->getAuthUser()) {
            // No Auth::user returned. Not login yet
            return request()->wantsJson()
                ? response()->json([
                    'error' => 'Login required',
                    'url' => $this->helper->getConfigByGroup('login_route', $this->configGroup, 'login')
                ], 403)
                : redirect()->route(
                    $this->helper->getConfigByGroup('login_route', $this->configGroup, 'login')
                );
        }

        if ($this->helper->isVerificationCompleted($this->configGroup)) {
            $this->redirectToVerifiedRoute();
        }
        return false;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToVerifiedRoute()
    {
        $nextRoute = $this->helper->getConfigByGroup('verified_route', $this->configGroup);
        if ($nextRoute) {
            return redirect()->route($nextRoute);
        }
        return redirect()->to(config('app.url', '/'));
    }

    /**
     * @param  Request  $request
     * @return false|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getIndex(Request $request)
    {
        if ($redirect = $this->userNotLoggedInRedirect()) {
            return $redirect;
        }

        $minutes = $this->helper->getConfigByGroup('code_expire_after_minutes', $this->configGroup, 10);
        $code = $this->helper->refreshVerificationCode($this->getCodeCacheKey(), $minutes);

        $emailTemplate = $this->helper->getConfigByGroup('email.template', $this->configGroup);
        $emailVars = [
            'user' => $this->getAuthUser(),
            'code' => $code,
            'minutes' => $minutes,
        ];

        $mailer = Mail::to($this->getAuthUser()->email);
        $mailable = new SendMFAMail(
            $emailTemplate,
            $emailVars,
            $this->helper->getConfigByGroup('email.subject', $this->configGroup)
        );
        if ($this->helper->getConfigByGroup('email.queue', $this->configGroup)) {
            $mailer->queue($mailable);
        } else {
            $mailer->send($mailable);
        }

        return redirect()->route('mfa.form', [
            'group' => $this->configGroup,
        ]);
    }

    /**
     * @param  Request  $request
     * @return false|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getForm(Request $request)
    {
        if ($redirect = $this->userNotLoggedInRedirect()) {
            return $redirect;
        }

        $minutes = $this->helper->getConfigByGroup('code_expire_after_minutes', $this->configGroup, 10);

        return view('laravel-mfa::mfa.form', [
            'group' => $this->configGroup,
            'minutes' => $minutes,
        ]);
    }

    /**
     * @param  Request  $request
     * @return false|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function postForm(Request $request)
    {
        if ($redirect = $this->userNotLoggedInRedirect()) {
            return $redirect;
        }

        $code = Cache::get($this->getCodeCacheKey());

        if (!$code || trim($request->get('code')) != $code) {
            return redirect()->back()->withErrors([
                'code' => __('The code is not correct.')
            ]);
        }
        $this->helper->setVerificationCompleted($this->configGroup);

        // Redirect to a group specific URL, otherwise redirect to app.url
        return $this->redirectToVerifiedRoute();
    }
}
