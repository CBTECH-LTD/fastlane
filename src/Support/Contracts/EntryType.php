<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\QueryFilter\QueryFilterContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface EntryType extends Hookable
{
    public static function load(): EntryType;

    public function newInstance(?Model $model): EntryInstance;

    public function identifier(): string;

    public function name(): string;

    public function pluralName(): string;

    public function icon(): string;

    public function model(): string;

    public function newModelInstance(): Model;

    public function apiResource(): string;

    public function apiResourceCollection(): string;

    public function policy(): ?string;

    public function fields(): array;

    public function isVisibleOnMenu(): bool;

    public function getItems(?QueryFilterContract $queryFilter = null): LengthAwarePaginator;

    public function getItemsForRelationField(?QueryFilterContract $queryFilter = null): Collection;

    public function findItem(string $hashid): EntryInstance;

    public function store(Request $request): EntryInstance;

    public function update(Request $request, string $hashid): EntryInstance;

    public function delete(string $hashid): EntryInstance;

    public function install(): void;
}
