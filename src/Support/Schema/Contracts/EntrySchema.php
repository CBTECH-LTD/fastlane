<?php

namespace CbtechLtd\Fastlane\Support\Schema\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;

interface EntrySchema
{
    /**
     * @return SchemaField[]
     */
    public function all(): array;

    /**
     * @return SchemaField[]
     */
    public function toIndex(): array;

    /**
     * @return SchemaField[]
     */
    public function toCreate(): array;

    /**
     * @return SchemaField[]
     */
    public function toUpdate(): array;

    public function findField(string $name): SchemaField;
}
