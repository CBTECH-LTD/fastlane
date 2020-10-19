<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;

class Hidden extends Field
{
    protected string $component = 'hidden';

    protected function getFieldRules(array $data, EntryType $entryType): array
    {
        return [
            $this->getAttribute() => 'string',
        ];
    }
}
