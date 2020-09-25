<?php

namespace CbtechLtd\Fastlane\QueryFilter;

use CbtechLtd\Fastlane\Contracts\QueryBuilder;
use CbtechLtd\Fastlane\QueryFilter\Pipes\QueryPipeContract;

interface QueryFilterContract
{
    public function withOrder(string $order): self;

    public function addOrder(string $order): self;

    public function addFilter(QueryPipeContract $queryPipe): self;

    public function pipeThrough(QueryBuilder $builder): QueryBuilder;
}
