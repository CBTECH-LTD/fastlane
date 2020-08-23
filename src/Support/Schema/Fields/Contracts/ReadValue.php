<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldValue;

interface ReadValue
{
    public function readValue(EntryInstance $entryInstance): FieldValue;

    public function readValueUsing($callback): self;
}
