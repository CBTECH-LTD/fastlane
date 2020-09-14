<?php

namespace CbtechLtd\Fastlane\QueryFilter\Pipes;

use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use Closure;

interface QueryPipeContract
{
    public function handle(QueryBuilder $query, Closure $next);
}
