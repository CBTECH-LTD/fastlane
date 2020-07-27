<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class TextField extends BaseSchemaField
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
