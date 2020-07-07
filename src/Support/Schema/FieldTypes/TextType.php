<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

class TextType extends BaseType
{
    public function getType(): string
    {
        return 'text';
    }

    protected function getTypeRules(): string
    {
        return 'string';
    }

    public function toMigration(): string
    {
        $base = "text('{$this->getName()}')";

        if (! $this->isRequired()) {
            $base = "{$base}->nullable()";
        }

        if ($this->hasUniqueRule()) {
            $base = "{$base}->unique()";
        }

        return $base;
    }
}
