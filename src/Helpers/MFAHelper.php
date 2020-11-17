<?php

namespace Sicaboy\LaravelMFA\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * Class MFAHelper
 * @package Sicaboy\LaravelMFA\Helpers
 */
class MFAHelper
{
    /**
     * @param string $key
     * @param string $group
     * @param  mixed  $default
     * @return \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed|null
     */
    public function getConfigByGroup($key, $group, $default = null)
    {
        if ($group && $value = config("laravel-mfa.group.{$group}.{$key}")) {
            return $value;
        }
        if ($value = config("laravel-mfa.default.{$key}")) {
            return $value;
        }
        return $default;
    }

    /**
     * @param string $configGroup
     * @return bool
     */
    public function isVerificationCompleted($configGroup): bool
    {
        return Cache::has('mfa_completed_' . $configGroup . '_' . $this->getUserModel($configGroup)->id);
    }

    /**
     * @param string $configGroup
     */
    public function setVerificationCompleted($configGroup)
    {
        Cache::put('mfa_completed_' . $configGroup . '_' . $this->getUserModel($configGroup)->id, true, now()->addDay());
    }

    /**
     * @param string $configGroup
     * @param int $userId
     */
    public function clearVerificationCompleted($configGroup, $userId)
    {
        Cache::forget('mfa_completed_' . $configGroup . '_' . $userId);
    }

    /**
     * @param  string  $codeCacheKey
     * @param  int  $expiryMinutes
     * @return int
     */
    public function refreshVerificationCode(string $codeCacheKey, int $expiryMinutes)
    {
        $code = rand(100000, 999999);
        Cache::put($codeCacheKey, $code, now()->addMinutes($expiryMinutes));
        return $code;
    }

    /**
     * @param string $configGroup
     * @return mixed
     */
    public function getUserModel($configGroup)
    {
        $closure = $this->getConfigByGroup('auth_user_closure', $configGroup, function () {
            return Auth::user();
        });
        return call_user_func($closure);
    }
}
