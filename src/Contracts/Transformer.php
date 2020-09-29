<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Contracts;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Value;

interface Transformer
{
    /**
     * Transform the raw value to a augmented value.
     *
     * @param EntryType $entryType
     * @param           $value
     * @return mixed
     */
    public function get(EntryType $entryType, $value);

    /**
     * Transform the augmented value to a raw value.
     *
     * @param EntryType $entryType
     * @param           $value
     * @return mixed
     */
    public function set(EntryType $entryType, $value);

    /**
     * Transform the value coming from the request.
     *
     * @param EntryType $entryType
     * @param Field     $field
     * @param           $value
     * @return mixed
     */
    public function toValueObject(EntryType $entryType, Field $field, $value): Value;
}
