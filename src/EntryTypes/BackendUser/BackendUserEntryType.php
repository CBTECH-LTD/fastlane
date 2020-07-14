<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes\RandomPasswordPipe;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes\UpdateRolePipe;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BackendUserEntryType extends EntryType
{
    const PERM_MANAGE_SYSTEM_ADMINS = 'manage admins';
    const ROLE_SYSTEM_ADMIN = 'system admin';

    public function __construct(Gate $gate)
    {
        parent::__construct($gate);

        $this->addHook(static::HOOK_BEFORE_CREATING, RandomPasswordPipe::class);
        $this->addHook(static::HOOK_BEFORE_UPDATING, UpdateRolePipe::class);
    }

    public function icon(): string
    {
        return 'user';
    }

    public function model(): string
    {
        return User::class;
    }

    protected function queryItems(Builder $query): void
    {
        $query->except(Auth::user());
    }

    protected function querySingleItem(Builder $query, string $hashid): void
    {
        $query->except(Auth::user());
    }
}
