<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\Support\Contracts\ModelPolicy;
use Illuminate\Database\Eloquent\Model;

class BackendUserPolicy implements ModelPolicy
{
    public function before($user, $ability)
    {
        if ($user->can(BackendUserEntryType::PERM_MANAGE_SYSTEM_ADMINS)) {
            return true;
        }
    }

    public function list($user)
    {
        return false;
    }

    public function create($user)
    {
        return false;
    }

    public function view($user, Model $model)
    {
        return false;
    }

    public function update($user, Model $model)
    {
        return false;
    }

    public function delete($user, Model $model)
    {
        return false;
    }

    public function createToken($user, Model $model)
    {
        return $user->is($model) && $user->can(BackendUserEntryType::PERM_MANAGE_ACCESS_TOKENS);
    }
}
