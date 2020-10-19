<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\ValueResolver;

class StringTransformer implements Transformer
{
    /**
     * Transform the raw value to a augmented value.
     *
     * @param EntryType $entryType
     * @param           $value
     * @return mixed
     */
    public function get(EntryType $entryType, $value)
    {
        return $value;
    }

    /**
     * Transform the augmented value to a raw value.
     *
     * @param EntryType $entryType
     * @param           $value
     * @return mixed
     */
    public function set(EntryType $entryType, $value)
    {
        return $value;
    }

    public function fromRequest(EntryType $entryType, $value): ValueResolver
    {
        return new ValueResolver($entryType, $value);
    }
}
