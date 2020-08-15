<?php

namespace CbtechLtd\Fastlane\Support\Schema\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;

interface EntrySchema
{
    /**
     * @return SchemaField[]
     */
    public function getFields(): array;

    /**
     * @return SchemaField[]
     */
    public function getIndexFields(): array;

    /**
     * @return SchemaField[]
     */
    public function getCreateFields(): array;

    /**
     * @return SchemaField[]
     */
    public function getUpdateFields(): array;

    /**
     * @return FieldPanel[]
     */
    public function getPanels(): array;

    /**
     * @param string $name
     * @return SchemaField
     */
    public function findField(string $name): SchemaField;
}
