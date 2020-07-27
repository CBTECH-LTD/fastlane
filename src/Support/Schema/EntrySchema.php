<?php

namespace CbtechLtd\Fastlane\Support\Schema;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EntrySchema implements Contracts\EntrySchema
{
    private EntryType $entryType;
    private array $allFields = [];
    private array $indexFields = [];
    private array $createFields = [];
    private array $updateFields = [];

    public function __construct(EntryType $entryType)
    {
        $this->entryType = $entryType;

        $this->allFields = $this->build($this->entryType->fields());
        $this->indexFields = $this->build($this->entryType->fieldsOnIndex());
        $this->createFields = $this->build($this->entryType->fieldsOnCreate());
        $this->updateFields = $this->build($this->entryType->fieldsOnUpdate());
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
                return $field->setEntryType($this->entryType);
            })->all();
    }
}
