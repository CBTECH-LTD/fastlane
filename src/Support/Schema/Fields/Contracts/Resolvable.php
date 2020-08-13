<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Http\Request;

interface Resolvable
{
    /**
     * @param EntryTypeContract $entryType
     * @param Request           $request
     * @return SchemaField[]
     */
    public function resolve(EntryTypeContract $entryType, Request $request): array;
}
