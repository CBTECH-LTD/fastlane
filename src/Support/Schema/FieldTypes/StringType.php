<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

class StringType extends BaseType
{
    protected $default = '';

    public function getType(): string
    {
        return 'string';
    }

    protected function getTypeRules(): string
    {
        return 'string';
    }
}
