<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;

interface Resolvable
{
    /**
     * @param EntryTypeContract $entryType
     * @param array             $data
     * @return SchemaField[]
     */
    public function resolve(EntryTypeContract $entryType, array $data): array;
}
