<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use CbtechLtd\Fastlane\QueryFilter\Pipes\QueryPipeContract;
use Closure;
use Illuminate\Support\Str;

class WhereParent implements QueryPipeContract
{
    private ?string $parentId;

    public function __construct(?string $parentId)
    {
        $this->parentId = $parentId;
    }

    public function handle(QueryBuilder $query, Closure $next)
    {
        $this->prepareQuery($query);

        return $next($query);
    }

    protected function prepareQuery(QueryBuilder $query): void
    {
        if (is_null($this->parentId)) {
            $query->getBuilder()->whereNull('parent_id');
            return;
        }

        $query->getBuilder()->where('parent_id', $this->parentId);
    }
}
