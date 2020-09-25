<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Types\Select;
use CbtechLtd\Fastlane\Fields\Value;

class SelectTransformer implements Transformer
{
    private Select $field;

    public function __construct(Select $field)
    {
        $this->field = $field;
    }

    public function get(EntryType $entryType, $value)
    {
        $value = $this->field->isMultiple()
            ? $value
            : [$value];

        return $this->field->getOptions()
            ->load()
            ->select($value)
            ->selected()
            ->values();
    }

    /**
     * @inheritDoc
     */
    public function set(EntryType $entryType, $value)
    {
        $value = $value->value()->map->getValue();

        if ($this->field->isMultiple()) {
            return $value->toJson();
        }

        return $value->first();
    }

    public function fromRequest(EntryType $entryType, $value): Value
    {
//        $value = $this->field->isMultiple()
//            ? $value
//            : [$value];
//
//        $selected = $this->field->getOptions()
//            ->load()
//            ->select($value)
//            ->selected()
//            ->values();
//
//        dd($selected);

        return new Value($entryType, $value);
    }
}
