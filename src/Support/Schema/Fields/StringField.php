<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class StringField extends BaseSchemaField
{
    public function getType(): string
    {
        return 'string';
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'string',
        ];
    }
}
