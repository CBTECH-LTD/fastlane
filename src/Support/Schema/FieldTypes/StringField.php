<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class StringField extends BaseSchemaField
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
