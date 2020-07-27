<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class ToggleField extends BaseSchemaField
{
    protected $default = true;

    public function getType(): string
    {
        return 'toggle';
    }

    public function toModelAttribute(): array
    {
        return [
            $this->getName() => 'boolean',
        ];
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'boolean',
        ];
    }

    protected function getMigrationMethod(): array
    {
        return ['boolean'];
    }
}
