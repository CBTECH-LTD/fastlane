<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser\Model;

use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasRoles, HasApiTokens;

    protected $table = 'fastlane_users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function newEloquentBuilder($query)
    {
        return new UserQueryBuilder($query);
    }

    public function newCollection(array $models = [])
    {
        return new UserCollection($models);
    }

    public function getRoleAttribute(): string
    {
        return $this->roles->first()->name;
    }

    public function setPasswordAttribute(string $value, bool $hash = true): self
    {
        $this->attributes['password'] = $hash ? Hash::make($value) : $value;
        return $this;
    }
}
