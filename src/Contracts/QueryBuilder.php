<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Contracts;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface QueryBuilder
{
    public function __construct(EntryType $entryType);

    /**
     * Get the underlying Eloquent builder instance.
     *
     * @return Builder
     */
    public function getBuilder(): Builder;

    /**
     * Get results that are active.
     *
     * @return $this
     */
    public function active(): self;

    /**
     * Limit the number of items fetched.
     *
     * @param int $value
     * @return $this
     */
    public function limit(int $value): self;

    /**
     * Filter results by their key column (most often id).
     *
     * @param $value
     * @return $this
     */
    public function key($value): self;

    /**
     * Filter results by their route key, as configured
     * in the underlying model.
     *
     * @param $value
     * @return $this
     */
    public function routeKey($value): self;

    /**
     * Do not include the given items in the result.
     *
     * @param array  $ids
     * @param string $column
     * @return $this
     */
    public function except(array $ids, string $column = 'id'): self;

    /**
     * Order the items by the given column and direction.
     *
     * @param string $column
     * @param string $order
     * @return $this
     */
    public function orderBy(string $column, string $order = 'asc'): self;

    /**
     * Filter results that have at least one item related to
     * the given relationship.
     *
     * @param string $relationshipName
     * @return $this
     */
    public function hasRelated(string $relationshipName): self;

    /**
     * Add a custom Eloquent query through the given callback.
     *
     * @param Closure     $callback
     * @param string|null $cacheKey
     * @return $this
     */
    public function query(Closure $callback, ?string $cacheKey = null): self;

    /**
     * Call the given callback if the given value is truthy.
     *
     * @param      $value
     * @param      $callback
     * @param null $default
     * @return $this
     */
    public function when($value, $callback, $default = null): self;

    /**
     * Define what columns must be returned from database.
     *
     * @param array $columns
     * @return $this
     */
    public function select(array $columns): self;

    /**
     * Fetch and paginate the result.
     *
     * @param int|null $itemsPerPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $itemsPerPage = null): LengthAwarePaginator;

    /**
     * Fetch the result using the underlying Eloquent builder.
     *
     * @return Collection
     */
    public function get(): Collection;

    /**
     * Fetch a single item.
     *
     * @return EntryType|null
     */
    public function first(): ?EntryType;
}
