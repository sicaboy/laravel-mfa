<?php

namespace Sicaboy\LaravelSecurity\Model;

use Illuminate\Database\Eloquent\Model;

class UserExtendSecurity extends Model
{
    
    protected $guarded = ['id'];

    const STATUS_NORMAL = 10;
    const STATUS_LOCKED = -1;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('laravel-security.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('laravel-security.database.user_security_table'));
        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo(config('laravel-security.database.user_model') ?: 'App\User');
    }

}
