<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

class StringType extends BaseType
{
    public function getType(): string
    {
        return 'string';
    }

    public function toMigration(): string
    {
        $base = "string('{$this->getName()}')";

        if (! $this->isRequired()) {
            $base = "{$base}->nullable()";
        }

        if ($this->hasUniqueRule()) {
            $base = "{$base}->unique()";
        }

        return $base;
    }

    protected function getTypeRules(): string
    {
        return 'string';
    }
}
