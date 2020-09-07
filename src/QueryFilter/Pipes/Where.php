<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\QueryFilter\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class Where implements QueryPipeContract
{
    private string $field;
    private string $condition;
    private $value;

    public function __construct(string $field, string $condition, $value)
    {
        $this->field = $field;
        $this->condition = $condition;
        $this->value = $value;
    }

    public function handle(Builder $query, Closure $next)
    {
        return $next(
            $query->where($this->field, $this->condition, $this->value)
        );
    }
}
