<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;

interface WriteValue
{
    public function writeValue(EntryInstance $entryInstance, $value, array $requestData): void;

    public function writeValueUsing($callback): self;

    /**
     * Exports an array to be used by the model to define
     * a fillable column and its casting type.
     *
     * The returned array must be in the following format:
     *  [
     *      "field_name" => string | null
     * ]
     *
     * @return array
     */
    public function toModelAttribute(): array;
}
