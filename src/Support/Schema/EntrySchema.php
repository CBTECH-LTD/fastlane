<?php

namespace CbtechLtd\Fastlane\Support\Schema;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use Illuminate\Support\Collection;

abstract class EntrySchema implements Contracts\EntrySchema
{
    private EntryType $entryType;
    private EntrySchemaDefinition $definition;

    abstract protected function fields(): EntrySchemaDefinition;

    public function __construct(EntryType $entryType)
    {
        $this->entryType = $entryType;
        $this->definition = $this->build();
    }

    public function getDefinition(): EntrySchemaDefinition
    {
        return $this->definition;
    }

    private function build(): EntrySchemaDefinition
    {
        $fields = Collection::make($this->fields()->getFields())
            ->map(function (SchemaFieldType $field) {
                return $field->setEntryType($this->entryType);
            })->all();

        return EntrySchemaDefinition::make($fields);
    }
}
