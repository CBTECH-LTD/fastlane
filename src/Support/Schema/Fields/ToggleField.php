<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
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

    public function required(bool $required = true): self
    {
        return $this;
    }

    public function writeValue(EntryInstanceContract $entryInstance, $value, array $requestData): void
    {
        parent::writeValue($entryInstance, (bool)$value, $requestData);
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
