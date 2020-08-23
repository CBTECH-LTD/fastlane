<?php

namespace CbtechLtd\Fastlane\Support\Eloquent\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;

interface LoadRelationshipsFromEntryType
{
    public function loadRelationsFromEntryType(EntryInstance $entryInstance): void;
}
