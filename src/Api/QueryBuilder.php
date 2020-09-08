<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Api;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class QueryBuilder
{
    private Builder $builder;
    private EntryInstance $entryInstance;

    public function __construct(EntryType $entryType)
    {
        $this->entryInstance = $entryType->newInstance(
            $entryType->newModelInstance()
        );

        $this->builder = $this->entryInstance->model()->newQuery();
    }

    public function limit(int $value): self
    {
        $this->builder->limit($value);
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

    public function first(): EntryInstance
    {
        return $this->entryInstance->type()->newInstance($this->builder->first());
    }
}
