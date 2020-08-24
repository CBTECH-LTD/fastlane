<?php

namespace CbtechLtd\Fastlane\QueryFilter\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class OrderBy implements QueryPipeContract
{
    private string $field;
    private string $sort;

    public function __construct(string $field, string $sort)
    {
        $this->field = $field;
        $this->sort = $sort;
    }

    public function handle(Builder $query, Closure $next)
    {
        return $next(
            $query->orderBy($this->field, $this->sort)
        );
    }
}
