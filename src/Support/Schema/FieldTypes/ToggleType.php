<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

class ToggleType extends BaseType
{
    public function getType(): string
    {
        return 'boolean';
    }

    protected function getTypeRules(): string
    {
        return 'boolean';
    }
}
