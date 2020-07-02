<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\Support\AccessControl\ModelPolicy;

class BackendUserPolicy extends ModelPolicy
{
    public function list($user)
    {
        return $user->can(BackendUserEntryType::PERM_MANAGE_SYSTEM_ADMINS);
    }

    public function create($user)
    {
        return false;
    }

    public function update($user, $model)
    {
        return false;
    }

    public function delete($user, $model)
    {
        return false;
    }
}
