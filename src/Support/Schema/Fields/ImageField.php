<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class ImageField extends FileField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected int $listWidth = 150;

    protected array $accept = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    public function getType(): string
    {
        return 'image';
    }

    protected function buildFieldValueInstance(string $fieldName, $value): FieldValue
    {
        return new ImageFieldValue($fieldName, $value, $this->isMultiple());
    }
}
