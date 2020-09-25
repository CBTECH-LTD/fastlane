<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;

trait QueriesModel
{
    public function query(): QueryBuilder
    {
        return new QueryBuilder($this);
    }
}
