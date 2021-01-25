<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser\Model;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Models\Entry;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\FromEntryType;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Entry implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasRoles, HasApiTokens;

    /** @var string */
    protected $table = 'fastlane_users';

    /**
     * The attributes that should be mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
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

    /**
     * The relationships that should be eager loaded.
     *
     * @var string[]
     */
    protected $with = ['roles'];

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
        return optional($this->roles->first())->name ?? '';
    }

    public function setRoleAttribute($value): void
    {
        static::saved(function (User $user) use ($value) {
            $user->syncRoles(Arr::wrap($value));
        });
    }

    public function setPasswordAttribute(string $value, bool $hash = true): self
    {
        $this->attributes['password'] = $hash ? Hash::make($value) : $value;
        return $this;
    }

    public function toString(): string
    {
        return $this->name ?? '';
    }
}
