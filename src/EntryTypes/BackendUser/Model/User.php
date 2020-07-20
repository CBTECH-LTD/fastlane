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
use Spatie\Permission\Traits\HasRoles;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    protected bool $fillableFromSchema = false;

    use Notifiable, Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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

    public function setPasswordAttribute(string $value): self
    {
        $this->attributes['password'] = Hash::make($value);
        return $this;
    }
}
