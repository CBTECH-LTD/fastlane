<?php

namespace CbtechLtd\Fastlane\QueryFilter\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;

interface QueryPipeContract
{
    public function handle(Builder $query, Closure $next);
}
