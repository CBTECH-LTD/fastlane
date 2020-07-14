<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResourceCollection;
use Illuminate\Database\Eloquent\Model;

interface EntryType
{
    public function identifier(): string;

    public function name(): string;

    public function pluralName(): string;

    public function icon(): string;

    public function model(): string;

    public function apiResource(): string;

    public function policy(): ?string;

    public function schema(): EntrySchema;

    public function isVisibleOnMenu(): bool;

    public function getItems(): ApiResourceCollection;

    public function findItem(string $hashid): ApiResource;

    public function store(EntryRequest $request): Model;

    public function update(EntryRequest $request, string $hashid): Model;

    public function delete(string $hashid): Model;

    public function install(): void;
}
