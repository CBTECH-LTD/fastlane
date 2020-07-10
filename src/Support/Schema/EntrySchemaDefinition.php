<?php

namespace CbtechLtd\Fastlane\Support\Schema;

use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class EntrySchemaDefinition implements Contracts\EntrySchemaDefinition
{
    private array $fields;

    /**
     * @param SchemaFieldType[] $fields
     * @return EntrySchemaDefinition
     */
    public function __construct(array $fields)
    {
        Assert::allImplementsInterface(
            $fields,
            SchemaFieldType::class,
            'All fields must be instances of ' . SchemaFieldType::class
        );

        $this->fields = $fields;
    }

    /**
     * @param SchemaFieldType[] $fields
     * @return EntrySchemaDefinition
     */
    public static function make(array $fields): EntrySchemaDefinition
    {
        return new static($fields);
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function toArray()
    {
        return $this->transformToArray(Collection::make($this->fields));
    }

    public function toIndex(): EntrySchemaDefinition
    {
        return new EntrySchemaDefinition(
            Collection::make($this->fields)->filter(
                fn(SchemaFieldType $field) => $field->isShownOnIndex()
            )->all()
        );
    }

    public function toCreate(): EntrySchemaDefinition
    {
        return new EntrySchemaDefinition(
            Collection::make($this->fields)->filter(
                fn(SchemaFieldType $field) => $field->isShownOnCreate()
            )->all()
        );
    }

    public function toUpdate(): EntrySchemaDefinition
    {
        return new EntrySchemaDefinition(
            Collection::make($this->fields)->filter(
                fn(SchemaFieldType $field) => $field->isShownOnUpdate()
            )->all()
        );
    }

    public function findField(string $name): ?SchemaFieldType
    {
        return Collection::make($this->fields)->first(
            fn(SchemaFieldType $field) => $field->getName() === $name
        );
    }

    protected function transformToArray(Collection $items): array
    {
        return Collection::make($items)->mapWithKeys(
            fn(SchemaFieldType $field) => [$field->getName() => $field->toArray()]
        )->all();
    }
}
