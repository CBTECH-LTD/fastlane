<?php

namespace CbtechLtd\Fastlane\QueryFilter;

use CbtechLtd\Fastlane\QueryFilter\Pipes\QueryPipeContract;
use Illuminate\Database\Eloquent\Builder;

interface QueryFilterContract
{
    public function withOrder(string $order): self;

    public function addOrder(string $order): self;

    public function addFilter(QueryPipeContract $queryPipe): self;

    public function pipeThrough(Builder $builder): Builder;
}
