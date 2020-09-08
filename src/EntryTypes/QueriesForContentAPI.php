<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Api\QueryBuilder;

trait QueriesForContentAPI
{
    public function apiBuilder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }
}
