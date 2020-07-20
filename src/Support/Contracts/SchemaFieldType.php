<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use Illuminate\Contracts\Support\Arrayable;

interface SchemaFieldType extends Arrayable
{
    public function getType(): string;

    public function getName(): string;

    public function getLabel(): string;

    public function getEntryType(): EntryType;

    public function setEntryType(EntryType $entryType): self;

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

    public function hydrateValue($model, $value, EntryRequest $request): void;

    public function hydrateUsing($callback): self;

    public function toMigration(): string;

    public function toModelAttribute();
}
