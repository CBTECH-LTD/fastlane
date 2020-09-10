<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Api;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class QueryBuilder
{
    protected Builder $builder;
    protected EntryInstance $entryInstance;

    public function __construct(EntryType $entryType)
    {
        $this->entryInstance = $entryType->newInstance(
            $entryType->newModelInstance()
        );

        $this->builder = $this->entryInstance->model()->newQuery();
    }

    public function active(): self
    {
        $this->builder->where('is_active', true);
        return $this;
    }

    public function limit(int $value): self
    {
        $this->builder->limit($value);
        return $this;
    }

    public function key($value): self
    {
        $this->builder->whereKey($value);
        return $this;
    }

    public function except(array $ids, string $column = 'id'): self
    {
        $this->builder->whereNotIn($column, $ids);
        return $this;
    }

    public function orderBy(string $column, string $order = 'asc'): self
    {
        $this->builder->orderBy($column, $order);
        return $this;
    }

    public function hasRelated(string $relationshipName): self
    {
        $this->builder->has($relationshipName);
        return $this;
    }

    public function query(\Closure $callback): self
    {
        $callback($this->builder);
        return $this;
    }

    public function get(): Collection
    {
        return $this->builder
            ->get()
            ->map(
                fn(Model $model) => $this->entryInstance->type()->newInstance($model)
            );
    }

    public function first(): ?EntryInstance
    {
        $model = $this->builder->first();

        if (! $model) {
            return null;
        }

        return $this->entryInstance->type()->newInstance($model);
    }
}
