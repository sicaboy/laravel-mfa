<?php

namespace Sicaboy\LaravelMFA\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class MFAHelper
{
    public function getConfigByGroup($key, $group, $default = null) {
        if ($group && $value = config("laravel-mfa.group.{$group}.{$key}")) {
            return $value;
        }
        if ($value = config("laravel-mfa.default.{$key}")) {
            return $value;
        }
        return $default;
    }

    public function isVerificationCompleted($configGroup): bool {
        return Cache::has('mfa_completed_' . $configGroup . '_' . $this->getUserModel($configGroup)->id);
    }

    public function setVerificationCompleted($configGroup) {
        Cache::put('mfa_completed_' . $configGroup . '_' . $this->getUserModel($configGroup)->id, true, 1440);
    }

    public function clearVerificationCompleted($configGroup, $userId) {
        Cache::forget('mfa_completed_' . $configGroup . '_' . $userId);
    }

    public function getUserModel($configGroup) {
        $closure = $this->getConfigByGroup('auth_user_closure', $configGroup, function() {
            return Auth::user();
        });
        return call_user_func($closure);
    }
}
