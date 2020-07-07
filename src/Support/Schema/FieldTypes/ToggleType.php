<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

class ToggleType extends BaseType
{
    public function getType(): string
    {
        return 'boolean';
    }

    public function toMigration(): string
    {
        $base = "boolean('{$this->getName()}')";

        if (! $this->isRequired()) {
            $base = "{$base}->nullable()";
        }

        return $base;
    }

    protected function getTypeRules(): string
    {
        return 'boolean';
    }
}
