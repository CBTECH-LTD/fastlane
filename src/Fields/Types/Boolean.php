<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;

class Boolean extends Field
{
    protected string $component = 'toggle';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'default' => true,
        ]);
    }

    protected function getFieldRules(array $data, EntryType $entryType): array
    {
        return [$this->getAttribute() => 'boolean'];
    }

    protected function processReadValue($value)
    {
        return (bool)$value;
    }

    protected function processWriteValue($value)
    {
        return (bool)$value;
    }
}
