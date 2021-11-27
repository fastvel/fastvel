<?php

namespace Imdgr886\User\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends \Illuminate\Foundation\Auth\User implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'name', 'email', 'mobile', 'password'
    ];

    ##### Jwt 契约 start #####

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    ##### Jwt 契约 end #####
}
