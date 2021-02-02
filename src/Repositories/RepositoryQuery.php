<?php

namespace CbtechLtd\Fastlane\Repositories;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Models\Entry;
use CbtechLtd\Fastlane\Models\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class RepositoryQuery
{
    private Builder $query;
    private string $entryType;

    public function __construct(string $entryType, Builder $query)
    {
        $this->query = $query;
        $this->entryType = $entryType;
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Add a custom query through a closure.
     *
     * @param callable $closure
     * @return $this
     */
    public function custom(callable $closure): self
    {
        call_user_func($closure, $this->query);

        return $this;
    }

    /**
     * Get a list of items. Set $perPage argument to null if
     * pagination is not required.
     *
     * @param array    $columns
     * @param array    $filters
     * @param int|null $perPage
     */
    public function get(array $columns = [], array $filters = [], ?int $perPage = 20)
    {
        // Get the columns we want to retrieve from the database.
        $queryCols = ['id'];

        if (in_array(HasUuid::class, class_uses_recursive($this->query->getModel()))) {
            $queryCols[] = 'uuid';
        }

        // Select the columns we want to retrieve
        $this->query->select(
            $this->getColumnListing(array_merge($queryCols, $columns))
        );

        // Occasionally set the field we want to order and the sorting direction
        if ($orderBy = Arr::get($filters, 'order')) {
            $this->query->orderBy(...explode(':', $orderBy));
        }

        // Fetch the results then transform the collection
        // to entry instances.
        $this->beforeFetchListing();

        $result = is_null($perPage)
            ? $this->query->get()
            : $this->query->paginate($perPage);

        return tap($result, function ($r) {
            $collection = ($r instanceof LengthAwarePaginator)
                ? $r->getCollection()
                : $r;

            $collection->transform(fn (Model $model) => $this->transformModelToEntryInstance($model));
        });
    }

    /**
     * Find a single entry.
     *
     * @param          $id
     * @param string[] $columns
     * @return Model|null
     */
    public function findOne($id, $columns = ['*']): ?EntryInstance
    {
        $item = $this->query
            ->select($columns)
            ->where($this->query->getModel()->getRouteKeyName(), $id)
            ->first();

        return $this->transformModelToEntryInstance($item);
    }

    /**
     * Fetch the provided IDs.
     *
     * @param array    $ids
     * @param string[] $columns
     * @return Collection
     */
    public function findMany(array $ids, $columns = ['*']): Collection
    {
        return $this->query
            ->select($columns)
            ->where($this->query->getModel()->getRouteKeyName(), $id)
            ->get()
            ->map(fn (Model $model) => $this->transformModelToEntryInstance($model));
    }

    /**
     * Find one entry or throw a 404 error.
     *
     * @param string|int $id
     * @param string[]   $columns
     * @return EntryInstance
     */
    public function findOrFail($id, $columns = ['*']): EntryInstance
    {
        $item = $this->query
            ->select($columns)
            ->where($this->query->getModel()->getRouteKeyName(), $id)
            ->firstOrFail();

        return $this->transformModelToEntryInstance($item);
    }

    /**
     * This method is called just before the listing query is
     * executed. It's a good place to customize the query instead
     * of messing with all the base query provided by Fastlane.
     */
    protected function beforeFetchListing(): void
    {
        //
    }

    /**
     * Generate an entry instance from the given entry type and model.
     *
     * @param Model $model
     * @return EntryInstance
     */
    protected function transformModelToEntryInstance(Model $model): EntryInstance
    {
        return EntryInstance::newFromModel($this->entryType, $model);
    }

    /**
     * Get a list of columns defined in the database table.
     *
     * @param array|null $only
     * @return array
     */
    protected function getColumnListing(?array $only = null): array
    {
        return collect($this->query->getConnection()->getSchemaBuilder()->getColumnListing($this->query->getModel()->getTable()))
            ->mapWithKeys(fn($c) => [$c => $c])
            ->when($only, fn($collection, $onlyCols) => $collection->only($onlyCols))
            ->values()->all();
    }
}
