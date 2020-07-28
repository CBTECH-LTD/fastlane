<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

interface SchemaField extends Arrayable
{
    public function getType(): string;

    public function getName(): string;

    public function getLabel(): string;

    public function readValue(Model $model);

    public function readValueUsing($callback): self;

    public function hydrateValue($model, $value, EntryRequest $request): void;

    public function hydrateUsing($callback): self;

    public function resolve(EntryType $entryType, EntryRequest $request): void;

    public function isRequired(): bool;

    public function required(bool $required = true): self;

    public function setRules(string $rules): self;

    public function getCreateRules(): string;

    public function setCreateRules(string $rules): self;

    public function getUpdateRules(): string;

    public function getDefault();

    public function setDefault($default): self;

    public function setUpdateRules(string $rules): self;

    public function showOnIndex(bool $state = true): self;

    public function hideOnCreate(bool $state = true): self;

    public function hideOnUpdate(bool $state = true): self;

    public function hideOnForm(bool $state = true): self;

    public function isShownOnIndex(): bool;

    public function isShownOnCreate(): bool;

    public function isShownOnUpdate(): bool;

    public function toMigration(): string;

    /**
     * Exports an array to be used by the model to define
     * a fillable column and its casting type.
     *
     * The returned array must be in the following format:
     *  [
     *      "field_name" => string | null
     * ]
     *
     * @return array
     */
    public function toModelAttribute(): array;
}
