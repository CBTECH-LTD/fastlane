<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;

interface Resolvable
{
    /**
     * @param EntryTypeContract $entryType
     * @param EntryRequest      $request
     * @return SchemaField[]
     */
    public function resolve(EntryTypeContract $entryType, EntryRequest $request): array;
}
