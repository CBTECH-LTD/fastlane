<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

class ToggleType extends BaseType
{
    protected $default = true;

    public function getType(): string
    {
        return 'toggle';
    }

    public function toModelAttribute()
    {
        return [
            $this->getName() => 'boolean',
        ];
    }

    protected function getTypeRules(): string
    {
        return 'boolean';
    }

    protected function getMigrationMethod(): array
    {
        return ['boolean'];
    }
}
