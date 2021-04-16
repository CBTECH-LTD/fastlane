<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use CbtechLtd\Fastlane\QueryFilter\Pipes\QueryPipeContract;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WhereMimetype implements QueryPipeContract
{
    private array $mimetypes;
    private bool $includeDirectories;

    /**
     * WhereMimetype constructor.
     * @param array $mimetypes
     * @param bool $includeDirectories
     */
    public function __construct(array $mimetypes, bool $includeDirectories = true)
    {
        $this->mimetypes = $mimetypes;
        $this->includeDirectories = $includeDirectories;
    }

    public function handle(QueryBuilder $query, Closure $next)
    {
        $query->getBuilder()->where(function ($q) {
            $queryTypes = $this->getMimetypeQueries();

            $queryTypes->get('like', Collection::make())->each(
                fn ($str) =>$q->orWhere('mimetype', 'like', $str)
            );

            if ($queryTypes->has('in')) {
                $q->orWhereIn('mimetype', $queryTypes->get('in'));
            }

            if ($this->includeDirectories) {
                $q->orWhere('mimetype', 'fastlane/directory');
            }
        });

        return $next($query);
    }

    protected function getMimetypeQueries(): Collection
    {
        return Collection::make($this->mimetypes)->mapToGroups(function ($type) {
            if (Str::endsWith($type, '/*')) {
                return [
                    'like' => Str::replaceLast('/*', '/', $type) . '%',
                ];
            }

            return [
                'in' => $type,
            ];
        });
    }
}
