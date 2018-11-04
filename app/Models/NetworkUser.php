<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class NetworkUser extends Model// implements AuthenticatableContract, AuthorizableContract
{
    // use Authenticatable, Authorizable, SoftDeletes, HasApiTokens;

    // const ADMIN_ROLE = 'ADMIN_USER';
    // const BASIC_ROLE = 'BASIC_USER';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId',
        'firstName',
        'lastName',
        'clientId',
        'userSecret',
        'clientCode',
        'userToken',
        'tokenExpire'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        //'password',
    ];

    // /**
    //  * @return bool
    //  */
    // public function isAdmin()
    // {
    //     return (isset($this->role) ? $this->role : self::BASIC_ROLE) == self::ADMIN_ROLE;
    // }
}
