<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;

class ToggleField extends AbstractBaseField implements Contracts\ExportsToApiAttribute
{
    use ExportsToApiAttribute;

    protected $default = true;
    protected int $listWidth = 80;

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
