<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\Repositories\Repository;

class BackendUserRepository extends Repository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
