<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use CbtechLtd\Fastlane\QueryFilter\Pipes\QueryPipeContract;
use Closure;
use Illuminate\Support\Str;

class WhereMimetype implements QueryPipeContract
{
    private array $mimetypes;

    public function __construct(array $mimetypes)
    {
        $this->mimetypes = $mimetypes;
    }

    public function handle(QueryBuilder $query, Closure $next)
    {
        foreach ($this->mimetypes as $type) {
            if (Str::endsWith($type, '/*')) {
                $query->getBuilder()->orWhere('mimetype', 'like', Str::replaceLast('/*', '/', $type) . '%');
                continue;
            }

            $query->getBuilder()->orWhere('mimetype', $type);
        }

        return $next($query);
    }
}
