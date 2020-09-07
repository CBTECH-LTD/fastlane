<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\QueryFilter;

use CbtechLtd\Fastlane\QueryFilter\Pipes\OrderBy;
use CbtechLtd\Fastlane\QueryFilter\Pipes\QueryPipeContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QueryFilter implements QueryFilterContract
{
    protected Collection $order;
    protected Collection $filters;

    public function __construct()
    {
        $this->order = new Collection;
        $this->filters = new Collection;
    }

    public static function make(): self
    {
        return new static;
    }

    public function withOrder(string $order): self
    {
        $this->order = new Collection;
        $this->addOrder($order);

        return $this;
    }

    public function addOrder(string $order): self
    {
        $field = Str::replaceFirst('-', '', $order);
        $sort = Str::startsWith($order, '-')
            ? 'desc'
            : 'asc';

        $this->order->push(new OrderBy($field, $sort));

        return $this;
    }

    public function addFilter(QueryPipeContract $queryPipe): self
    {
        $this->filters->push($queryPipe);
        return $this;
    }

    public function pipeThrough(Builder $builder): Builder
    {
        return app(Pipeline::class)
            ->send($builder)
            ->through($this->order->all())
            ->through($this->filters->all())
            ->then(fn(Builder $builder) => $builder);
    }
}
