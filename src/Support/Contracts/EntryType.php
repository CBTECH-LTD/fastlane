<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EntryType extends Hookable
{
    public function resolveForRequest(EntryRequest $request): EntryType;

    public function identifier(): string;

    public function name(): string;

    public function pluralName(): string;

    public function icon(): string;

    public function model(): string;

    public function newModelInstance(): Model;

    public function apiResource(): string;

    public function policy(): ?string;

    public function fields(): array;

    public function schema(): EntrySchema;

    public function isVisibleOnMenu(): bool;

    public function getItems(): Collection;

    public function findItem(string $hashid): Model;

    public function store(EntryRequest $request): Model;

    public function update(EntryRequest $request, string $hashid): Model;

    public function delete(string $hashid): Model;

    public function install(): void;

    public function makeModelTitle(Model $model): string;
}
