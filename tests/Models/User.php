<?php

namespace Sicaboy\LaravelMFA\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $fillable = ['id', 'email', 'name'];
    
    public $timestamps = false;
}
