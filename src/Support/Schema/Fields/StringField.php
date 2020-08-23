<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class StringField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected $createRules = 'max:255';
    protected $updateRules = 'max:255';

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

    public function minLength(int $length): self
    {
        return $this->setRule('min', $length);
    }

    public function maxLength(int $length): self
    {
        return $this->setRule('max', $length);
    }
}
