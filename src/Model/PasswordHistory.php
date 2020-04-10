<?php

namespace Sicaboy\LaravelSecurity\Model;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'password'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('laravel-security.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('laravel-security.database.password_history_table'));
        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo(config('laravel-security.database.user_model') ?: 'App\User');
    }

}
