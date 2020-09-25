<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Types\Numeric;
use CbtechLtd\Fastlane\Fields\Value;

class NumberTransformer implements Transformer
{
    private Numeric $field;

    /**
     * NumberTransformer constructor.
     *
     * @param Numeric $field
     */
    public function __construct(Numeric $field)
    {
        $this->field = $field;
    }

    public function get(EntryType $entryType, $value)
    {
        return (float)$value;
    }

    public function set(EntryType $entryType, $value)
    {
        return $value->value();
    }

    public function fromRequest(EntryType $entryType, $value): Value
    {
        return new Value($entryType, $value);
    }
}
