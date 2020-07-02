<?php

namespace CbtechLtd\Fastlane\Support\Schema\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface EntrySchemaDefinition extends Arrayable
{
    public function getFields(): array;

    public function toIndex(): EntrySchemaDefinition;

    public function toCreate(): EntrySchemaDefinition;

    public function toUpdate(): EntrySchemaDefinition;
}
