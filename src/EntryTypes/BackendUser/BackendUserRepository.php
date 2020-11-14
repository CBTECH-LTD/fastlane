<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\Repositories\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BackendUserRepository extends Repository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    protected function beforeFetchListing(Builder $query): void
    {
        if (Auth::guard('fastlane-cp')->check()) {
            $query->where('id', '!=', Auth::user()->getKey());
        }
    }
}
