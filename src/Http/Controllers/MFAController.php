<?php

namespace Sicaboy\LaravelMFA\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class MFAController extends Controller
{

    const MFA_CODE_KEY = 'mfa_code';

    public function getIndex(Request $request) {

        $config = config('laravel-mfa');
        $user = Auth::user();
        $code = rand(100000, 999999);
        $minutes = config('laravel-mfa.code_expire_after_minutes', 10);
        Cache::put(self::MFA_CODE_KEY . '-' . $user->id, $code, $minutes);

        Mail::send($config['email']['template'], [
            'user' => $user,
            'code' => $code,
            'minutes' => $minutes,
        ], function($message) use ($user, $config) {
            $message->to($user->email);
            $message->subject($config['email']['subject']);
        });

        return redirect()->route('mfa.mfa-form', [
            'referer' => $request->get('referer')
        ]);
    }

    public function getForm(Request $request) {

        $minutes = config('laravel-mfa.code_expire_after_minutes', 10);

        return view('laravel-mfa::mfa.form', [
            'referer' => $request->get('referer'),
            'minutes' => $minutes,
        ]);
    }

    public function postForm(Request $request) {

        $user = Auth::user();
        $code = Cache::get(self::MFA_CODE_KEY . '-' . $user->id);

        if (!$code || trim($request->get('code')) != $code) {
            return redirect()->back()->withErrors([
                'code' => __('The code is not correct.')
            ]);
        }

        Session::put('mfa_completed', true);

        return redirect()->to($request->get('referer', '/'));
    }

}
