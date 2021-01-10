<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser\Model;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Support\Eloquent\BaseQueryBuilder;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\QueriesActive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class UserQueryBuilder extends BaseQueryBuilder
{
    use QueriesActive;

    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
    }

    public function byRole(string $role): self
    {
        $this->whereHas('roles', function (Builder $builder) use ($role) {
            $builder->where('name', $role);
        });

        return $this;
    }

    public function systemAdmins(): self
    {
        return $this->byRole(BackendUserEntryType::ROLE_SYSTEM_ADMIN);
    }

    public function except(User $user)
    {
        $this->whereKeyNot($user->getKey());
        return $this;
    }
}
