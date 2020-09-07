<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\QueryFilter\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class WhereIn implements QueryPipeContract
{
    private string $field;
    private array $value;

    public function __construct(string $field, array $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function handle(Builder $query, Closure $next)
    {
        return $next(
            $query->whereIn($this->field, $this->value)
        );
    }
}
