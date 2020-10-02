<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\QueryFilter\Pipes;

use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use Closure;

class WhereStartsWith implements QueryPipeContract
{
    private string $field;
    private string $value;

    public function __construct(string $field, string $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function handle(QueryBuilder $query, Closure $next)
    {
        return (new Where($this->field, 'like', $this->value . '%'))->handle(
            $query,
            $next
        );
    }
}
