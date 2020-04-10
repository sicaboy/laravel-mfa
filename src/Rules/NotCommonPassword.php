<?php


namespace Sicaboy\LaravelSecurity\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class NotCommonPassword implements Rule
{

    /** @var string */
    protected $attribute;

    public function __construct()
    {
    }

    public function passes($attribute, $value): bool
    {
        //$this->attribute = $attribute;
        $wordsStr = config( 'laravel-security-insecure-passwords');
        $cache_key = md5($wordsStr);
        $data = Cache::rememberForever('not_common_password_list_' . $cache_key, function () use ($wordsStr) {
            return collect(explode("\n", $wordsStr));
        });
        return !$data->contains($value);
    }

    public function message(): string
    {
        return __('This password is too common used. Please try another.', [
            'attribute' => $this->attribute,
        ]);

    }
}
