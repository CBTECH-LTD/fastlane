<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Value;

class BooleanTransformer implements Transformer
{
    /**
     * @inheritDoc
     */
    public function get(EntryType $entryType, $value)
    {
        return (bool)$value;
    }

    /**
     * @inheritDoc
     */
    public function set(EntryType $entryType, $value)
    {
        return $value->value();
    }

    public function toValueObject(EntryType $entryType, Field $field, $value): Value
    {
        return new Value($entryType, (bool)$value, $field);
    }
}
