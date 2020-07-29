<?php

namespace CbtechLtd\Fastlane\Support\Schema;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Resolvable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EntrySchema implements Contracts\EntrySchema
{
    private EntryTypeContract $entryType;
    private EntryRequest $request;
    private array $allFields = [];
    private array $indexFields = [];
    private array $createFields = [];
    private array $updateFields = [];
    private array $panels = [];

    public function __construct(EntryTypeContract $entryType)
    {
        $this->entryType = $entryType;
    }

    public function resolve(EntryRequest $request): self
    {
        $this->request = $request;

        $this->allFields = $this->build($this->entryType->allFields());
        $this->indexFields = $this->build($this->entryType->fieldsOnIndex());
        $this->createFields = $this->build($this->entryType->fieldsOnCreate());
        $this->updateFields = $this->build($this->entryType->fieldsOnUpdate());

        return $this;
    }

    public function getFields(): array
    {
        return $this->allFields;
    }

    public function getIndexFields(): array
    {
        return $this->indexFields;
    }

    public function getCreateFields(): array
    {
        return $this->createFields;
    }

    public function getUpdateFields(): array
    {
        return $this->updateFields;
    }

    public function getPanels(): array
    {
        return [];
    }

    public function findField(string $name): SchemaField
    {
        return Arr::get($this->allFields, $name);
    }

    private function build(array $fields): array
    {
        return Collection::make($fields)
            ->filter(fn($field) => $field instanceof Resolvable)
            ->map(function ($field) {
                $field->resolve($this->entryType, $this->request);
                return $field;
            })
            ->all();
    }
}
