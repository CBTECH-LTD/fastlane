<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface EntryType extends Hookable
{
    public function resolve(): EntryType;

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

    public function schema(): EntrySchema;

    public function isVisibleOnMenu(): bool;

    public function getItems(?Closure $queryCallback = null): Collection;

    public function findItem(string $hashid, ?Closure $queryCallback = null): Model;

    public function store(Request $request): Model;

    public function update(Request $request, string $hashid): Model;

    public function delete(string $hashid): Model;

    public function install(): void;

    public function makeModelTitle(Model $model): string;
}
