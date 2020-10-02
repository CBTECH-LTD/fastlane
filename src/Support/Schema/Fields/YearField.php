<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class YearField extends AbstractBaseField
{
    public function getType(): string
    {
        return 'year';
    }

    public function toModelAttribute(): array
    {
        return [
            $this->getName() => "integer",
        ];
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'date_format:Y',
        ];
    }

    protected function getMigrationMethod(): array
    {
        return ['year'];
    }
}
