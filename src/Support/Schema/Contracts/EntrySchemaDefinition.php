<?php

namespace CbtechLtd\Fastlane\Support\Schema\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use Illuminate\Contracts\Support\Arrayable;

interface EntrySchemaDefinition extends Arrayable
{
    public function getFields(): array;

    public function toIndex(): EntrySchemaDefinition;

    public function toCreate(): EntrySchemaDefinition;

    public function toUpdate(): EntrySchemaDefinition;

    public function findField(string $name): ?SchemaFieldType;
}
