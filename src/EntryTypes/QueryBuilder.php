<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class QueryBuilder
{
    protected Builder $builder;
    protected EntryInstance $entryInstance;
    protected int $cacheSeconds = 86400;
    protected array $cacheKey = [];
    protected bool $shouldCache;

    public function __construct(EntryType $entryType)
    {
        $this->shouldCache = (bool)config('fastlane.cache');

        $this->entryInstance = $entryType->newInstance(
            $entryType->newModelInstance()
        );

        $this->builder = $this->entryInstance->model()->newModelQuery();
        $this->cacheKey = [$entryType->identifier()];
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function disableCache(): self
    {
        $this->shouldCache = false;
        return $this;
    }

    public function cacheFor(int $seconds): self
    {
        $this->cacheSeconds = $seconds;
        return $this;
    }

    public function active(): self
    {
        $this->builder->where('is_active', true);
        return $this->addCacheKey('active');
    }

    public function limit(int $value): self
    {
        $this->builder->limit($value);
        return $this->addCacheKey("limit[{$value}]");
    }

    public function key($value): self
    {
        $this->builder->whereKey($value);
        return $this->addCacheKey("key[{$value}]");
    }

    public function slug(string $value): self
    {
        $this->builder->where('slug', $value);
        return $this->addCacheKey("slug[{$value}]");
    }

    public function routeKey($value): self
    {
        $this->builder->where(
            $this->entryInstance->model()->getRouteKeyName(),
            $value
        );

        return $this->addCacheKey("route[{$value}]");
    }

    public function except(array $ids, string $column = 'id'): self
    {
        if (empty($ids)) {
            return $this;
        }

        $this->builder->whereNotIn($column, $ids);
        return $this->addCacheKey("except[{$column}-" . implode(',', $ids) . ']');
    }

    public function orderBy(string $column, string $order = 'asc'): self
    {
        $this->builder->orderBy($column, $order);
        return $this->addCacheKey("orderBy[{$column}-{$order}]");
    }

    public function hasRelated(string $relationshipName): self
    {
        $this->builder->has($relationshipName);
        return $this->addCacheKey("related[{$relationshipName}]");
    }

    public function withRelated(...$relationships): self
    {
        $this->builder->with($relationships);
        return $this;
    }

    public function query(Closure $callback, ?string $cacheKey = null): self
    {
        $callback($this->builder);

        return $cacheKey
            ? $this->addCacheKey($cacheKey)
            : $this;
    }

    public function when($value, $callback, $default = null): self
    {
        $defaultCb = $default
            ? function ($v) use ($default) {
                $default($this, $v);
            }
            : null;

        $okCb = function ($_, $v) use ($callback) {
            $callback($this, $v);
        };

        $this->builder->when($value, $okCb, $defaultCb);

        return $this;
    }

    public function select(array $columns): self
    {
        $this->getBuilder()->select($columns);
        return $this;
    }

    public function paginate(int $itemsPerPage): LengthAwarePaginator
    {
        $fn = function () use ($itemsPerPage): LengthAwarePaginator {
            return $this->builder->paginate($itemsPerPage);
        };

        $paginator = empty($cacheKey)
            ? $fn()
            : Cache::remember($this->generateCacheKey(), $this->cacheSeconds, $fn);

        $paginator->getCollection()->transform(
            fn(Model $model) => $this->entryInstance->type()->newInstance($model)
        );

        return $paginator;
    }

    public function get(): Collection
    {
        $fn = function () {
            return $this->builder->get();
        };

        $items = ! $this->shouldCache || empty($this->cacheKey)
            ? $fn()
            : $this->cache()->rememberForever($this->generateCacheKey(), $fn);

        return $items->map(
            fn(Model $model) => $this->entryInstance->type()->newInstance($model)
        );
    }

    public function first(): ?EntryInstance
    {
        $fn = function () {
            return $this->builder->first();
        };

        $model = ! $this->shouldCache || empty($cacheKey)
            ? $fn()
            : $this->cache()->remember($this->generateCacheKey(), $this->cacheSeconds, $fn);

        return $this->entryInstance->type()->newInstance($model);
    }

    public function firstOrFail(): EntryInstance
    {
        $item = $this->first();

        if ($item->model()->exists) {
            return $item;
        }

        abort(Response::HTTP_NOT_FOUND);
    }

    protected function addCacheKey(string $key): self
    {
        $this->cacheKey[] = $key;
        return $this;
    }

    protected function generateCacheKey(): string
    {
        $key = implode(':', $this->cacheKey);

        return sha1($this->isCacheTaggable() ? $key : "fastlane:{$key}");
    }

    protected function cache()
    {
        if ($this->isCacheTaggable()) {
            return Cache::tags(['fastlane', $this->entryInstance->type()->identifier()]);
        }

        return Cache::store();
    }

    /**
     * @return bool
     */
    protected function isCacheTaggable(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
