<?php

namespace CbtechLtd\Fastlane\Contracts;

use CbtechLtd\Fastlane\EntryTypes\EntryTypeRouteCollection;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\Transformers\EntryResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface EntryType
{
    /**
     * Determine a unique identifier.
     *
     * @return string
     */
    public static function key(): string;

    /**
     * The model used by the entry type.
     *
     * @return Model|string
     */
    public static function model(): string;

    /**
     * The singular name of the entry type.
     *
     * @return string
     */
    public static function name(): string;

    /**
     * The plural name of the entry type.
     *
     * @return string
     */
    public static function pluralName(): string;

    /**
     * The icon to be used or empty for no icon.
     *
     * @return string
     */
    public static function icon(): string;

    /**
     * The authorization policy to be registered.
     *
     * @return string|null
     */
    public static function policy(): ?string;

    /**
     * The list of fields of the entry type.
     *
     * @return array
     */
    public static function fields(): array;

    /**
     * Bootstrap the entry type.
     *
     * @return void
     */
    public static function boot(): void;

    /**
     * Get an instance of the entry type query builder.
     *
     * @return QueryBuilder
     */
    public static function query(): QueryBuilder;

    /**
     * Get an instance of the entry type query builder
     * to fetch items to the listing page.
     *
     * @param callable|null $callback
     * @return LengthAwarePaginator
     */
    public static function queryListing(?callable $callback = null): LengthAwarePaginator;

    /**
     * Get an instance of the entry type query builder
     * to fetch items
     *
     * @param string|int    $id
     * @param callable|null $callback
     * @return EntryType|null
     */
    public static function queryEntry($id, ?callable $callback = null): ?EntryType;

    /**
     * Get a new instance of the entry type.
     *
     * @param Model|null $model
     * @return EntryType
     */
    public static function newInstance(?Model $model = null): EntryType;

    /**
     * Get the key (ID) of the underlying model.
     *
     * @return mixed
     */
    public function entryKey();

    /**
     * Get the route key of the underlying model.
     *
     * @return mixed
     */
    public function entryRouteKey();

    /**
     * Generate a string to be used as the description
     * of the underlying model.
     *
     * @return string
     */
    public function entryTitle(): string;

    /**
     * Determine the controller used by the entry type.
     *
     * @return string
     */
    public static function controller(): string;

    /**
     * Determine the available route of the entry type.
     *
     * @return EntryTypeRouteCollection
     */
    public static function routes(): EntryTypeRouteCollection;

    /**
     * Install the Entry Type.
     */
    public static function install(): void;

    /**
     * Get the underlying Model instance.
     *
     * @return Model
     */
    public function modelInstance(): Model;

    /**
     * Get the collection of all resolved fields.
     *
     * @return FieldCollection
     */
    public function getFields(): FieldCollection;

    /**
     * Create the underlying model with the request data.
     *
     * @param array $data
     * @return EntryType
     */
    public function store(array $data): EntryType;

    /**
     * Update the underlying model with the request data.
     *
     * @param array $data
     * @return $this
     */
    public function update(array $data): self;

    /**
     * Delete the instance of the underlying model.
     *
     * @return \CbtechLtd\Fastlane\EntryTypes\EntryType
     * @throws \Exception
     */
    public function delete(): self;

    /**
     * Get an array representation of the entry type including
     * only fields available on listing page.
     *
     * @return EntryResource
     */
    public function toListingResource(): EntryResource;

    /**
     * Get an array representation of the entry type including
     * only fields available on create page.
     *
     * @return EntryResource
     */
    public function toCreateResource(): EntryResource;

    /**
     * Get an array representation of the entry type including
     * only fields available on update page.
     *
     * @return EntryResource
     */
    public function toUpdateResource(): EntryResource;
}
