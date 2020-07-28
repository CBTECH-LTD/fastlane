<?php

namespace CbtechLtd\Fastlane\Support\Schema;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EntrySchema implements Contracts\EntrySchema
{
    private EntryTypeContract $entryType;
    private EntryRequest $request;
    private bool $isResolved = false;
    private array $allFields = [];
    private array $indexFields = [];
    private array $createFields = [];
    private array $updateFields = [];

    public function __construct(EntryTypeContract $entryType)
    {
        $this->entryType = $entryType;
    }

    public function resolve(EntryRequest $request): self
    {
        $this->request = $request;

        $this->allFields = $this->build($this->entryType->fields());
        $this->indexFields = $this->build($this->entryType->fieldsOnIndex());
        $this->createFields = $this->build($this->entryType->fieldsOnCreate());
        $this->updateFields = $this->build($this->entryType->fieldsOnUpdate());

        $this->isResolved = true;
        return $this;
    }

    public function isResolved(): bool
    {
        return $this->isResolved;
    }

    public function all(): array
    {
        return $this->allFields;
    }

    public function toIndex(): array
    {
        return $this->indexFields;
    }

    public function toCreate(): array
    {
        return $this->createFields;
    }

    public function toUpdate(): array
    {
        return $this->updateFields;
    }

    public function findField(string $name): SchemaField
    {
        return Arr::get($this->allFields, $name);
    }

    private function build(array $fields): array
    {
        return Collection::make($fields)
            ->map(function (SchemaField $field) {
                $field->resolve($this->entryType, $this->request);
                return $field;
            })->all();
    }
}
