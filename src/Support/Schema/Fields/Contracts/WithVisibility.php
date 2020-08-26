<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;

interface WithVisibility
{
    public function isShownOnIndex(EntryInstance $entryInstance): bool;

    public function isShownOnCreate(EntryInstance $entryInstance): bool;

    public function isShownOnUpdate(EntryInstance $entryInstance): bool;
}
