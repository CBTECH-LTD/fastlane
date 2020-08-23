<?php

namespace CbtechLtd\Fastlane\Support\Eloquent\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;

interface LoadAttributesFromEntryType
{
    public function loadAttributesFromEntryType(EntryInstance $entryInstance): void;
}
