<?php

namespace Sicaboy\LaravelMFA\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Sicaboy\LaravelMFA\Helpers\MFAHelper;
use Sicaboy\LaravelMFA\Mail\SendMFAMail;

class MFAController extends Controller
{

    const MFA_CODE_KEY = 'mfa_code';

    protected $authUser;

    protected $helper;

    protected $configGroup;

    public function __construct(MFAHelper $helper, Request $request) {
        $this->helper = $helper;
        $this->configGroup = $request->get('group');
    }

    protected function getAuthUser() {
        if ($this->authUser) {
            return $this->authUser;
        }
        $this->authUser = $this->helper->getUserModel($this->configGroup);
        return $this->authUser;
    }

    protected function getCodeCacheKey() {
        return self::MFA_CODE_KEY . '-' . $this->configGroup . '-' . $this->getAuthUser()->id;
    }

    protected function userNotLoggedInRedirect() {

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
            return redirect()->to(config('app.url'));
        }
        return false;
    }

    public function getIndex(Request $request) {

        if ($redirect = $this->userNotLoggedInRedirect()) {
            return $redirect;
        }

        $code = rand(100000, 999999);
        $minutes = $this->helper->getConfigByGroup('code_expire_after_minutes', $this->configGroup, 10);
        Cache::put($this->getCodeCacheKey(), $code, $minutes);


        $emailTemplate = $this->helper->getConfigByGroup('email.template', $this->configGroup);
        $emailVars = [
            'user' => $this->getAuthUser(),
            'code' => $code,
            'minutes' => $minutes,
        ];

        $mailer = Mail::to($this->getAuthUser()->email);
        $mailable = new SendMFAMail($emailTemplate, $emailVars, $this->helper->getConfigByGroup('email.subject', $this->configGroup));
        if ($this->helper->getConfigByGroup('email.queue', $this->configGroup)) {
            $mailer->queue($mailable);
        } else {
            $mailer->send($mailable);
        }


        return redirect()->route('mfa.mfa-form', [
            'group' => $this->configGroup,
            'referer' => $request->get('referer')
        ]);
    }

    public function getForm(Request $request) {

        if ($redirect = $this->userNotLoggedInRedirect()) {
            return $redirect;
        }

        $minutes = $this->helper->getConfigByGroup('code_expire_after_minutes', $this->configGroup, 10);

        return view('laravel-mfa::mfa.form', [
            'group' => $this->configGroup,
            'referer' => $request->get('referer'),
            'minutes' => $minutes,
        ]);
    }

    public function postForm(Request $request) {

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

        return redirect()->to($request->get('referer', config('app.url')));
    }

}
