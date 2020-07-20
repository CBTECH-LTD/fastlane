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

    protected function getMigrationMethod(): array
    {
        return ['longText'];
    }
}
