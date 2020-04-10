<?php


namespace Sicaboy\LaravelSecurity\Rules;

use Illuminate\Contracts\Validation\Rule;
use Hash;

class NotUsedPassword implements Rule
{

    /** @var string */
    protected $modelClassName;

    /** @var string */
    protected $modelAttribute;

    /** @var string */
    protected $attribute;

    /**
     * Only check used password for userId
     * @var integer
     */
    protected $userId;

    public function __construct($userId = null, $modelClassName = null)
    {
        if(!$modelClassName) {
            $modelClassName = config('laravel-security.database.password_history_model');
        }
        $this->userId = $userId;
        $this->modelClassName = $modelClassName;
    }


    public function passes($attribute, $value): bool
    {
        // $this->attribute = $attribute;
        $model = $this->modelClassName::select('password');
        if(!empty($this->userId)) {
            $model->where('user_id', $this->userId);
        }
        $allUsedPasswords = $model->get();
        $isOldPassword = false;
        foreach ($allUsedPasswords as $item) {
            if (Hash::check($value, $item->password)) {
                $isOldPassword = true;
            }
        }
        return !$isOldPassword;
    }

    public function message(): string
    {
        return __('laravel-security.not_used_password', [
            // 'attribute' => $this->attribute,
            // 'model' => $classBasename,
        ]);
    }
}
