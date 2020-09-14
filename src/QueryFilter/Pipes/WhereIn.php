<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\QueryFilter\Pipes;

use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use Closure;

class WhereIn implements QueryPipeContract
{
    private string $field;
    private array $value;

    public function __construct(string $field, array $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function handle(QueryBuilder $query, Closure $next)
    {
        return $next(
            $query->getBuilder()->whereIn($this->field, $this->value)
        );
    }
}
