<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class HiddenField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    public function getType(): string
    {
        return 'hidden';
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'string',
        ];
    }
}
